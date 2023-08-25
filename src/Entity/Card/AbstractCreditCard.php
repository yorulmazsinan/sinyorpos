<?php
namespace EceoPos\Entity\Card;

use DateTimeImmutable;

/**
 * Class AbstractCreditCard
 */
abstract class AbstractCreditCard
{
	public const CARD_TYPE_VISA = 'visa';
	public const CARD_TYPE_MASTERCARD = 'master';
	public const CARD_TYPE_AMEX = 'amex';
	public const CARD_TYPE_TROY = 'troy';
	/**
	 * 16 haneli boşluksuz bir şekilde girilmiş kredi kartı numarası.
	 * @var string
	 */
	protected $number;
	/** @var DateTimeImmutable */
	protected $expDate;
	/** @var string */
	protected $cvv;
	/** @var string|null */
	protected $holderName;
	/**
	 * visa, master, troy, amex vs.
	 * @var string|null
	 */
	protected $type;

	/**
	 * @param string $number ; boşluklu veya boşluksuz kredi kartı numarası
	 * @param DateTimeImmutable $expDate
	 * @param string $cvv
	 * @param string|null $cardHolderName
	 * @param string|null $cardType ; örnek değerler: 'visa', 'master', '1', '2' vb.
	 */
	public function __construct(string $number, DateTimeImmutable $expDate, string $cvv, ?string $cardHolderName = null, ?string $cardType = null)
	{
		$this->number = preg_replace('/\s+/', '', $number);
		$this->expDate = $expDate;
		$this->cvv = $cvv;
		$this->holderName = $cardHolderName;
		$this->type = $cardType;
	}

	/**
	 * Boşluk olmadan kart numarasını döndürür.
	 * @return string
	 */
	public function getNumber(): string
	{
		return $this->number;
	}

	/**
	 * EXP yılını 2 haneli olarak döndürür. (Örnek: '01' '02' '12')
	 * @param string $format
	 * @return string
	 */
	public function getExpireYear(string $format = 'y'): string
	{
		return $this->expDate->format($format);
	}

	/**
	 * EXP ayını 2 haneli olarak döndürür. (Örnek: '01' '02' '12')
	 * @param string $format
	 * @return string
	 */
	public function getExpireMonth(string $format = 'm'): string
	{
		return $this->expDate->format($format);
	}

	/**
	 * Kartın son kullanma tarihindeki ayı ve yılı birleştirir. (Örnek: '0120' '0220' '1220')
	 * @param string $format
	 * @return string
	 */
	public function getExpirationDate(string $format = 'ym'): string
	{
		return $this->expDate->format($format);
	}

	/**
	 * @return string
	 */
	public function getCvv(): string
	{
		return $this->cvv;
	}

	/**
	 * @return string|null
	 */
	public function getHolderName(): ?string
	{
		return $this->holderName;
	}

	/**
	 * @param string|null $name
	 */
	public function setHolderName(?string $name)
	{
		$this->holderName = $name;
	}

	/**
	 * @return string|null
	 */
	public function getType(): ?string
	{
		return $this->type;
	}
}
