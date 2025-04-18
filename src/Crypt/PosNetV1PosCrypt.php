<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\PosNetAccount;

class PosNetV1PosCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha256';

    /** @var string */
    protected const HASH_SEPARATOR = '';

    /**
     * @param PosNetAccount $posAccount
     *
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData, ?string $txType = null): string
    {
        $hashData = [
            $posAccount->getClientId(),
            $posAccount->getTerminalId(),
            // no card data for 3D host payment
            $requestData['CardNo'] ?? null,
            $requestData['Cvv'] ?? null,
            $requestData['ExpiredDate'] ?? null,

            $requestData['Amount'],
            $posAccount->getStoreKey(),
        ];
        $hashStr  = \implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }

    /**
     * @param PosNetAccount $posAccount
     *
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $posAccount, array $data): bool
    {
        if (null === $posAccount->getStoreKey()) {
            throw new \LogicException('Account storeKey eksik!');
        }

        $actualHash = $this->hashFromParams($posAccount->getStoreKey(), $data, 'MacParams', ':');

        if ($actualHash !== $data['Mac']) {
            $this->logger->error('hash check failed', [
                'order_id' => $data['OrderId'],
            ]);

            return false;
        }

        $this->logger->debug('hash check is successful', [
            'order_id' => $data['OrderId'],
        ]);

        return true;
    }

    /**
     * @param PosNetAccount                               $posAccount
     * @param array<string, string|array<string, string>> $requestData
     *
     * @inheritDoc
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        /** @var array<string, string> $threeDSecureData */
        $threeDSecureData = $requestData['ThreeDSecureData'];
        $hashData = [
            $posAccount->getClientId(),
            $posAccount->getTerminalId(),
            $threeDSecureData['SecureTransactionId'],
            $threeDSecureData['CavvData'],
            $threeDSecureData['Eci'],
            $threeDSecureData['MdStatus'],
            $posAccount->getStoreKey(),
        ];

        $hashStr = implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }
}
