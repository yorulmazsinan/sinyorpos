<?php

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\GarantiPosAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use Psr\Log\LogLevel;

class GarantiPosCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha512';
    
    /**
     * @param GarantiPosAccount $account
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $account, array $requestData, ?string $txType = null): string
    {
        $map = [
            $account->getTerminalId(),
            $requestData['id'],
            $requestData['amount'],
            $requestData['success_url'],
            $requestData['fail_url'],
            $txType,
            $requestData['installment'],
            $account->getStoreKey(),
            $this->createSecurityData($account, $txType),
        ];

        return $this->hashStringUpperCase(\implode(static::HASH_SEPARATOR, $map), self::HASH_ALGORITHM);
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $account, array $data): bool
    {
        $actualHash = $this->hashFromParams($account->getStoreKey(), $data, 'hashparams', ':');

        if ($data['hash'] === $actualHash) {
            $this->logger->log(LogLevel::DEBUG, 'hash check is successful');

            return true;
        }

        $this->logger->log(LogLevel::ERROR, 'hash check failed', [
            'data'           => $data,
            'generated_hash' => $actualHash,
            'expected_hash'  => $data['hash'],
        ]);

        return false;
    }

    /**
     * Make Hash Data
     *
     * @param GarantiPosAccount       $account
     * {@inheritDoc}
     */
    public function createHash(AbstractPosAccount $account, array $requestData, ?string $txType = null, ?AbstractCreditCard $card = null): string
    {
        $map = [
            $requestData['id'],
            $account->getTerminalId(),
            isset($card) ? $card->getNumber() : null,
            $requestData['amount'],
            $this->createSecurityData($account, $txType),
        ];

        return $this->hashStringUpperCase(\implode(static::HASH_SEPARATOR, $map), self::HASH_ALGORITHM);
    }

    /**
     * Make Security Data
     *
     * @param GarantiPosAccount $account
     * @param string|null       $txType
     *
     * @return string
     */
    private function createSecurityData(AbstractPosAccount $account, ?string $txType = null): string
    {
        $password = 'void' === $txType || 'refund' === $txType ? $account->getRefundPassword() : $account->getPassword();

        $map = [
            $password,
            str_pad($account->getTerminalId(), 9, '0', STR_PAD_LEFT),
        ];

        return $this->hashStringUpperCase(\implode(static::HASH_SEPARATOR, $map), self::HASH_ALGORITHM);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function hashStringUpperCase(string $str): string
    {
        return strtoupper(hash(static::HASH_ALGORITHM, $str));
    }
}
