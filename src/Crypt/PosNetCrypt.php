<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\PosNetAccount;
use SinyorPos\Exceptions\NotImplementedException;

class PosNetCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha256';

    /** @var string */
    protected const HASH_SEPARATOR = ';';

    /**
     * @param PosNetAccount $posAccount
     *
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData, ?string $txType = null): string
    {
        $secondHashData = [
            $requestData['id'],
            $requestData['amount'],
            $requestData['currency'],
            $posAccount->getClientId(),
            $this->createSecurityData($posAccount),
        ];
        $hashStr        = implode(static::HASH_SEPARATOR, $secondHashData);

        return $this->hashString($hashStr);
    }

    /**
     * @param PosNetAccount $posAccount
     *
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $posAccount, array $data): bool
    {
        $secondHashData = [
            $data['mdStatus'],
            $data['xid'],
            $data['amount'],
            $data['currency'],
            $posAccount->getClientId(),
            $this->createSecurityData($posAccount),
        ];
        $hashStr        = implode(static::HASH_SEPARATOR, $secondHashData);

        if ($this->hashString($hashStr) !== $data['mac']) {
            $this->logger->error('hash check failed', [
                'order_id' => $data['xid'],
            ]);

            return false;
        }

        $this->logger->debug('hash check is successful', [
            'order_id' => $data['xid'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        throw new NotImplementedException();
    }

    /**
     * Make Security Data
     *
     * @param PosNetAccount $posAccount
     *
     * @return string
     */
    public function createSecurityData(AbstractPosAccount $posAccount): string
    {
        $hashData = [
            $posAccount->getStoreKey(),
            $posAccount->getTerminalId(),
        ];
        $hashStr  = implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }
}
