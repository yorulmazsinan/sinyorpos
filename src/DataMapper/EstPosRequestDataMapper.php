<?php
/**
 * @license MIT
 */
namespace SinyorPos\DataMapper;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Gateways\AbstractGateway;

/**
 * Creates request data for EstPos Gateway requests
 */
class EstPosRequestDataMapper extends AbstractRequestDataMapperCrypt
{
    /** @var string */
    public const CREDIT_CARD_EXP_DATE_FORMAT = 'm/y';

    /** @var string */
    public const CREDIT_CARD_EXP_MONTH_FORMAT = 'm';

    /** @var string */
    public const CREDIT_CARD_EXP_YEAR_FORMAT = 'y';

    /**
     * {@inheritDoc}
     */
    protected $txTypeMappings = [
        AbstractGateway::TX_PAY      => 'Auth',
        AbstractGateway::TX_PRE_PAY  => 'PreAuth',
        AbstractGateway::TX_POST_PAY => 'PostAuth',
        AbstractGateway::TX_CANCEL   => 'Void',
        AbstractGateway::TX_REFUND   => 'Credit',
        AbstractGateway::TX_STATUS   => 'ORDERSTATUS',
        AbstractGateway::TX_HISTORY  => 'ORDERHISTORY',
    ];

    /**
     * {@inheritdoc}
     */
    protected $cardTypeMapping = [
        AbstractCreditCard::CARD_TYPE_VISA       => '1',
        AbstractCreditCard::CARD_TYPE_MASTERCARD => '2',
    ];

    /**
     * {@inheritdoc}
     */
    protected $recurringOrderFrequencyMapping = [
        'DAY'   => 'D',
        'WEEK'  => 'W',
        'MONTH' => 'M',
        'YEAR'  => 'Y',
    ];

    /**
     * {@inheritdoc}
     */
    protected $secureTypeMappings = [
        AbstractGateway::MODEL_3D_SECURE        => '3d',
        AbstractGateway::MODEL_3D_PAY           => '3d_pay',
        AbstractGateway::MODEL_3D_PAY_HOSTING   => '3d_pay_hosting',
        AbstractGateway::MODEL_3D_HOST          => '3d_host',
        AbstractGateway::MODEL_NON_SECURE       => 'regular',
    ];

    /**
     * {@inheritDoc}
     *
     * @param array{md: string, xid: string, eci: string, cavv: string} $responseData
     */
    public function create3DPaymentRequestData(AbstractPosAccount $account, $order, string $txType, array $responseData): array
    {
        $requestData = $this->getRequestAccountData($account) + [
            'Type'                    => $this->mapTxType($txType),
            'IPAddress'               => (string) ($order->ip ?? ''),
            'Email'                   => (string) $order->email,
            'OrderId'                 => (string) $order->id,
            'UserId'                  => (string) ($order->user_id ?? ''),
            'Total'                   => (string) $order->amount,
            'Currency'                => $this->mapCurrency($order->currency),
            'Taksit'                  => $this->mapInstallment($order->installment),
            'Number'                  => $responseData['md'],
            'PayerTxnId'              => $responseData['xid'],
            'PayerSecurityLevel'      => $responseData['eci'],
            'PayerAuthenticationCode' => $responseData['cavv'],
            'Mode'                    => 'P',
        ];

        if ($order->name) {
            $requestData['BillTo'] = [
                'Name' => (string) $order->name,
            ];
        }

        if (isset($order->recurringFrequency)) {
            $requestData += $this->getRecurringRequestOrderData($order);
        }

        return  $requestData;
    }

    /**
     * {@inheritDoc}
     * @return array{PbOrder?: array{OrderType: string, OrderFrequencyInterval: string, OrderFrequencyCycle: string, TotalNumberPayments: string}, Type: string, IPAddress: mixed, Email: mixed, OrderId: mixed, UserId: mixed, Total: mixed, Currency: string, Taksit: string, Number: string, Expires: string, Cvv2Val: string, Mode: string, BillTo: array{Name: mixed}, Name: string, Password: string, ClientId: string}
     */
    public function createNonSecurePaymentRequestData(AbstractPosAccount $account, $order, string $txType, ?AbstractCreditCard $card = null): array
    {
        $requestData =  $this->getRequestAccountData($account) + [
            'Type'      => $this->mapTxType($txType),
            'IPAddress' => (string) ($order->ip ?? ''),
            'Email'     => (string) $order->email,
            'OrderId'   => (string) $order->id,
            'UserId'    => (string) ($order->user_id ?? ''),
            'Total'     => (string) $order->amount,
            'Currency'  => $this->mapCurrency($order->currency),
            'Taksit'    => $this->mapInstallment($order->installment),
            'Number'    => $card->getNumber(),
            'Expires'   => $card->getExpirationDate(self::CREDIT_CARD_EXP_DATE_FORMAT),
            'Cvv2Val'   => $card->getCvv(),
            'Mode'      => 'P',
            'BillTo'    => [
                'Name' => (string) ($order->name ?? ''),
            ],
        ];

        if (isset($order->recurringFrequency)) {
            $requestData += $this->getRecurringRequestOrderData($order);
        }

        return $requestData;
    }

    /**
     * {@inheritDoc}
     *
     * @return array{Type: string, OrderId: string, Name: string, Password: string, ClientId: string}
     */
    public function createNonSecurePostAuthPaymentRequestData(AbstractPosAccount $account, $order, ?AbstractCreditCard $card = null): array
    {
        return $this->getRequestAccountData($account) + [
            'Type'     => $this->mapTxType(AbstractGateway::TX_POST_PAY),
            'OrderId'  => (string) $order->id,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createStatusRequestData(AbstractPosAccount $account, $order): array
    {
        $statusRequestData = $this->getRequestAccountData($account) + [
            'Extra' => [
                $this->mapTxType(AbstractGateway::TX_STATUS) => 'QUERY',
            ],
        ];

        if (isset($order->id)) {
            $statusRequestData['OrderId'] = $order->id;
        } elseif (isset($order->recurringId)) {
            $statusRequestData['Extra']['RECURRINGID'] = $order->recurringId;
        }

        return $statusRequestData;
    }

    /**
     * {@inheritDoc}
     */
    public function createCancelRequestData(AbstractPosAccount $account, $order): array
    {
        $orderData = [];
        if (isset($order->recurringOrderInstallmentNumber)) {
            // this method cancels only pending recurring orders, it will not cancel already fulfilled transactions
            $orderData['Extra']['RECORDTYPE'] = 'Order';
                // cancel single installment
                $orderData['Extra']['RECURRINGOPERATION'] = 'Cancel';
                /**
                 * the order ids of recurring order installments:
                 * 'ORD_ID_1' => '202210121ABC',
                 * 'ORD_ID_2' => '202210121ABC-2',
                 * 'ORD_ID_3' => '202210121ABC-3',
                 * ...
                 */
                $orderData['Extra']['RECORDID'] = $order->id . '-' . $order->recurringOrderInstallmentNumber;

            return $this->getRequestAccountData($account) + $orderData;
        }

        return $this->getRequestAccountData($account) + [
            'OrderId'  => $order->id,
            'Type'     => $this->mapTxType(AbstractGateway::TX_CANCEL),
        ];
    }

    /**
     * {@inheritDoc}
     * @return array{OrderId: string, Currency: string, Type: string, Total?: string, Name: string, Password: string, ClientId: string}
     */
    public function createRefundRequestData(AbstractPosAccount $account, $order): array
    {
        $requestData = [
            'OrderId'  => (string) $order->id,
            'Currency' => $this->mapCurrency($order->currency),
            'Type'     => $this->mapTxType(AbstractGateway::TX_REFUND),
        ];

        if (isset($order->amount)) {
            $requestData['Total'] = (string) $order->amount;
        }

        return $this->getRequestAccountData($account) + $requestData;
    }

    /**
     * {@inheritDoc}
     * @return array{OrderId: string, Extra: array<string, string>&mixed[], Name: string, Password: string, ClientId: string}
     */
    public function createHistoryRequestData(AbstractPosAccount $account, $order, array $extraData = []): array
    {
        $requestData = [
            'OrderId'  => (string) $extraData['order_id'], //todo orderId ya da id olarak degistirilecek, Payfor'da orderId, Garanti'de id
            'Extra'    => [
                $this->mapTxType(AbstractGateway::TX_HISTORY) => 'QUERY',
            ],
        ];

        return $this->getRequestAccountData($account) + $requestData;
    }


    /**
     * {@inheritDoc}
     */
    public function create3DFormData(AbstractPosAccount $account, $order, string $txType, string $gatewayURL, ?AbstractCreditCard $card = null): array
    {
        $data = $this->create3DFormDataCommon($account, $order, $txType, $gatewayURL, $card);

        $orderMapped = clone $order;
        $orderMapped->installment = $this->mapInstallment($order->installment);

        $data['inputs']['hash'] = $this->crypt->create3DHash($account, (array) $orderMapped, $this->mapTxType($txType));

        return $data;
    }

    /**
     * @param AbstractGateway::TX_* $txType
     *
     * @return array{gateway: string, method: 'POST', inputs: array<string, string>}
     */
    public function create3DFormDataCommon(AbstractPosAccount $account, $order, string $txType, string $gatewayURL, ?AbstractCreditCard $card = null): array
    {
        $inputs = [
            'clientid'  => $account->getClientId(),
            'storetype' => $this->secureTypeMappings[$account->getModel()],
            'amount'    => $order->amount,
            'oid'       => $order->id,
            'okUrl'     => $order->success_url,
            'failUrl'   => $order->fail_url,
            'rnd'       => $order->rand,
            'lang'      => $this->getLang($account, $order),
            'currency'  => $this->mapCurrency($order->currency),
            'taksit'    => $this->mapInstallment($order->installment),
            'islemtipi' => $this->mapTxType($txType),
            // custom data, any key value pairs can be used
            'firmaadi'  => $order->name,
            'Email'     => $order->email,
            // todo add custom data dynamically instead of hard coding them
        ];

        if ($card !== null) {
            $inputs['cardType'] = $this->cardTypeMapping[$card->getType()];
            $inputs['pan'] = $card->getNumber();
            $inputs['Ecom_Payment_Card_ExpDate_Month'] = $card->getExpireMonth(self::CREDIT_CARD_EXP_MONTH_FORMAT);
            $inputs['Ecom_Payment_Card_ExpDate_Year'] = $card->getExpireYear(self::CREDIT_CARD_EXP_YEAR_FORMAT);
            $inputs['cv2'] = $card->getCvv();
        }

        return [
            'gateway' => $gatewayURL,
            'method'  => 'POST',
            'inputs'  => $inputs,
        ];
    }

    public function mapInstallment(?int $installment): string
    {
        return $installment > 1 ? (string) $installment : '';
    }

    /**
     * @param AbstractPosAccount $account
     *
     * @return array{Name: string, Password: string, ClientId: string}
     */
    private function getRequestAccountData(AbstractPosAccount $account): array
    {
        return [
            'Name'     => $account->getUsername(),
            'Password' => $account->getPassword(),
            'ClientId' => $account->getClientId(),
        ];
    }

    /**
     * @return array{PbOrder: array{OrderType: string, OrderFrequencyInterval: string, OrderFrequencyCycle: string, TotalNumberPayments: string}}
     */
    private function getRecurringRequestOrderData($order): array
    {
        return [
            'PbOrder' => [
                'OrderType'              => '0',
                // Periyodik İşlem Frekansı
                'OrderFrequencyInterval' => (string) $order->recurringFrequency,
                //D|M|Y
                'OrderFrequencyCycle'    => $this->mapRecurringFrequency($order->recurringFrequencyType),
                'TotalNumberPayments'    => (string) $order->recurringInstallmentCount,
            ],
        ];
    }
}
