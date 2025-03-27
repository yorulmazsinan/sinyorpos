<?php

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Account\PosNetAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Exceptions\NotImplementedException;
use SinyorPos\Gateways\AbstractGateway;
use Psr\Log\LogLevel;

class PosNetCrypt extends AbstractCrypt
{
    /** @var string */
    protected const HASH_ALGORITHM = 'sha256';
    
    /** @var string */
    protected const HASH_SEPARATOR = ';';

    /**
     * @param PosNetAccount $account
     *
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $account, array $requestData, ?string $txType = null): string
    {
        if ($account->getModel() === AbstractGateway::MODEL_3D_SECURE || $account->getModel() === AbstractGateway::MODEL_3D_PAY) {
            $secondHashData = [
                $requestData['id'],
                $requestData['amount'],
                $requestData['currency'],
                $account->getClientId(),
                $this->createSecurityData($account),
            ];
            $hashStr        = implode(static::HASH_SEPARATOR, $secondHashData);

            return $this->hashString($hashStr);
        }

        return '';
    }

    /**
     * @param PosNetAccount $account
     *
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $account, array $data): bool
    {
        $hashStr = '';

        if ($account->getModel() === AbstractGateway::MODEL_3D_SECURE || $account->getModel() === AbstractGateway::MODEL_3D_PAY) {
            $secondHashData = [
                $data['mdStatus'],
                $data['xid'],
                $data['amount'],
                $data['currency'],
                $account->getClientId(),
                $this->createSecurityData($account),
            ];
            $hashStr = implode(static::HASH_SEPARATOR, $secondHashData);
        }

        if ($this->hashString($hashStr) !== $data['mac']) {
            $this->logger->log(LogLevel::ERROR, 'hash check failed', [
                'order_id' => $data['xid'],
            ]);

            return false;
        }

        $this->logger->log(LogLevel::DEBUG, 'hash check is successful', [
            'order_id' => $data['xid'],
        ]);

        return true;
    }

    public function createHash(AbstractPosAccount $account, array $requestData, ?string $txType = null, ?AbstractCreditCard $card = null): string
    {
        throw new NotImplementedException();
    }

    /**
     * Make Security Data
     *
     * @param PosNetAccount $account
     *
     * @return string
     */
    public function createSecurityData(AbstractPosAccount $account): string
    {
        $hashData = [
            $account->getStoreKey(),
            $account->getTerminalId(),
        ];
        $hashStr  = implode(static::HASH_SEPARATOR, $hashData);

        return $this->hashString($hashStr);
    }
}
