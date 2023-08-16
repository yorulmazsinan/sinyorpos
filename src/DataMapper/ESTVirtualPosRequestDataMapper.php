<?php

namespace EceoPos\DataMapper;

use EceoPos\Entity\Account\AbstractPosAccount;
use EceoPos\Entity\Card\AbstractCreditCard;
use EceoPos\Gateways\AbstractGateway;

/**
 * ESTVirtualPos; ağ geçidi için "request data mapper" sınıfı:
 */
class ESTVirtualPosRequestDataMapper extends AbstractRequestDataMapper
{
    public const CREDIT_CARD_EXP_DATE_FORMAT = 'm/y';

    public const CREDIT_CARD_EXP_MONTH_FORMAT = 'm';

    public const CREDIT_CARD_EXP_YEAR_FORMAT = 'y';

    /**
     * {@inheritdoc}
     */
    protected $txTypeMappings = [
        AbstractGateway::TX_PAY => 'Auth',
        AbstractGateway::TX_PRE_PAY => 'PreAuth',
        AbstractGateway::TX_POST_PAY => 'PostAuth',
        AbstractGateway::TX_CANCEL => 'Void',
        AbstractGateway::TX_REFUND => 'Credit',
        AbstractGateway::TX_STATUS => 'ORDERSTATUS',
        AbstractGateway::TX_HISTORY => 'ORDERHISTORY',
    ];

    protected $cardTypeMapping = [
        AbstractCreditCard::CARD_TYPE_VISA => '1',
        AbstractCreditCard::CARD_TYPE_MASTERCARD => '2',
        AbstractCreditCard::CARD_TYPE_TROY => '4',
    ];

    protected $recurringOrderFrequencyMapping = [
        'DAY' => 'D',
        'WEEK' => 'W',
        'MONTH' => 'M',
        'YEAR' => 'Y',
    ];

    protected $secureTypeMappings = [
        AbstractGateway::MODEL_3D_SECURE => '3d',
        AbstractGateway::MODEL_3D_PAY => '3d_pay',
        AbstractGateway::MODEL_3D_HOST => '3d_host',
        AbstractGateway::MODEL_NON_SECURE => 'regular',
    ];

    public function create3DPaymentRequestData(AbstractPosAccount $account, $order, string $txType, array $responseData): array
    {
        $requestData = $this->getRequestAccountData($account) + [
            'Type' => $this->mapTxType($txType),
            'IPAddress' => $order->ip ?? null,
            'Email' => $order->email,
            'OrderId' => $order->id,
            'UserId' => $order->user_id ?? null,
            'Total' => $order->amount,
            'Currency' => $this->mapCurrency($order->currency),
            'Taksit' => $this->mapInstallment($order->installment),
            'Number' => $responseData['md'],
            'PayerTxnId' => $responseData['xid'],
            'PayerSecurityLevel' => $responseData['eci'],
            'PayerAuthenticationCode' => $responseData['cavv'],
            'Mode' => 'P',
        ];
        if ($order->name) {
            $requestData['BillTo'] = [
                'Name' => $order->name,
            ];
        }
        if (isset($order->recurringFrequency)) {
            $requestData['PbOrder'] = [
                'OrderType' => 0,
                // Periyodik İşlem Frekansı
                'OrderFrequencyInterval' => $order->recurringFrequency,
                //D|M|Y
                'OrderFrequencyCycle' => $this->mapRecurringFrequency($order->recurringFrequencyType),
                'TotalNumberPayments' => $order->recurringInstallmentCount,
            ];
        }

        return $requestData;
    }

    public function createNonSecurePaymentRequestData(AbstractPosAccount $account, $order, string $txType, ?AbstractCreditCard $card = null): array
    {
        return $this->getRequestAccountData($account) + [
            'Type' => $this->mapTxType($txType),
            'IPAddress' => $order->ip ?? null,
            'Email' => $order->email,
            'OrderId' => $order->id,
            'UserId' => $order->user_id ?? null,
            'Total' => $order->amount,
            'Currency' => $this->mapCurrency($order->currency),
            'Taksit' => $this->mapInstallment($order->installment),
            'Number' => $card->getNumber(),
            'Expires' => $card->getExpirationDate(self::CREDIT_CARD_EXP_DATE_FORMAT),
            'Cvv2Val' => $card->getCvv(),
            'Mode' => 'P',
            'BillTo' => [
                'Name' => $order->name ?: null,
            ],
        ];
    }

    public function createNonSecurePostAuthPaymentRequestData(AbstractPosAccount $account, $order, ?AbstractCreditCard $card = null): array
    {
        return $this->getRequestAccountData($account) + [
            'Type' => $this->mapTxType(AbstractGateway::TX_POST_PAY),
            'OrderId' => $order->id,
        ];
    }

    public function createStatusRequestData(AbstractPosAccount $account, $order): array
    {
        return $this->getRequestAccountData($account) + [
            'OrderId' => $order->id,
            'Extra' => [
                $this->mapTxType(AbstractGateway::TX_STATUS) => 'QUERY',
            ],
        ];
    }

    public function createCancelRequestData(AbstractPosAccount $account, $order): array
    {
        return $this->getRequestAccountData($account) + [
            'OrderId' => $order->id,
            'Type' => $this->mapTxType(AbstractGateway::TX_CANCEL),
        ];
    }

    public function createRefundRequestData(AbstractPosAccount $account, $order): array
    {
        $requestData = [
            'OrderId' => $order->id,
            'Currency' => $this->mapCurrency($order->currency),
            'Type' => $this->mapTxType(AbstractGateway::TX_REFUND),
        ];
        if (isset($order->amount)) {
            $requestData['Total'] = $order->amount;
        }

        return $this->getRequestAccountData($account) + $requestData;
    }

    public function createHistoryRequestData(AbstractPosAccount $account, $order, array $extraData = []): array
    {
        $requestData = [
            'OrderId' => $extraData['order_id'],
            // TODO: "orderId" ya da "id" olarak değiştirilmesi gerekiyor; Payfor'da "orderId", Garanti'de "id"
            'Extra' => [
                $this->mapTxType(AbstractGateway::TX_HISTORY) => 'QUERY',
            ],
        ];

        return $this->getRequestAccountData($account) + $requestData;
    }

    public function create3DFormData(AbstractPosAccount $account, $order, string $txType, string $gatewayURL, ?AbstractCreditCard $card = null): array
    {
        $hash = $this->create3DHash($account, $order, $txType);
        $inputs = [
            'clientid' => $account->getClientId(),
            'storetype' => $this->secureTypeMappings[$account->getModel()],
            'hash' => $hash,
            'firmaadi' => $order->name,
            'Email' => $order->email,
            'amount' => $order->amount,
            'oid' => $order->id,
            'okUrl' => $order->success_url,
            'failUrl' => $order->fail_url,
            'rnd' => $order->rand,
            'lang' => $this->getLang($account, $order),
            'currency' => $this->mapCurrency($order->currency),
            'taksit' => $this->mapInstallment($order->installment),
            'islemtipi' => $this->mapTxType($txType),
        ];
        if ($card) {
            $inputs['cardType'] = $this->cardTypeMapping[$card->getType()];
            $inputs['pan'] = $card->getNumber();
            $inputs['Ecom_Payment_Card_ExpDate_Month'] = $card->getExpireMonth(self::CREDIT_CARD_EXP_MONTH_FORMAT);
            $inputs['Ecom_Payment_Card_ExpDate_Year'] = $card->getExpireYear(self::CREDIT_CARD_EXP_YEAR_FORMAT);
            $inputs['cv2'] = $card->getCvv();
        }

        return [
            'gateway' => $gatewayURL,
            'inputs' => $inputs,
        ];
    }

    public function create3DHash(AbstractPosAccount $account, $order, string $txType): string
    {
        $hashData = [
            $account->getClientId(),
            $order->id,
            $order->amount,
            $order->success_url,
            $order->fail_url,
            $this->mapTxType($txType),
            $this->mapInstallment($order->installment),
            $order->rand,
            $account->getStoreKey(),
        ];
        $hashStr = implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }

    public function mapInstallment(?int $installment)
    {
        return $installment > 1 ? $installment : '';
    }

    /**
     * @param  AbstractPosAccount  $account
     * @return array
     */
    private function getRequestAccountData(AbstractPosAccount $account): array
    {
        return [
            'Name' => $account->getUsername(),
            'Password' => $account->getPassword(),
            'ClientId' => $account->getClientId(),
        ];
    }
}
