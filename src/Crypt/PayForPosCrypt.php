<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Exceptions\NotImplementedException;

class PayForPosCrypt extends AbstractCrypt
{
    /**
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        $hashData = [
            $requestData['MbrId'],
            $requestData['OrderId'],
            $requestData['PurchAmount'],
            $requestData['OkUrl'],
            $requestData['FailUrl'],
            $requestData['TxnType'],
            $requestData['InstallmentCount'],
            $requestData['Rnd'],
            $posAccount->getStoreKey(),
        ];
        $hashStr = implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $posAccount, array $data): bool
    {
        $hashData = [
            $posAccount->getClientId(),
            $posAccount->getStoreKey(),
            $data['OrderId'],
            $data['AuthCode'],
            $data['ProcReturnCode'],
            $data['3DStatus'],
            $data['ResponseRnd'],
            $posAccount->getUsername(),
        ];

        $hashStr = implode(static::HASH_SEPARATOR, $hashData);

        $hash = $this->hashString($hashStr);

        if ($hash === $data['ResponseHash']) {
            $this->logger->debug('hash check is successful');

            return true;
        }

        $this->logger->error('hash check failed', [
            'data'           => $data,
            'generated_hash' => $hash,
            'expected_hash'  => $data['ResponseHash'],
        ]);

        return false;
    }

    /**
     * @inheritdoc
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        throw new NotImplementedException();
    }
}
