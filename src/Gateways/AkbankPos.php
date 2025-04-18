<?php
/**
 * @license MIT
 */

namespace SinyorPos\Gateways;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\AkbankPosAccount;
use SinyorPos\Entity\Card\CreditCardInterface;
use SinyorPos\Event\RequestDataPreparedEvent;
use SinyorPos\Exceptions\HashMismatchException;
use SinyorPos\Exceptions\UnsupportedTransactionTypeException;
use SinyorPos\PosInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @link https://sanalpos-prep.akbank.com/#entry
 */
class AkbankPos extends AbstractGateway
{
    /** @var string */
    public const NAME = 'AkbankPos';

    /** @var AkbankPosAccount */
    protected AbstractPosAccount $account;

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
        PosInterface::TX_TYPE_STATUS         => false,
        PosInterface::TX_TYPE_CANCEL         => true,
        PosInterface::TX_TYPE_REFUND         => true,
        PosInterface::TX_TYPE_REFUND_PARTIAL => true,
        PosInterface::TX_TYPE_ORDER_HISTORY  => true,
        PosInterface::TX_TYPE_HISTORY        => true,
        PosInterface::TX_TYPE_CUSTOM_QUERY   => true,
    ];

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException when transaction type is not provided
     */
    public function getApiURL(string $txType = null, string $paymentModel = null, ?string $orderTxType = null): string
    {
        if (null !== $txType) {
            return parent::getApiURL().'/'.$this->getRequestURIByTransactionType($txType);
        }

        throw new \InvalidArgumentException('Transaction type is required to generate API URL');
    }

    /** @return AkbankPosAccount */
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
            $this->response = $this->responseDataMapper->map3DPaymentData(
                $request->all(),
                null,
                $txType,
                $order
            );

            return $this;
        }

        if (!$this->requestDataMapper->getCrypt()->check3DHash($this->account, $request->all())) {
            throw new HashMismatchException();
        }

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

        $contents          = $this->serializer->encode($requestData, $txType);
        $provisionResponse = $this->send(
            $contents,
            $txType,
            PosInterface::MODEL_3D_SECURE,
            $this->getApiURL($txType)
        );

        $this->response = $this->responseDataMapper->map3DPaymentData(
            $request->all(),
            $provisionResponse,
            $txType,
            $order
        );
        $this->logger->debug('finished 3D payment', ['mapped_response' => $this->response]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function make3DPayPayment(Request $request, array $order, string $txType): PosInterface
    {
        if (!$this->requestDataMapper->getCrypt()->check3DHash($this->account, $request->request->all())) {
            throw new HashMismatchException();
        }

        $this->response = $this->responseDataMapper->map3DPayResponseData($request->request->all(), $txType, $order);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function make3DHostPayment(Request $request, array $order, string $txType): PosInterface
    {
        if (!$this->requestDataMapper->getCrypt()->check3DHash($this->account, $request->request->all())) {
            throw new HashMismatchException();
        }

        $this->response = $this->responseDataMapper->map3DHostResponseData($request->request->all(), $txType, $order);

        return $this;
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
     */
    public function status(array $order): PosInterface
    {
        throw new UnsupportedTransactionTypeException();
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     *
     * @throws \RuntimeException thrown when we get HTTP 400 error
     */
    protected function send($contents, string $txType, string $paymentModel, string $url): array
    {
        $this->logger->debug('sending request', ['url' => $url]);
        if (!\is_string($contents)) {
            throw new \InvalidArgumentException(\sprintf('Argument type must be string, %s provided.', \gettype($contents)));
        }

        $hash = $this->requestDataMapper->getCrypt()->hashString($contents, $this->account->getStoreKey());

        $response = $this->client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'auth-hash'    => $hash,
            ],
            'body'    => $contents,
        ]);

        if ($response->getStatusCode() === 400) {
            $this->logger->error('api error', ['status_code' => $response->getStatusCode()]);

            // when the data is sent fails validation checks we get 400 error
            $data = $this->serializer->decode($response->getBody()->getContents(), $txType);
            throw new \RuntimeException($data['message'], $data['code']);
        }

        $this->logger->debug('request completed', ['status_code' => $response->getStatusCode()]);

        return $this->data = $this->serializer->decode($response->getBody()->getContents(), $txType);
    }

    /**
     * @phpstan-param PosInterface::TX_TYPE_* $txType
     *
     * @param string $txType
     *
     * @return string
     */
    private function getRequestURIByTransactionType(string $txType): string
    {
        $arr = [
            PosInterface::TX_TYPE_HISTORY => 'portal/report/transaction',
        ];

        return $arr[$txType] ?? 'transaction/process';
    }
}
