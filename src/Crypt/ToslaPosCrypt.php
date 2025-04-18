<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;

class ToslaPosCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha512';

    /**
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        $hashData = [
            $posAccount->getStoreKey(),
            $posAccount->getClientId(),
            $posAccount->getUsername(),
            $requestData['rnd'],
            $requestData['timeSpan'],
        ];

        $hashStr = \implode(static::HASH_SEPARATOR, $hashData);

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

        $data['ClientId'] = $posAccount->getClientId();
        $data['ApiUser']  = $posAccount->getUsername();

        $actualHash = $this->hashFromParams($posAccount->getStoreKey(), $data, 'HashParameters', ',');

        if ($data['Hash'] === $actualHash) {
            $this->logger->debug('hash check is successful');

            return true;
        }

        $this->logger->error('hash check failed', [
            'data'           => $data,
            'generated_hash' => $actualHash,
            'expected_hash'  => $data['Hash'],
        ]);

        return false;
    }

    /**
     * @param AbstractPosAccount   $posAccount
     * @param array<string, mixed> $requestData
     *
     * @return string
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        return $this->create3DHash($posAccount, $requestData);
    }

    /**
     * @inheritDoc
     */
    protected function concatenateHashKey(string $hashKey, string $hashString): string
    {
        return $hashKey.$hashString;
    }
}
