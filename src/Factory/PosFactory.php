<?php

/**
 * @license MIT
 */
namespace SinyorPos\Factory;

use DomainException;
use SinyorPos\Client\HttpClient;
use SinyorPos\Crypt\CryptInterface;
use SinyorPos\Crypt\EstPosCrypt;
use SinyorPos\Crypt\EstV3PosCrypt;
use SinyorPos\Crypt\GarantiPosCrypt;
use SinyorPos\Crypt\InterPosCrypt;
use SinyorPos\Crypt\KuveytPosCrypt;
use SinyorPos\Crypt\PayForPosCrypt;
use SinyorPos\Crypt\PosNetV1PosCrypt;
use SinyorPos\Crypt\PosNetCrypt;
use SinyorPos\Crypt\PayFlexCPV4Crypt;
use SinyorPos\DataMapper\AbstractRequestDataMapper;
use SinyorPos\DataMapper\EstPosRequestDataMapper;
use SinyorPos\DataMapper\EstV3PosRequestDataMapper;
use SinyorPos\DataMapper\GarantiPosRequestDataMapper;
use SinyorPos\DataMapper\InterPosRequestDataMapper;
use SinyorPos\DataMapper\KuveytPosRequestDataMapper;
use SinyorPos\DataMapper\PayFlexCPV4PosRequestDataMapper;
use SinyorPos\DataMapper\PayFlexV4PosRequestDataMapper;
use SinyorPos\DataMapper\PayForPosRequestDataMapper;
use SinyorPos\DataMapper\PosNetV1PosRequestDataMapper;
use SinyorPos\DataMapper\PosNetRequestDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\AbstractResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\EstPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\GarantiPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\InterPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\KuveytPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PayFlexCPV4PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PayFlexV4PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PayForPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PosNetResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PosNetV1PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\VakifBankCPPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\VakifBankPosResponseDataMapper;
use SinyorPos\DataMapper\VakifBankCPPosRequestDataMapper;
use SinyorPos\DataMapper\VakifBankPosRequestDataMapper;
use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Exceptions\BankClassNullException;
use SinyorPos\Exceptions\BankNotFoundException;
use SinyorPos\Gateways\EstPos;
use SinyorPos\Gateways\EstV3Pos;
use SinyorPos\Gateways\GarantiPos;
use SinyorPos\Gateways\InterPos;
use SinyorPos\Gateways\KuveytPos;
use SinyorPos\Gateways\PayFlexCPV4Pos;
use SinyorPos\Gateways\PayFlexV4Pos;
use SinyorPos\Gateways\PayForPos;
use SinyorPos\Gateways\PosNet;
use SinyorPos\Gateways\PosNetV1Pos;
use SinyorPos\Gateways\VakifBankCPPos;
use SinyorPos\Gateways\VakifBankPos;
use SinyorPos\PosInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * PosFactory
 */
class PosFactory
{
	/**
	 * @param AbstractPosAccount   $posAccount
	 * @param array|string|null    $config config path or config array
	 * @param HttpClient|null      $client
	 * @param LoggerInterface|null $logger
	 *
	 * @return PosInterface
	 *
	 * @throws BankClassNullException
	 * @throws BankNotFoundException
	 */
	public static function createPosGateway(
		AbstractPosAccount $posAccount,
		                   $config = null,
		?HttpClient        $client = null,
		?LoggerInterface   $logger = null
	): PosInterface
	{
		if ($logger === null) {
			$logger = new NullLogger();
		}

		if ($client === null) {
			$client = HttpClientFactory::createDefaultHttpClient();
		}

		if (is_string($config)) {
			$config = require $config;
		} elseif (empty($config)) {
			$config = require __DIR__.'/../../config/pos.php';
		}

		// Bank API Exist
		if (!array_key_exists($posAccount->getBank(), $config['banks'])) {
			throw new BankNotFoundException();
		}

		/** @var class-string|null $class Gateway Class */
		$class = $config['banks'][$posAccount->getBank()]['class'] ?? null;

		if (null === $class) {
			throw new BankClassNullException();
		}

		$currencies = [];
		if (isset($config['currencies'])) {
			$currencies = $config['currencies'];
		}

		$logger->debug('creating gateway for bank', ['bank' => $posAccount->getBank()]);

		$crypt              = self::getGatewayCrypt($class, $logger);
		$requestDataMapper  = self::getGatewayRequestMapper($class, $currencies, $crypt);
		$responseDataMapper = self::getGatewayResponseMapper($class, $requestDataMapper, $logger);

		// Create Bank Class Instance
		return new $class(
			$config['banks'][$posAccount->getBank()],
			$posAccount,
			$requestDataMapper,
			$responseDataMapper,
			$client,
			$logger
		);
	}

	/**
	 * @param class-string          $gatewayClass
	 * @param array<string, string> $currencies
	 * @param CryptInterface|null   $crypt
	 *
	 * @return AbstractRequestDataMapper
	 */
	public static function getGatewayRequestMapper(string $gatewayClass, array $currencies = [], ?CryptInterface $crypt = null): AbstractRequestDataMapper
	{
		$classMappings = [
			EstPos::class         => EstPosRequestDataMapper::class,
			EstV3Pos::class       => EstV3PosRequestDataMapper::class,
			GarantiPos::class     => GarantiPosRequestDataMapper::class,
			InterPos::class       => InterPosRequestDataMapper::class,
			KuveytPos::class      => KuveytPosRequestDataMapper::class,
			PayForPos::class      => PayForPosRequestDataMapper::class,
			PosNet::class         => PosNetRequestDataMapper::class,
			PosNetV1Pos::class    => PosNetV1PosRequestDataMapper::class,
			PayFlexCPV4Pos::class => PayFlexCPV4PosRequestDataMapper::class,
			VakifBankCPPos::class => VakifBankCPPosRequestDataMapper::class,
		];
		if (isset($classMappings[$gatewayClass])) {
			if (null === $crypt) {
				throw new \InvalidArgumentException(sprintf('Gateway %s requires Crypt instance', $gatewayClass));
			}

			return new $classMappings[$gatewayClass]($crypt, $currencies);
		}

		if ($gatewayClass === VakifBankPos::class) {
			return new VakifBankPosRequestDataMapper(null, $currencies);
		}

		if ($gatewayClass === PayFlexV4Pos::class) {
			return new PayFlexV4PosRequestDataMapper(null, $currencies);
		}

		throw new DomainException('unsupported gateway');
	}

	/**
	 * @param class-string              $gatewayClass
	 * @param AbstractRequestDataMapper $requestDataMapper
	 * @param LoggerInterface           $logger
	 *
	 * @return AbstractResponseDataMapper
	 */
	public static function getGatewayResponseMapper(string $gatewayClass, AbstractRequestDataMapper $requestDataMapper, LoggerInterface $logger): AbstractResponseDataMapper
	{
		$classMappings = [
			EstV3Pos::class       => EstPosResponseDataMapper::class,
			EstPos::class         => EstPosResponseDataMapper::class,
			GarantiPos::class     => GarantiPosResponseDataMapper::class,
			InterPos::class       => InterPosResponseDataMapper::class,
			KuveytPos::class      => KuveytPosResponseDataMapper::class,
			PayForPos::class      => PayForPosResponseDataMapper::class,
			PosNet::class         => PosNetResponseDataMapper::class,
			PosNetV1Pos::class    => PosNetV1PosResponseDataMapper::class,
			PayFlexV4Pos::class   => PayFlexV4PosResponseDataMapper::class,
			VakifBankPos::class   => VakifBankPosResponseDataMapper::class,
			VakifBankCPPos::class => VakifBankCPPosResponseDataMapper::class,
			PayFlexCPV4Pos::class => PayFlexCPV4PosResponseDataMapper::class,
		];

		if (isset($classMappings[$gatewayClass])) {
			return new $classMappings[$gatewayClass](
				$requestDataMapper->getCurrencyMappings(),
				$requestDataMapper->getTxTypeMappings(),
				$logger
			);
		}

		throw new DomainException('unsupported gateway');
	}

	/**
	 * @param class-string    $gatewayClass
	 * @param LoggerInterface $logger
	 *
	 * @return CryptInterface|null
	 */
	public static function getGatewayCrypt(string $gatewayClass, LoggerInterface $logger): ?CryptInterface
	{
		$classMappings = [
			EstV3Pos::class       => EstV3PosCrypt::class,
			EstPos::class         => EstPosCrypt::class,
			GarantiPos::class     => GarantiPosCrypt::class,
			InterPos::class       => InterPosCrypt::class,
			KuveytPos::class      => KuveytPosCrypt::class,
			PayForPos::class      => PayForPosCrypt::class,
			PosNet::class         => PosNetCrypt::class,
			PosNetV1Pos::class    => PosNetV1PosCrypt::class,
			VakifBankCPPos::class => PayFlexCPV4Crypt::class,
			PayFlexCPV4Pos::class => PayFlexCPV4Crypt::class,
		];

		if (isset($classMappings[$gatewayClass])) {
			return new $classMappings[$gatewayClass]($logger);
		}

		return null;
	}
}
