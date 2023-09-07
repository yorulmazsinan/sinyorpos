<?php

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Exceptions\NotImplementedException;

class PayFlexCPV4Crypt extends AbstractCrypt
{
    /**
     * todo "ErrorCode" => "5029"
     * "ResponseMessage" => "Geçersiz İstek" hash ile istek gonderince hatasi aliyoruz.
     *
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $account, array $requestData, ?string $txType = null): string
    {
        $hashData = [
            $account->getClientId(),
            $requestData['currency'],
            $requestData['amount'],
            $account->getPassword(),
            '',
            'VBank3DPay2014', // todo
        ];

        $hashStr = implode(static::HASH_SEPARATOR, $hashData);

        return '';
        //return $this->hashString($hashStr);
    }

    /**
     * todo implement
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $account, array $data): bool
    {
         return true;
    }

    public function createHash(AbstractPosAccount $account, array $requestData, ?string $txType = null, ?AbstractCreditCard $card = null): string
    {
        throw new NotImplementedException();
    }
}
