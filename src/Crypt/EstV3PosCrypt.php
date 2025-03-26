<?php

namespace SinyorPos\Crypt;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Exceptions\NotImplementedException;
use Psr\Log\LogLevel;

class EstV3PosCrypt extends AbstractCrypt
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
        // Nestpay Hash Versiyon 3 için hashAlgorithm parametresini ekle
        $requestData['hashAlgorithm'] = 'ver3';
        
        // Hash hesaplaması için Encoding ve Hash parametrelerini çıkar
        $hashData = $requestData;
        unset($hashData['hash']);
        unset($hashData['encoding']);
        
        // Parametreleri alfabetik olarak sırala
        ksort($hashData, SORT_NATURAL | SORT_FLAG_CASE);
        
        // Parametreleri StoreKey ile birleştir
        $hashData[] = $account->getStoreKey();
        
        // Escape | ve \ karakterleri
        $data = [];
        foreach ($hashData as $value) {
            $value = str_replace("\\", "\\\\", (string)$value);
            $value = str_replace(self::HASH_SEPARATOR, "\\".self::HASH_SEPARATOR, $value);
            $data[] = $value;
        }
        
        $hashStr = implode(self::HASH_SEPARATOR, $data);
        
        return $this->hashString($hashStr);
    }

    /**
     * {@inheritdoc}
     */
    public function check3DHash(AbstractPosAccount $account, array $data): bool
    {
        // Hash hesaplaması için hash, encoding ve countdown parametrelerini çıkar
        $hashData = $data;
        unset($hashData['HASH']);
        unset($hashData['encoding']);
        unset($hashData['countdown']);
        
        // Parametreleri alfabetik olarak sırala
        ksort($hashData, SORT_NATURAL | SORT_FLAG_CASE);
        
        // Parametreleri StoreKey ile birleştir
        $hashData[] = $account->getStoreKey();
        
        // Escape | ve \ karakterleri
        $dataArray = [];
        foreach ($hashData as $value) {
            $value = str_replace("\\", "\\\\", (string)$value);
            $value = str_replace(self::HASH_SEPARATOR, "\\".self::HASH_SEPARATOR, $value);
            $dataArray[] = $value;
        }
        
        $hashStr = implode(self::HASH_SEPARATOR, $dataArray);
        $actualHash = $this->hashString($hashStr);

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
