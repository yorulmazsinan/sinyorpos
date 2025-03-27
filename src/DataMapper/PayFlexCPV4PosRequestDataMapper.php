<?php
/**
 * @license MIT
 */

namespace SinyorPos\DataMapper;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\PayFlexAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Exceptions\NotImplementedException;
use SinyorPos\Gateways\AbstractGateway;

/**
 * Creates request data for PayFlex Common Payment V4 Gateway requests
 */
class PayFlexCPV4PosRequestDataMapper extends AbstractRequestDataMapperCrypt
{
    /** @var string */
    public const CREDIT_CARD_EXP_DATE_LONG_FORMAT = 'Ym';

    /**
     * {@inheritDoc}
     */
    protected $txTypeMappings = [
        AbstractGateway::TX_PAY      => 'Sale',
        AbstractGateway::TX_PRE_PAY  => 'Auth',
        AbstractGateway::TX_POST_PAY => 'Capture',
        AbstractGateway::TX_CANCEL   => 'Cancel',
        AbstractGateway::TX_REFUND   => 'Refund',
        AbstractGateway::TX_HISTORY  => 'TxnHistory',
        AbstractGateway::TX_STATUS   => 'OrderInquiry',
    ];

    /**
     * {@inheritdoc}
     */
    protected $cardTypeMapping = [
        AbstractCreditCard::CARD_TYPE_VISA       => '100',
        AbstractCreditCard::CARD_TYPE_MASTERCARD => '200',
        AbstractCreditCard::CARD_TYPE_TROY       => '300',
        AbstractCreditCard::CARD_TYPE_AMEX       => '400',
    ];

    /**
     * {@inheritdoc}
     */
    protected $langMappings = [
        AbstractGateway::LANG_TR => 'tr-TR',
        AbstractGateway::LANG_EN => 'en-US',
    ];

    /**
     * {@inheritdoc}
     */
    protected $recurringOrderFrequencyMapping = [
        'DAY'   => 'Day',
        'MONTH' => 'Month',
        'YEAR'  => 'Year',
    ];

    /**
     * todo implement
     * {@inheritDoc}
     *
     * @param PayFlexAccount $account
     */
    public function create3DPaymentRequestData(AbstractPosAccount $account, $order, string $txType, array $responseData, ?AbstractCreditCard $card = null): array
    {
        throw new NotImplementedException();
    }


    /**
     * @param PayFlexAccount                                     $account
     * @param array{TransactionId: string, PaymentToken: string} $responseData
     *
     * @return array{HostMerchantId: string, Password: string, TransactionId: string, PaymentToken: string}
     */
    public function create3DPaymentStatusRequestData(AbstractPosAccount $account, array $responseData): array
    {
        return $this->getRequestAccountData($account) + [
                'HostMerchantId' => $account->getClientId(),
                'Password'       => $account->getPassword(),
                'TransactionId'  => $responseData['TransactionId'],
                'PaymentToken'   => $responseData['PaymentToken'],
            ];
    }

    /**
     * @param PayFlexAccount          $account
     * @param object                  $order
     * @param AbstractGateway::TX_*   $txType
     * @param AbstractCreditCard|null $card
     *
     * @return array<string, string>
     */
    public function create3DEnrollmentCheckRequestData(AbstractPosAccount $account, $order, string $txType, ?AbstractCreditCard $card = null): array
    {
        $mappedOrder             = (array) $order;
        $mappedOrder['currency'] = $this->mapCurrency($order->currency);
        $mappedOrder['amount']   = self::amountFormat($order->amount);
        $hashData                = $this->crypt->create3DHash($account, $mappedOrder, $txType);

        $requestData = [
            'HostMerchantId'       => $account->getClientId(),
            'MerchantPassword'     => $account->getPassword(),
            'HostTerminalId'       => $account->getTerminalId(),
            'TransactionType'      => $this->mapTxType($txType),
            'AmountCode'           => $this->mapCurrency($order->currency),
            'Amount'               => self::amountFormat($order->amount),
            'OrderID'              => (string) $order->id,
            'OrderDescription'     => (string) ($order->description ?? null),
            'IsSecure'             => 'true', // Işlemin 3D yapılıp yapılmayacağına dair flag, alabileceği değerler: 'true', 'false'
            /**
             * 3D Programına Dahil Olmayan Kartlar ile İşlem Yapma Flagi: "3D İşlem Flagi" (IsSecure) "true" gönderilmiş
             * işlemler için bir alt seçenektir. Kart sahibi "3D Secure" programına dahil değilse Ortak Ödemenin işlemi
             * Sanal Pos'a gönderip göndermeyeceğini belirtir. "true" gönderilmesi durumunda kart sahibi
             * 3D Secure programına dahil olmasa bile işlemi Sanal Pos'a gönderecektir.
             * Bu tür işlemler "Half Secure" olarak işaretlenecektir.
             */
            'AllowNotEnrolledCard' => 'false',
            'SuccessUrl'           => (string) $order->success_url,
            'FailUrl'              => (string) $order->fail_url,
            'HashedData'           => $hashData,
            'RequestLanguage'      => $this->getLang($account, $order),
            /**
             * Bu alanda gönderilecek değer kart hamili
             * ektresinde işlem açıklamasında çıkacaktır.
             * (Abone no vb. bilgiler gönderilebilir)
             */
            'Extract'              => '',
            /**
             * Uye işyeri tarafından işleme ait ek bilgiler varsa CustomItems alanında gönderilir.
             * İçeriğinde "name" ve "value" attirbutelarını barındırır.
             * Örnek: İsim1:Değer1 İsim2:Değer2 İsim3:Değer3
             */
            'CustomItems'          => '',
        ];

        if ($card instanceof AbstractCreditCard) {
            $requestData += [
                'BrandNumber'     => $this->cardTypeMapping[$card->getType()],
                'CVV'             => $card->getCvv(),
                'PAN'             => $card->getNumber(),
                'ExpireMonth'     => $card->getExpireMonth(),
                'ExpireYear'      => $card->getExpireYear(),
                'CardHoldersName' => (string) $card->getHolderName(),
            ];
        }

        if ($order->installment) {
            $requestData['InstallmentCount'] = $this->mapInstallment($order->installment);
        }

        return $requestData;
    }

    /**
     * TODO implement
     * {@inheritDoc}
     *
     * @param PayFlexAccount $account
     *
     * @return array<string, string>
     */
    public function createNonSecurePaymentRequestData(AbstractPosAccount $account, $order, string $txType, ?AbstractCreditCard $card = null): array
    {
        $requestData = $this->getRequestAccountData($account) + [
                'TransactionType'         => $this->mapTxType($txType),
                'OrderId'                 => (string) $order->id,
                'CurrencyAmount'          => self::amountFormat($order->amount),
                'CurrencyCode'            => $this->mapCurrency($order->currency),
                'ClientIp'                => (string) $order->ip,
                'TransactionDeviceSource' => '0',
            ];

        if ($card instanceof AbstractCreditCard) {
            $requestData['Pan']    = $card->getNumber();
            $requestData['Expiry'] = $card->getExpirationDate(self::CREDIT_CARD_EXP_DATE_LONG_FORMAT);
            $requestData['Cvv']    = $card->getCvv();
        }

        return $requestData;
    }

    /**
     * @param PayFlexAccount          $account
     * @param                         $order
     * @param AbstractCreditCard|null $card
     *
     * @return array{TransactionType: string, ReferenceTransactionId: string,
     *     CurrencyAmount: string, CurrencyCode: string, ClientIp: string,
     *     MerchantId: string, Password: string}
     */
    public function createNonSecurePostAuthPaymentRequestData(AbstractPosAccount $account, $order, ?AbstractCreditCard $card = null): array
    {
        return $this->getRequestAccountData($account) + [
                'TransactionType'        => $this->mapTxType(AbstractGateway::TX_POST_PAY),
                'ReferenceTransactionId' => (string) $order->id,
                'CurrencyAmount'         => self::amountFormat($order->amount),
                'CurrencyCode'           => $this->mapCurrency($order->currency),
                'ClientIp'               => (string) $order->ip,
            ];
    }

    /**
     * {@inheritDoc}
     */
    public function createStatusRequestData(AbstractPosAccount $account, $order): array
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param PayFlexAccount $account
     *
     * @return array{MerchantId: string, Password: string, TransactionType: string, ReferenceTransactionId: string,
     *     ClientIp: string}
     */
    public function createCancelRequestData(AbstractPosAccount $account, $order): array
    {
        return $this->getRequestAccountData($account) + [
                'TransactionType'        => $this->mapTxType(AbstractGateway::TX_CANCEL),
                'ReferenceTransactionId' => (string) $order->id,
                'ClientIp'               => (string) $order->ip,
            ];
    }

    /**
     * {@inheritDoc}
     *
     * @param PayFlexAccount $account
     *
     * @return array{MerchantId: string, Password: string, TransactionType: string, ReferenceTransactionId: string,
     *     ClientIp: string, CurrencyAmount: string}
     */
    public function createRefundRequestData(AbstractPosAccount $account, $order): array
    {
        return $this->getRequestAccountData($account) + [
            'TransactionType'        => $this->mapTxType(AbstractGateway::TX_REFUND),
            'ReferenceTransactionId' => (string) $order->id,
            'ClientIp'               => (string) $order->ip,
            'CurrencyAmount'         => self::amountFormat($order->amount),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createHistoryRequestData(AbstractPosAccount $account, $order, array $extraData = []): array
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param array{CommonPaymentUrl: string, PaymentToken: string} $extraData
     *
     * @return array{gateway: string, method: 'GET', inputs: array{Ptkn: string}}
     */
    public function create3DFormData(
        ?AbstractPosAccount  $account,
                             $order,
        ?string              $txType,
        ?string              $gatewayURL,
        ?AbstractCreditCard  $card = null,
        array                $extraData = []): array
    {
        return [
            'gateway' => $extraData['CommonPaymentUrl'],
            'method'  => 'GET',
            'inputs'  => [
                'Ptkn' => $extraData['PaymentToken'],
            ],
        ];
    }

    /**
     * Amount Formatter
     *
     * @param float $amount
     *
     * @return string ex: 10.1 => 10.10
     */
    public static function amountFormat(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    public function mapInstallment(?int $installment): string
    {
        return $installment > 1 ? (string) $installment : '0';
    }

    /**
     * @param PayFlexAccount $account
     *
     * @return array{MerchantId: string, Password: string}
     */
    private function getRequestAccountData(AbstractPosAccount $account): array
    {
        return [
            'MerchantId' => $account->getClientId(),
            'Password'   => $account->getPassword(),
        ];
    }
}
