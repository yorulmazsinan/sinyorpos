<?php
/**
 * @license MIT
 */

namespace SinyorPos\Entity\Account;

use SinyorPos\PosInterface;

/**
 * KuveytPosAccount
 */
class KuveytPosAccount extends AbstractPosAccount
{
    /**
     * POS dokumanda response'da SubMerchantId yer aliyor
     * ancak kullanimi hakkinda hic bir bilgi yok.
     */
    protected ?string $subMerchantId;

    /**
     * @param string               $bank
     * @param string               $merchantId Mağaza Numarası
     * @param string               $username   POS panelinizden kullanıcı işlemleri sayfasında APİ rolünde kullanıcı oluşturulmalıdır
     * @param string               $customerId CustomerNumber, Müşteri No
     * @param string               $storeKey   Oluşturulan APİ kullanıcısının şifre bilgisidir.
     * @param PosInterface::LANG_* $lang
     * @param string|null          $subMerchantId
     */
    public function __construct(
        string $bank,
        string $merchantId,
        string $username,
        string $customerId,
        string $storeKey,
        string $lang,
        ?string $subMerchantId = null
    ) {
        parent::__construct($bank, $merchantId, $username, $customerId, $lang, $storeKey);
        $this->subMerchantId = $subMerchantId;
    }

    /**
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSubMerchantId(): ?string
    {
        return $this->subMerchantId;
    }
}
