<?php
namespace EceoPos\Factory;

use DateTimeImmutable;
use DomainException;
use EceoPos\Entity\Card\AbstractCreditCard;
use EceoPos\Entity\Card\CreditCard;
use EceoPos\Exceptions\CardTypeNotSupportedException;
use EceoPos\Exceptions\CardTypeRequiredException;
use EceoPos\PosInterface;

/**
 * CreditCardFactory
 */
class CreditCardFactory
{
	/**
	 * AbstractCreditCard yapıcı metodu:
	 * @param PosInterface $pos
	 * @param string $number ; boşluklu veya boşluksuz kredi kartı numarası
	 * @param string $expireYear ; yılı 1, 2 ve 4 haneli olarak kabul eder. (örn: 1, 20, 2020)
	 * @param string $expireMonth ; tek haneli ve çift haneli olarak ay değerlerini kabul eder. (örn: 1, 01, 12)
	 * @param string $cvv
	 * @param string|null $cardHolderName
	 * @param string|null $cardType ; kart tipidir ve bankaya göre zorunludur. (örn: Visa, MasterCard, ...)
	 * @return AbstractCreditCard
	 */
	public static function create($data): AbstractCreditCard
	{
		$pos = $data['pos']; // PosInterface
		$number = preg_replace('/\s+/', '', $data['card_number']); // boşluklu veya boşluksuz kredi kartı numarası
		$expireYear = str_pad($data['card_expiry_year'], 2, '0', STR_PAD_LEFT); // yılı 1, 2 ve 4 haneli olarak kabul eder. (örn: 1, 20, 2020)
		$expireYear = str_pad($data['card_expiry_year'], 4, '20', STR_PAD_LEFT); // yılı 1, 2 ve 4 haneli olarak kabul eder. (örn: 1, 20, 2020)
		$expireMonth = str_pad($data['card_expiry_month'], 2, '0', STR_PAD_LEFT); // tek haneli ve çift haneli olarak ay değerlerini kabul eder. (örn: 1, 01, 12)
		$cvv = $data['card_cvv']; // kart güvenlik kodu
		$cardHolderName = $data['card_name']; // kart sahibinin adı
		$cardType = $data['card_type']; // kart tipidir ve bankaya göre zorunludur. (örn: Visa, MasterCard, ...)
		$expDate = DateTimeImmutable::createFromFormat('Ym', $expireYear . $expireMonth); // kartın son kullanma tarihi
		if (!$expDate) {
			throw new DomainException('Geçersiz tarih biçimi!');
		}
		$supportedCardTypes = array_keys($pos->getCardTypeMapping());
		if (!empty($supportedCardTypes) && empty($cardType)) {
			throw new CardTypeRequiredException($pos::NAME);
		}
		if (!empty($supportedCardTypes) && !in_array($cardType, $supportedCardTypes)) {
			throw new CardTypeNotSupportedException($cardType);
		}
		return new CreditCard($number, $expDate, $cvv, $cardHolderName, $cardType);
	}
}
