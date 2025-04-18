<?php
/**
 * @license MIT
 */

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;

/**
 * Dummy crypt that can be used if there is no cryptography logic needed.
 */
class NullCrypt extends AbstractCrypt
{
    /**
     * {@inheritDoc}
     */
    public function create3DHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $posAccount, array $data): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function createHash(AbstractPosAccount $posAccount, array $requestData): string
    {
        return '';
    }
}
