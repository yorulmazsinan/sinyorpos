<?php
/**
 * @license MIT
 */

namespace SinyorPos\Gateways;

use SinyorPos\DataMapper\RequestDataMapper\PayForPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\RequestDataMapperInterface;
use SinyorPos\DataMapper\ResponseDataMapper\PayForPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\ResponseDataMapperInterface;
use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\PayForAccount;
use SinyorPos\Entity\Card\CreditCardInterface;
use SinyorPos\Event\RequestDataPreparedEvent;
use SinyorPos\Exceptions\HashMismatchException;
use SinyorPos\PosInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PayForPos
 */
class PayForPos extends AbstractGateway
{
    /** @var string */
    public const NAME = 'PayForPOS';

    /** @var PayForAccount */
    protected AbstractPosAccount $account;

    /** @var PayForPosRequestDataMapper */
    protected RequestDataMapperInterface$requestDataMapper;

    /** @var PayForPosResponseDataMapper */
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
        PosInterface::TX_TYPE_HISTORY        => true,
        PosInterface::TX_TYPE_ORDER_HISTORY  => true,
        PosInterface::TX_TYPE_CUSTOM_QUERY   => true,
    ];

    /** @return PayForAccount */
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

        if (!$this->is3DAuthSuccess($request->all())) {
            $this->response = $this->responseDataMapper->map3DPaymentData($request->all(), null, $txType, $order);

            return $this;
        }

        if (!$this->requestDataMapper->getCrypt()->check3DHash($this->account, $request->all())) {
            throw new HashMismatchException();
        }

        // valid ProcReturnCode is V033 in case of success 3D Authentication
        $requestData = $this->requestDataMapper->create3DPaymentRequestData($this->account, $order, $txType, $request->all());

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

        $this->response = $this->responseDataMapper->map3DPaymentData($request->all(), $bankResponse, $txType, $order);

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
     * Satış işlemi ile farklı batchtlerde olmalıdır.
     *
     * @inheritDoc
     */
    public function refund(array $order): PosInterface
    {
        return parent::refund($order);
    }

    /**
     * Fetches Transaction history (both failed and successful) for the given date ReqDate
     * Note: history request to gateway returns JSON response
     * @inheritDoc
     */
    public function history(array $data): PosInterface
    {
        return parent::history($data);
    }

    /**
     * Fetches transaction history (both failed and successful, refund|pre|post|cancel) related to the queried order
     * Note: history request to gateway returns JSON response
     * @inheritDoc
     */
    public function orderHistory(array $order): PosInterface
    {
        return parent::orderHistory($order);
    }


    /**
     * {@inheritDoc}
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
        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF-8',
            ],
            'body'    => $contents,
        ]);
        $this->logger->debug('request completed', ['status_code' => $response->getStatusCode()]);

        return $this->data = $this->serializer->decode($response->getBody()->getContents(), $txType);
    }
}
