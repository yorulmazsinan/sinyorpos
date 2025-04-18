<?php
/**
 * @license MIT
 */

namespace SinyorPos\Gateways;

use SinyorPos\DataMapper\RequestDataMapper\PayFlexCPV4PosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\RequestDataMapperInterface;
use SinyorPos\DataMapper\ResponseDataMapper\PayFlexCPV4PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\ResponseDataMapperInterface;
use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\PayFlexAccount;
use SinyorPos\Entity\Card\CreditCardInterface;
use SinyorPos\Event\RequestDataPreparedEvent;
use SinyorPos\Exceptions\UnsupportedPaymentModelException;
use SinyorPos\Exceptions\UnsupportedTransactionTypeException;
use SinyorPos\PosInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * PayFlex Common Payment (Ortak Ödeme) ISD v4.0
 * Dokumanlar: http://sanalpos.innova.com.tr/
 */
class PayFlexCPV4Pos extends AbstractGateway
{
    /** @var string */
    public const NAME = 'PayFlex-Common-Payment-V4';

    /** @var PayFlexAccount */
    protected AbstractPosAccount $account;

    /** @var PayFlexCPV4PosRequestDataMapper */
    protected RequestDataMapperInterface $requestDataMapper;

    /** @var PayFlexCPV4PosResponseDataMapper */
    protected ResponseDataMapperInterface $responseDataMapper;

    /** @inheritdoc */
    protected static array $supportedTransactions = [
        PosInterface::TX_TYPE_PAY_AUTH       => [
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
        PosInterface::TX_TYPE_HISTORY        => false,
        PosInterface::TX_TYPE_ORDER_HISTORY  => false,
        PosInterface::TX_TYPE_CUSTOM_QUERY   => true,
    ];

    /** @return PayFlexAccount */
    public function getAccount(): AbstractPosAccount
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function make3DPayment(Request $request, array $order, string $txType, CreditCardInterface $creditCard = null): PosInterface
    {
        throw new UnsupportedPaymentModelException();
    }

    /**
     * @inheritDoc
     *
     * @throws ClientExceptionInterface
     */
    public function make3DPayPayment(Request $request, array $order, string $txType): PosInterface
    {
        $resultCode = $request->query->get('Rc');
        if (null !== $resultCode && $this->responseDataMapper::PROCEDURE_SUCCESS_CODE !== $resultCode) {
            $this->logger->error('received error response from the bank', $request->query->all());
            $this->response = $this->responseDataMapper->map3DPayResponseData($request->query->all(), $txType, $order);

            return $this;
        }

        /** @var array{TransactionId: string, PaymentToken: string} $queryParams */
        $queryParams = $request->query->all();

        // Burda odemenin basarili olup olmadigini sorguluyoruz.
        $requestData = $this->requestDataMapper->create3DPaymentStatusRequestData($this->account, $queryParams);

        $event = new RequestDataPreparedEvent(
            $requestData,
            $this->account->getBank(),
            $txType,
            \get_class($this),
            $order,
            PosInterface::MODEL_3D_PAY
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

        /**
         * sending request to make sure that payment was successful
         * @var array{ErrorCode: string}|array{
         *     Rc: string,
         *     AuthCode: string,
         *     TransactionId: string,
         *     PaymentToken: string,
         *     MaskedPan: string}|array{
         *     Rc: string,
         *     Message: string,
         *     TransactionId: string,
         *     PaymentToken: string} $bankResponse
         */
        $bankResponse = $this->send(
            $requestData,
            PosInterface::TX_TYPE_PAY_AUTH,
            PosInterface::MODEL_3D_SECURE,
            $this->getQueryAPIUrl()
        );

        $this->response = $this->responseDataMapper->map3DPayResponseData($bankResponse, $txType, $order);

        $this->logger->debug('finished 3D payment', ['mapped_response' => $this->response]);

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientExceptionInterface
     */
    public function make3DHostPayment(Request $request, array $order, string $txType): PosInterface
    {
        return $this->make3DPayPayment($request, $order, $txType);
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
     * {@inheritDoc}
     */
    public function get3DFormData(array $order, string $paymentModel, string $txType, CreditCardInterface $creditCard = null): array
    {
        $this->check3DFormInputs($paymentModel, $txType, $creditCard);

        /** @var array{CommonPaymentUrl: string|null, PaymentToken: string|null, ErrorCode: string|null, ResponseMessage: string|null} $data */
        $data = $this->registerPayment($order, $txType, $paymentModel, $creditCard);

        if (null !== $data['ErrorCode']) {
            $this->logger->error('payment register fail response', $data);
            throw new \RuntimeException('İşlem gerçekleştirilemiyor');
        }

        $this->logger->debug('preparing 3D form data');

        return $this->requestDataMapper->create3DFormData(
            null,
            [],
            null,
            null,
            null,
            null,
            $data
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

        $isXML = \is_string($contents);
        $body  = $isXML ? ['body' => $contents] : ['form_params' => $contents];

        $response = $this->client->post($url, $body);
        $this->logger->debug('request completed', ['status_code' => $response->getStatusCode()]);

        $responseContent = $response->getBody()->getContents();

        return $this->data = $this->serializer->decode($responseContent, $txType);
    }

    /**
     *
     * ORTAK ÖDEME SİSTEMİNE İŞLEM KAYDETME
     *
     * @phpstan-param PosInterface::TX_TYPE_PAY_AUTH|PosInterface::TX_TYPE_PAY_PRE_AUTH $txType
     * @phpstan-param PosInterface::MODEL_3D_*                                          $paymentModel
     *
     * @param array<string, int|string|float|null> $order
     * @param string                               $txType
     * @param string                               $paymentModel
     * @param CreditCardInterface|null             $creditCard
     *
     * Basarili durumda donen cevap formati: array{CommonPaymentUrl: string, PaymentToken: string, ErrorCode: null,
     * ResponseMessage: null} Basarisiz durumda donen cevap formati: array{CommonPaymentUrl: null, PaymentToken: null,
     * ErrorCode: string, ResponseMessage: string}
     *
     * @return array{CommonPaymentUrl: string|null, PaymentToken: string|null, ErrorCode: string|null, ResponseMessage: string|null}
     *
     * @throws UnsupportedTransactionTypeException
     * @throws ClientExceptionInterface
     */
    private function registerPayment(array $order, string $txType, string $paymentModel, CreditCardInterface $creditCard = null): array
    {
        $requestData = $this->requestDataMapper->create3DEnrollmentCheckRequestData(
            $this->account,
            $order,
            $txType,
            $paymentModel,
            $creditCard
        );

        $event = new RequestDataPreparedEvent(
            $requestData,
            $this->account->getBank(),
            $txType,
            \get_class($this),
            $order,
            $paymentModel
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

        /** @var array{CommonPaymentUrl: string|null, PaymentToken: string|null, ErrorCode: string|null, ResponseMessage: string|null} $response */
        $response = $this->send(
            $requestData,
            $txType,
            $paymentModel,
            $this->getApiURL()
        );

        return $response;
    }
}
