<?php
/**
 * @license MIT
 */
namespace SinyorPos\DataMapper;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;

/**
 * Creates request data for EstPos Gateway requests that supports v3 Hash algorithm
 */
class EstV3PosRequestDataMapper extends EstPosRequestDataMapper
{
    /**
     * {@inheritDoc}
     */
    public function create3DFormData(AbstractPosAccount $account, $order, string $txType, string $gatewayURL, ?AbstractCreditCard $card = null): array
    {
        $data = $this->create3DFormDataCommon($account, $order, $txType, $gatewayURL, $card);

        $data['inputs']['TranType'] = $this->mapTxType($txType);
        unset($data['inputs']['islemtipi']);

        $data['inputs']['hashAlgorithm'] = 'ver3';
        $data['inputs']['hash'] = $this->crypt->create3DHash($account, $data['inputs'], $txType);

        return $data;
    }
}
