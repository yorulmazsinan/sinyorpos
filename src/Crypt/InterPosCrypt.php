<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Exceptions\NotImplementedException;

class InterPosCrypt extends AbstractCrypt
{
    /**
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        $hashData = [
            $posAccount->getClientId(),
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
        if (null === $posAccount->getStoreKey()) {
            throw new \LogicException('Account storeKey eksik!');
        }

        $actualHash = $this->hashFromParams($posAccount->getStoreKey(), $data, 'HASHPARAMS', ':');

        if ($data['HASH'] === $actualHash) {
            $this->logger->debug('hash check is successful');

            return true;
        }

        $this->logger->error('hash check failed', [
            'data' => $data,
            'generated_hash' => $actualHash,
            'expected_hash' => $data['HASH'],
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
