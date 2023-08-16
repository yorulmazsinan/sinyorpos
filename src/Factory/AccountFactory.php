<?php
namespace EceoPos\Factory;

use EceoPos\Entity\Account\ESTVirtualPosAccount;
use EceoPos\Entity\Account\GarantiVirtualPosAccount;
use EceoPos\Entity\Account\InterVirtualPosAccount;
use EceoPos\Entity\Account\KuveytVirtualPosAccount;
use EceoPos\Entity\Account\PayForVirtualPosAccount;
use EceoPos\Entity\Account\PosNetVirtualPosAccount;
use EceoPos\Entity\Account\VakifBankVirtualPosAccount;
use EceoPos\Exceptions\MissingAccountInfoException;
use EceoPos\Gateways\AbstractGateway;

/**
 * AccountFactory
 */
class AccountFactory
{
	/**
	 * @param string $bank
	 * @param string $clientId ; Üye iş yeri numarası
	 * @param string $kullaniciAdi
	 * @param string $password
	 * @param string $model
	 * @param string|null $storeKey
	 * @param string $lang
	 * @return ESTVirtualPosAccount
	 * @throws MissingAccountInfoException
	 */
	public static function createESTVirtualPosAccount(string $bank, string $clientId, string $kullaniciAdi, string $password, string $model = AbstractGateway::MODEL_NON_SECURE, ?string $storeKey = null, string $lang = AbstractGateway::LANG_TR): ESTVirtualPosAccount
	{
		self::checkParameters($model, $storeKey);
		return new ESTVirtualPosAccount($bank, $model, $clientId, $kullaniciAdi, $password, $lang, $storeKey);
	}

	/**
	 * @param string $bank
	 * @param string $merchantId
	 * @param string $userCode
	 * @param string $userPassword
	 * @param string $model
	 * @param string|null $merchantPass
	 * @param string $lang
	 * @return PayForVirtualPosAccount
	 * @throws MissingAccountInfoException
	 */
	public static function createPayForVirtualPosAccount(string $bank, string $merchantId, string $userCode, string $userPassword, string $model = AbstractGateway::MODEL_NON_SECURE, ?string $merchantPass = null, string $lang = AbstractGateway::LANG_TR): PayForVirtualPosAccount
	{
		self::checkParameters($model, $merchantPass);
		return new PayForVirtualPosAccount($bank, $model, $merchantId, $userCode, $userPassword, $lang, $merchantPass);
	}

	/**
	 * @param string $bank
	 * @param string $merchantId Üye işyeri Numarası
	 * @param string $userId
	 * @param string $password Terminal UserID şifresi
	 * @param string $terminalId
	 * @param string $model
	 * @param string|null $storeKey
	 * @param string|null $refundUsername
	 * @param string|null $refundPassword
	 * @param string $lang
	 * @return GarantiVirtualPosAccount
	 * @throws MissingAccountInfoException
	 */
	public static function createGarantiVirtualPosAccount(string $bank, string $merchantId, string $userId, string $password, string $terminalId, string $model = AbstractGateway::MODEL_NON_SECURE, ?string $storeKey = null, ?string $refundUsername = null, ?string $refundPassword = null, string $lang = AbstractGateway::LANG_TR): GarantiVirtualPosAccount
	{
		self::checkParameters($model, $storeKey);
		return new GarantiVirtualPosAccount($bank, $model, $merchantId, $userId, $password, $lang, $terminalId, $storeKey, $refundUsername, $refundPassword);
	}

	/**
	 * @param string $bank
	 * @param string $merchantId Mağaza Numarası
	 * @param string $username POS panelinizden kullanıcı işlemleri sayfasında APİ rolünde kullanıcı oluşturulmalıdır
	 * @param string $customerId CustomerNumber, Müşteri No
	 * @param string $storeKey Oluşturulan APİ kullanıcısının şifre bilgisidir.
	 * @param string $model
	 * @param string $lang
	 * @param string|null $subMerchantId
	 * @return KuveytVirtualPosAccount
	 */
	public static function createKuveytVirtualPosAccount(string $bank, string $merchantId, string $username, string $customerId, string $storeKey, string $model = AbstractGateway::MODEL_3D_SECURE, string $lang = AbstractGateway::LANG_TR, ?string $subMerchantId = null): KuveytVirtualPosAccount
	{
		return new KuveytVirtualPosAccount($bank, $merchantId, $username, $customerId, $storeKey, $model, $lang, $subMerchantId);
	}

	/**
	 * @param string $bank
	 * @param string $merchantId
	 * @param string $username kullanilmamakta, bos atayin
	 * @param string $password kullanilmamakta, bos atayin
	 * @param string $terminalId
	 * @param string $posNetId
	 * @param string $model
	 * @param string|null $storeKey
	 * @param string $lang
	 * @return PosNetVirtualPosAccount
	 * @throws MissingAccountInfoException
	 */
	public static function createPosNetVirtualPosAccount(string $bank, string $merchantId, string $username, string $password, string $terminalId, string $posNetId, string $model = AbstractGateway::MODEL_NON_SECURE, ?string $storeKey = null, string $lang = AbstractGateway::LANG_TR): PosNetVirtualPosAccount
	{
		self::checkParameters($model, $storeKey);
		return new PosNetVirtualPosAccount($bank, $model, $merchantId, $username, $password, $lang, $terminalId, $posNetId, $storeKey);
	}

	/**
	 * @param string $bank
	 * @param string $merchantId ; Üye işyeri numarası
	 * @param string $password ; Üye işyeri şifresi
	 * @param string $terminalNo ; İşlemin hangi terminal üzerinden gönderileceği bilgisi
	 * @param string $model
	 * @param int $merchantType
	 * @param null $subMerchantId
	 * @return VakifBankVirtualPosAccount
	 * @throws MissingAccountInfoException
	 */
	public static function createVakifBankVirtualPosAccount(string $bank, string $merchantId, string $password, string $terminalNo, string $model = AbstractGateway::MODEL_NON_SECURE, int $merchantType = VakifBankVirtualPosAccount::MERCHANT_TYPE_STANDARD, $subMerchantId = null): VakifBankVirtualPosAccount
	{
		self::checkVakifBankMerchantType($merchantType, $subMerchantId);
		return new VakifBankVirtualPosAccount($bank, $model, $merchantId, $password, $terminalNo, $merchantType, $subMerchantId);
	}

	/**
	 * @param string $bank
	 * @param string $shopCode
	 * @param string $userCode
	 * @param string $userPass
	 * @param string $model
	 * @param string|null $merchantPass
	 * @param string $lang
	 * @return InterVirtualPosAccount
	 * @throws MissingAccountInfoException
	 */
	public static function createInterVirtualPosAccount(string $bank, string $shopCode, string $userCode, string $userPass, string $model = AbstractGateway::MODEL_NON_SECURE, ?string $merchantPass = null, string $lang = AbstractGateway::LANG_TR): InterVirtualPosAccount
	{
		self::checkParameters($model, $merchantPass);
		return new InterVirtualPosAccount($bank, $model, $shopCode, $userCode, $userPass, $lang, $merchantPass);
	}

	/**
	 * @param string $model
	 * @param string|null $storeKey
	 * @return void
	 * @throws MissingAccountInfoException
	 */
	private static function checkParameters(string $model, ?string $storeKey): void
	{
		if (AbstractGateway::MODEL_NON_SECURE !== $model && null === $storeKey) {
			throw new MissingAccountInfoException("$model storeKey gerekli!");
		}
	}

	/**
	 * @param int $merchantType
	 * @param string|null $subMerchantId
	 * @return void
	 * @throws MissingAccountInfoException
	 */
	private static function checkVakifBankMerchantType(int $merchantType, ?string $subMerchantId): void
	{
		if (VakifBankVirtualPosAccount::MERCHANT_TYPE_SUB_DEALER === $merchantType && empty($subMerchantId)) {
			throw new MissingAccountInfoException('"Alt Bayi"ler için "SubMerchantId" bilgisi zorunludur!');
		}
		if (!in_array($merchantType, VakifBankVirtualPosAccount::getMerchantTypes())) {
			throw new MissingAccountInfoException('Geçersiz MerchantType!');
		}
	}
}
