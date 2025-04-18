<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\GarantiPosAccount;

class GarantiPosCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha512';

    /**
     * @param GarantiPosAccount $posAccount
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        $map = [
            $posAccount->getTerminalId(),
            $requestData['orderid'],
            $requestData['txnamount'],
            $requestData['txncurrencycode'],
            $requestData['successurl'],
            $requestData['errorurl'],
            $requestData['txntype'],
            $requestData['txninstallmentcount'],
            $posAccount->getStoreKey(),
            $this->createSecurityData($posAccount, $requestData['txntype']),
        ];

        return $this->hashStringUpperCase(implode(static::HASH_SEPARATOR, $map), self::HASH_ALGORITHM);
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $posAccount, array $data): bool
    {
        if (null === $posAccount->getStoreKey()) {
            throw new \LogicException('Account storeKey eksik!');
        }

        $actualHash = $this->hashFromParams($posAccount->getStoreKey(), $data, 'hashparams', ':');

        if ($data['hash'] === $actualHash) {
            $this->logger->debug('hash check is successful');

            return true;
        }

        $this->logger->error('hash check failed', [
            'data'           => $data,
            'generated_hash' => $actualHash,
            'expected_hash'  => $data['hash'],
        ]);

        return false;
    }

    /**
     * Make Hash Data
     *
     * @param GarantiPosAccount $posAccount
     * {@inheritDoc}
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        $map = [
            $requestData['Order']['OrderID'],
            $posAccount->getTerminalId(),
            $requestData['Card']['Number'] ?? null,
            $requestData['Transaction']['Amount'],
            $requestData['Transaction']['CurrencyCode'] ?? null,
            $this->createSecurityData($posAccount, $requestData['Transaction']['Type']),
        ];

        return $this->hashStringUpperCase(implode(static::HASH_SEPARATOR, $map), self::HASH_ALGORITHM);
    }

    /**
     * @inheritDoc
     */
    public function hashString(string $str, ?string $encryptionKey = null): string
    {
        return $this->hashStringUpperCase($str, self::HASH_ALGORITHM);
    }

    /**
     * Make Security Data
     *
     * @param GarantiPosAccount $posAccount
     * @param string|null       $txType
     *
     * @return string
     */
    private function createSecurityData(AbstractPosAccount $posAccount, ?string $txType = null): string
    {
        $password = 'void' === $txType || 'refund' === $txType ? $posAccount->getRefundPassword() : $posAccount->getPassword();

        $map = [
            $password,
            \str_pad($posAccount->getTerminalId(), 9, '0', STR_PAD_LEFT),
        ];

        return $this->hashStringUpperCase(\implode(static::HASH_SEPARATOR, $map), 'sha1');
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function hashStringUpperCase(string $str, string $algorithm): string
    {
        return strtoupper(hash($algorithm, $str));
    }
}
