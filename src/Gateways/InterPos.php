<?php
/**
 * @license MIT
 */

namespace SinyorPos\Gateways;

use InvalidArgumentException;
use SinyorPos\DataMapper\RequestDataMapper\InterPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\RequestDataMapperInterface;
use SinyorPos\DataMapper\ResponseDataMapper\InterPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\ResponseDataMapperInterface;
use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\InterPosAccount;
use SinyorPos\Entity\Card\CreditCardInterface;
use SinyorPos\Event\RequestDataPreparedEvent;
use SinyorPos\Exceptions\HashMismatchException;
use SinyorPos\Exceptions\UnsupportedTransactionTypeException;
use SinyorPos\PosInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deniz bankin desteklidigi Gateway
 * Class InterPos
 */
class InterPos extends AbstractGateway
{
    /** @var string */
    public const NAME = 'InterPos';

    /** @var InterPosAccount */
    protected AbstractPosAccount $account;

    /** @var InterPosRequestDataMapper */
    protected RequestDataMapperInterface $requestDataMapper;

    /** @var InterPosResponseDataMapper */
    protected ResponseDataMapperInterface $responseDataMapper;

    /** @inheritdoc */
    protected static array $supportedTransactions = [
        PosInterface::TX_TYPE_PAY_AUTH       => [
            PosInterface::MODEL_3D_SECURE,
            PosInterface::MODEL_3D_PAY,
            PosInterface::MODEL_3D_HOST,
            PosInterface::MODEL_NON_SECURE,
        ],
        PosInterface::TX_TYPE_PAY_PRE_AUTH   => true,
        PosInterface::TX_TYPE_PAY_POST_AUTH  => true,
        PosInterface::TX_TYPE_STATUS         => true,
        PosInterface::TX_TYPE_CANCEL         => true,
        PosInterface::TX_TYPE_REFUND         => true,
        PosInterface::TX_TYPE_REFUND_PARTIAL => true,
        PosInterface::TX_TYPE_HISTORY        => false,
        PosInterface::TX_TYPE_ORDER_HISTORY  => false,
        PosInterface::TX_TYPE_CUSTOM_QUERY   => true,
    ];

    /** @return InterPosAccount */
    public function getAccount(): AbstractPosAccount
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function make3DPayment(Request $request, array $order, string $txType, CreditCardInterface $creditCard = null): PosInterface
    {
        $request = $request->request;
        /** @var array{"3DStatus": string, MD: string, PayerTxnId: string, Eci: string, PayerAuthenticationCode: string} $gatewayResponse */
        $gatewayResponse = $request->all();

        if (!$this->is3DAuthSuccess($gatewayResponse)) {
            $this->response = $this->responseDataMapper->map3DPaymentData($gatewayResponse, null, $txType, $order);

            return $this;
        }

        if (!$this->requestDataMapper->getCrypt()->check3DHash($this->account, $gatewayResponse)) {
            throw new HashMismatchException();
        }

        $this->logger->debug('finishing payment');

        $requestData  = $this->requestDataMapper->create3DPaymentRequestData($this->account, $order, $txType, $gatewayResponse);

        $event = new RequestDataPreparedEvent(
            $requestData,
            $this->account->getBank(),
            $txType,
            \get_class($this),
            $order,
            PosInterface::MODEL_3D_SECURE
        );
        /** @var RequestDataPreparedEvent $event */
        $event = $this->eventDispatcher->dispatch($event);
        if ($requestData !== $event->getRequestData()) {
            $this->logger->debug('Request data is changed via listeners', [
                'txType'      => $event->getTxType(),
                'bank'        => $event->getBank(),
                'initialData' => $requestData,
                'updatedData' => $event->getRequestData(),
            ]);
            $requestData = $event->getRequestData();
        }

        $contents     = $this->serializer->encode($requestData, $txType);
        $bankResponse = $this->send(
            $contents,
            $txType,
            PosInterface::MODEL_3D_SECURE,
            $this->getApiURL()
        );

        $this->response = $this->responseDataMapper->map3DPaymentData($gatewayResponse, $bankResponse, $txType, $order);
        $this->logger->debug('finished 3D payment', ['mapped_response' => $this->response]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function make3DPayPayment(Request $request, array $order, string $txType): PosInterface
    {
        $this->response = $this->responseDataMapper->map3DPayResponseData($request->request->all(), $txType, $order);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function make3DHostPayment(Request $request, array $order, string $txType): PosInterface
    {
        $this->response = $this->responseDataMapper->map3DHostResponseData($request->request->all(), $txType, $order);

        return $this;
    }

    /**
     * Deniz bank dokumantasyonunda history sorgusu ile alakali hic bir bilgi yok
     * @inheritDoc
     */
    public function history(array $data): PosInterface
    {
        throw new UnsupportedTransactionTypeException();
    }

    /**
     * @inheritDoc
     */
    public function orderHistory(array $order): PosInterface
    {
        throw new UnsupportedTransactionTypeException();
    }

    /**
     * @inheritDoc
     */
    public function get3DFormData(array $order, string $paymentModel, string $txType, CreditCardInterface $creditCard = null): array
    {
        $this->check3DFormInputs($paymentModel, $txType, $creditCard);

        $this->logger->debug('preparing 3D form data');

        return $this->requestDataMapper->create3DFormData(
            $this->account,
            $order,
            $paymentModel,
            $txType,
            $this->get3DGatewayURL($paymentModel),
            $creditCard
        );
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    protected function send($contents, string $txType, string $paymentModel, string $url): array
    {
        $this->logger->debug('sending request', ['url' => $url]);
        if (!\is_array($contents)) {
            throw new InvalidArgumentException(\sprintf('Argument type must be array, %s provided.', \gettype($contents)));
        }

        $response = $this->client->post($url, ['form_params' => $contents]);
        $this->logger->debug('request completed', ['status_code' => $response->getStatusCode()]);

        return $this->data = $this->serializer->decode($response->getBody()->getContents(), $txType);
    }
}
