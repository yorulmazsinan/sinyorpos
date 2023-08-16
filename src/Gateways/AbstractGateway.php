<?php
namespace EceoPos\Gateways;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use EceoPos\Client\HttpClient;
use EceoPos\DataMapper\AbstractRequestDataMapper;
use EceoPos\Entity\Account\AbstractPosAccount;
use EceoPos\Entity\Card\AbstractCreditCard;
use EceoPos\Exceptions\UnsupportedPaymentModelException;
use EceoPos\Exceptions\UnsupportedTransactionTypeException;
use EceoPos\PosInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

abstract class AbstractGateway implements PosInterface
{
	use LoggerAwareTrait;

	public const LANG_TR = 'tr';
	public const LANG_EN = 'en';
	public const TX_PAY = 'pay';
	public const TX_PRE_PAY = 'pre';
	public const TX_POST_PAY = 'post';
	public const TX_CANCEL = 'cancel';
	public const TX_REFUND = 'refund';
	public const TX_STATUS = 'status';
	public const TX_HISTORY = 'history';
	public const MODEL_3D_SECURE = '3d';
	public const MODEL_3D_PAY = '3d_pay';
	public const MODEL_3D_HOST = '3d_host';
	public const MODEL_NON_SECURE = 'regular';
	protected const HASH_ALGORITHM = 'sha1';
	protected const HASH_SEPARATOR = '';
	protected $cardTypeMapping = [];
	/** @var array */
	private $config;
	/** @var AbstractPosAccount */
	protected $account;
	/** @var AbstractCreditCard */
	protected $card;
	/**
	 * İşlem Tipi
	 * @var string
	 */
	protected $type;
	/**
	 * Yinelenen Sipariş Sıklığı Eşlemesi
	 * @var array
	 */
	protected $recurringOrderFrequencyMapping = [];
	/**
	 * @var object
	 */
	protected $order;
	/**
	 * İşlenmiş Yanıt Verileri
	 * @var object
	 */
	protected $response;
	/**
	 * Ham Yanıt Verileri
	 * @var object
	 */
	protected $data;
	/** @var HttpClient */
	protected $client;
	/** @var AbstractRequestDataMapper */
	protected $requestDataMapper;
	private $testMode = false;

	public function __construct(array $config, AbstractPosAccount $account, AbstractRequestDataMapper $requestDataMapper, HttpClient $client, LoggerInterface $logger)
	{
		$this->requestDataMapper = $requestDataMapper;
		$this->cardTypeMapping = $requestDataMapper->getCardTypeMapping();
		$this->recurringOrderFrequencyMapping = $requestDataMapper->getRecurringOrderFrequencyMapping();
		$this->config = $config;
		$this->account = $account;
		$this->client = $client;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 * @return self
	 */
	public function prepare(array $order, string $txType, $card = null)
	{
		$this->setTxType($txType);
		switch ($txType) {
			case self::TX_PAY:
			case self::TX_PRE_PAY:
				$this->order = $this->preparePaymentOrder($order);
				break;
			case self::TX_POST_PAY:
				$this->order = $this->preparePostPaymentOrder($order);
				break;
			case self::TX_CANCEL:
				$this->order = $this->prepareCancelOrder($order);
				break;
			case self::TX_REFUND:
				$this->order = $this->prepareRefundOrder($order);
				break;
			case self::TX_STATUS:
				$this->order = $this->prepareStatusOrder($order);
				break;
			case self::TX_HISTORY:
				$this->order = $this->prepareHistoryOrder($order);
				break;
		}
		$this->logger->log(LogLevel::DEBUG, 'Ağ geçidi ve sipariş hazırlandı.', [$this->order]);
		$this->card = $card;
		return $this;
	}

	/**
	 * @return object
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @return array
	 */
	public function getCurrencies(): array
	{
		return $this->requestDataMapper->getCurrencyMappings();
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @return AbstractPosAccount
	 */
	abstract public function getAccount();

	/**
	 * @return AbstractCreditCard|null
	 */
	public function getCard(): ?AbstractCreditCard
	{
		return $this->card;
	}

	/**
	 * @param AbstractCreditCard|null $card
	 */
	public function setCard(?AbstractCreditCard $card)
	{
		$this->card = $card;
	}

	/**
	 * @return object
	 */
	public function getOrder()
	{
		return $this->order;
	}

	public function createXML(array $nodes, string $encoding = 'UTF-8', bool $ignorePiNode = false): string
	{
		$rootNodeName = array_keys($nodes)[0];
		$encoder = new XmlEncoder();
		$context = [XmlEncoder::ROOT_NODE_NAME => $rootNodeName, XmlEncoder::ENCODING => $encoding];
		if ($ignorePiNode) {
			$context[XmlEncoder::ENCODER_IGNORED_NODE_TYPES] = [XML_PI_NODE];
		}
		return $encoder->encode($nodes[$rootNodeName], 'xml', $context);
	}

	public function printData($data): ?string
	{
		if ((is_object($data) || is_array($data)) && !count((array)$data)) {
			$data = null;
		}
		return (string)$data;
	}

	/**
	 * Başarılı mı?
	 * @return bool
	 */
	public function isSuccess(): bool
	{
		return isset($this->response->status) && 'approved' === $this->response->status;
	}

	public function isError(): bool
	{
		return !$this->isSuccess();
	}

	/**
	 * XML dizesini nesneye çevirir.
	 * @param string $data
	 * @return object
	 */
	public function XMLStringToObject($data)
	{
		$encoder = new XmlEncoder();
		$xml = $encoder->decode($data, 'xml');
		return (object)json_decode(json_encode($xml));
	}

	/**
	 * @return string
	 */
	public function getApiURL(): string
	{
		return $this->config['urls'][$this->getModeInWord()];
	}

	/**
	 * @return string
	 */
	public function get3DGatewayURL(): string
	{
		return $this->config['urls']['gateway'][$this->getModeInWord()];
	}

	/**
	 * @return string|null
	 */
	public function get3DHostGatewayURL(): ?string
	{
		return isset($this->config['urls']['gateway_3d_host'][$this->getModeInWord()]) ? $this->config['urls']['gateway_3d_host'][$this->getModeInWord()] : null;
	}

	/**
	 * @return bool
	 */
	public function testMode(): bool
	{
		return $this->testMode;
	}

	/**
	 * @param string $txType
	 * @throws UnsupportedTransactionTypeException
	 */
	public function setTxType(string $txType)
	{
		$this->requestDataMapper->mapTxType($txType);
		$this->type = $txType;
		$this->logger->log(LogLevel::DEBUG, 'İşlem türünü ayarla!', [$txType]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function payment($card = null)
	{
		$request = Request::createFromGlobals();
		$this->card = $card;
		$model = $this->account->getModel();
		$this->logger->log(LogLevel::DEBUG, 'Ödeme Arandı', ['card_provided' => (bool)$this->card, 'model' => $model]);
		if (self::MODEL_NON_SECURE === $model) {
			$this->makeRegularPayment();
		} elseif (self::MODEL_3D_SECURE === $model) {
			$this->make3DPayment($request);
		} elseif (self::MODEL_3D_PAY === $model) {
			$this->make3DPayPayment($request);
		} elseif (self::MODEL_3D_HOST === $model) {
			$this->make3DHostPayment($request);
		} else {
			$this->logger->log(LogLevel::ERROR, 'Desteklenmeyen Ödeme Modeli', ['model' => $model]);
			throw new UnsupportedPaymentModelException();
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeRegularPayment()
	{
		$this->logger->log(LogLevel::DEBUG, 'Ödeme Yapmak', [
			'model'   => $this->account->getModel(),
			'tx_type' => $this->type,
		]);
		$contents = '';
		if (in_array($this->type, [self::TX_PAY, self::TX_PRE_PAY])) {
			$contents = $this->createRegularPaymentXML();
		} elseif (self::TX_POST_PAY === $this->type) {
			$contents = $this->createRegularPostXML();
		}
		$bankResponse = $this->send($contents);
		$this->response = (object)$this->mapPaymentResponse($bankResponse);
		return $this;
	}

	public function refund()
	{
		$xml = $this->createRefundXML();
		$bankResponse = $this->send($xml);
		$this->response = $this->mapRefundResponse($bankResponse);
		return $this;
	}

	public function cancel()
	{
		$xml = $this->createCancelXML();
		$bankResponse = $this->send($xml);
		$this->response = $this->mapCancelResponse($bankResponse);
		return $this;
	}

	public function status()
	{
		$xml = $this->createStatusXML();
		$bankResponse = $this->send($xml);
		$this->response = $this->mapStatusResponse($bankResponse);
		return $this;
	}

	public function history(array $meta)
	{
		$xml = $this->createHistoryXML($meta);
		$bankResponse = $this->send($xml);
		$this->response = $this->mapHistoryResponse($bankResponse);
		return $this;
	}

	/**
	 * @param bool $testMode
	 * @return $this
	 */
	public function setTestMode(bool $testMode): self
	{
		$this->testMode = $testMode;
		$this->requestDataMapper->setTestMode($testMode);
		$this->logger->log(LogLevel::DEBUG, 'Anahtarlama Modu', ['mode' => $this->getModeInWord()]);
		return $this;
	}

	/**
	 * @param string $period
	 * @return string
	 */
	public function mapRecurringFrequency(string $period): string
	{
		return $this->recurringOrderFrequencyMapping[$period] ?? $period;
	}

	/**
	 * @return array
	 */
	public function getCardTypeMapping(): array
	{
		return $this->cardTypeMapping;
	}

	/**
	 * @return string[]
	 */
	public function getLanguages(): array
	{
		return [self::LANG_TR, self::LANG_EN];
	}

	/**
	 * Düzenli ödeme için XML oluşturur.
	 * @return string
	 */
	abstract public function createRegularPaymentXML();

	/**
	 * Düzenli ödeme sonrası işlem için XML oluşturur.
	 * @return string
	 */
	abstract public function createRegularPostXML();

	/**
	 * Tarih sorgulaması için XML dizesi oluşturur.
	 * @param array $customQueryData
	 * @return string
	 */
	abstract public function createHistoryXML($customQueryData);

	/**
	 * Sipariş durumu sorgulaması için XML dizesi oluşturur.
	 * @return mixed
	 */
	abstract public function createStatusXML();

	/**
	 * Sipariş iptal işlemi için XML dizesi oluşturur.
	 * @return string
	 */
	abstract public function createCancelXML();

	/**
	 * Sipariş iade işlemi için XML dizesi oluşturur.
	 * @return mixed
	 */
	abstract public function createRefundXML();

	/**
	 * 3D Ödeme için XML oluşturur.
	 * @param $responseData
	 * @return string|array
	 */
	abstract public function create3DPaymentXML($responseData);

	/**
	 * 3D ödeme için gerekli olan form verilerini ve anahtar değerleri döndürür.
	 * @return array
	 */
	abstract public function get3DFormData(): array;

	/**
	 * Ödeme talebi için siparişi hazırlar.
	 * @param array $order
	 * @return object
	 */
	abstract protected function preparePaymentOrder(array $order);

	/**
	 * TX_POST_PAY tipindeki talep için siparişi hazırlar.
	 * @param array $order
	 * @return object
	 */
	abstract protected function preparePostPaymentOrder(array $order);

	/**
	 * Sipariş durumu talebi için siparişi hazırlar.
	 * @param array $order
	 * @return object
	 */
	abstract protected function prepareStatusOrder(array $order);

	/**
	 * Geçmiş siparişler talebi için siparişi hazırlar.
	 * @param array $order
	 * @return object
	 */
	abstract protected function prepareHistoryOrder(array $order);

	/**
	 * İptal talebi için siparişi hazırlar.
	 * @param array $order
	 * @return object
	 */
	abstract protected function prepareCancelOrder(array $order);

	/**
	 * İade talebi için siparişi hazırlar.
	 * @param array $order
	 * @return object
	 */
	abstract protected function prepareRefundOrder(array $order);

	/**
	 * @param array $raw3DAuthResponseData ; 3D kimlik doğrulamasından dönen yanıt.
	 * @param object|array $rawPaymentResponseData
	 * @return object
	 */
	abstract protected function map3DPaymentData($raw3DAuthResponseData, $rawPaymentResponseData);

	/**
	 * @param array $raw3DAuthResponseData ; 3D kimlik doğrulamasından dönen yanıt
	 * @return object
	 */
	abstract protected function map3DPayResponseData($raw3DAuthResponseData);

	/**
	 * Düzenli ödeme yanıtı verilerini işler.
	 * @param object|array $responseData
	 * @return array
	 */
	abstract protected function mapPaymentResponse($responseData): array;

	/**
	 * @param $rawResponseData
	 * @return object
	 */
	abstract protected function mapRefundResponse($rawResponseData);

	/**
	 * @param $rawResponseData
	 * @return object
	 */
	abstract protected function mapCancelResponse($rawResponseData);

	/**
	 * @param object $rawResponseData
	 * @return object
	 */
	abstract protected function mapStatusResponse($rawResponseData);

	/**
	 * @param object $rawResponseData
	 * @return mixed
	 */
	abstract protected function mapHistoryResponse($rawResponseData);

	/**
	 * Ödemedeki varsayılan yanıt verilerini döndürür.
	 * @return array
	 */
	protected function getDefaultPaymentResponse(): array
	{
		return [
			'id'               => null,
			'order_id'         => null,
			'trans_id'         => null,
			'transaction_type' => $this->type,
			'transaction'      => empty($this->type) ? null : $this->requestDataMapper->mapTxType($this->type),
			'auth_code'        => null,
			'host_ref_num'     => null,
			'proc_return_code' => null,
			'code'             => null,
			'status'           => 'declined',
			'status_detail'    => null,
			'error_code'       => null,
			'error_message'    => null,
			'response'         => null,
			'all'              => null,
		];
	}

	/**
	 * @param string $str
	 * @return bool
	 */
	protected function isHTML($str): bool
	{
		return $str !== strip_tags($str);
	}

	/**
	 * @param string $str
	 * @return string
	 */
	protected function hashString(string $str): string
	{
		return base64_encode(hash(static::HASH_ALGORITHM, $str, true));
	}

	/**
	 * Aşağıdaki iki dizinin ortak anahtarı varsa boş olmayan dizinin değerini tercih eder.
	 * Her iki dizi de aynı anahtar için boş olmayan değerlere sahipse, $arr2 değeri tercih edilir.
	 * @param array $arr1
	 * @param array $arr2
	 * @return array
	 */
	protected function mergeArraysPreferNonNullValues(array $arr1, array $arr2): array
	{
		$resultArray = array_diff_key($arr1, $arr2) + array_diff_key($arr2, $arr1);
		$commonArrayKeys = array_keys(array_intersect_key($arr1, $arr2));
		foreach ($commonArrayKeys as $key) {
			$resultArray[$key] = $arr2[$key] ? : $arr1[$key];
		}
		return $resultArray;
	}

	/**
	 * XML dizesini diziye dönüştürür.
	 * @param string $data
	 * @param array $context
	 * @return array
	 */
	protected function XMLStringToArray(string $data, array $context = []): array
	{
		$encoder = new XmlEncoder();
		return $encoder->decode($data, 'xml', $context);
	}

	/**
	 * Bankadan gelen boş string değerlerini null değerine dönüştürür.
	 * @param string|object|array $data
	 * @return string|array
	 */
	protected function emptyStringsToNull($data)
	{
		$result = [];
		if (is_string($data)) {
			$result = '' === $data ? null : $data;
		} elseif (is_numeric($data)) {
			$result = $data;
		} elseif (is_array($data) || is_object($data)) {
			foreach ($data as $key => $value) {
				$result[$key] = self::emptyStringsToNull($value);
			}
		}
		return $result;
	}

	/**
	 * Dönüş değerlerini yapılandırma dosyasında bir anahtar olarak kullanılır.
	 * @return string
	 */
	private function getModeInWord(): string
	{
		return !$this->testMode() ? 'production' : 'test';
	}
}
