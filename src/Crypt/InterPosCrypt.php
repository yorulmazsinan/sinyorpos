<?php

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Exceptions\NotImplementedException;
use Psr\Log\LogLevel;

class InterPosCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha512';
    
    /** @var string */
    protected const HASH_SEPARATOR = '|';
    
    /**
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $account, array $requestData, ?string $txType = null): string
    {
        $hashData = [
            $account->getClientId(),
            $requestData['id'],
            $requestData['amount'],
            $requestData['success_url'],
            $requestData['fail_url'],
            $txType,
            $requestData['installment'],
            $requestData['rand'],
            $account->getStoreKey(),
        ];

        $hashStr = implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $account, array $data): bool
    {
        // HASHPARAMS veya HASH yoksa işlem başarısız
        if (!isset($data['HASHPARAMS']) || !isset($data['HASH'])) {
            $this->logger->log(LogLevel::ERROR, 'hash check failed, HASHPARAMS or HASH not found', [
                'data' => $data
            ]);
            return false;
        }
        
        $actualHash = $this->hashFromParams($account->getStoreKey(), $data, 'HASHPARAMS', ':');

        if ($data['HASH'] === $actualHash) {
            $this->logger->log(LogLevel::DEBUG, 'hash check is successful');

            return true;
        }

        $this->logger->log(LogLevel::ERROR, 'hash check failed', [
            'data' => $data,
            'generated_hash' => $actualHash,
            'expected_hash' => $data['HASH']
        ]);

        return false;
    }

    public function createHash(AbstractPosAccount $account, array $requestData, ?string $txType = null, ?AbstractCreditCard $card = null): string
    {
        throw new NotImplementedException();
    }
}
