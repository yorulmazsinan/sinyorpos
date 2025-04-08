<?php

/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Exceptions\NotImplementedException;

class PayFlexCPV4Crypt extends AbstractCrypt
{
    /**
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $formInputs): string
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $posAccount, array $data): bool
    {
        throw new NotImplementedException();
    }

    /**
     * todo "ErrorCode" => "5029"
     * "ResponseMessage" => "Geçersiz İstek" hash ile istek gonderince hatasi aliyoruz.
     *
     * {@inheritDoc}
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        $hashData = [
            $requestData['HostMerchantId'],
            $requestData['AmountCode'],
            $requestData['Amount'],
            $requestData['MerchantPassword'],
            '',
            'VBank3DPay2014', // todo
        ];

        $hashStr = \implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }
}
