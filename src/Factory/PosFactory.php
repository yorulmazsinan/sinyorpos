<?php
namespace EceoPos\Factory;

use DomainException;
use EceoPos\Client\HttpClient;
use EceoPos\DataMapper\AbstractRequestDataMapper;
use EceoPos\DataMapper\ESTVirtualPosRequestDataMapper;
use EceoPos\DataMapper\VakifBankVirtualPosRequestDataMapper;
use EceoPos\DataMapper\GarantiVirtualPosRequestDataMapper;
use EceoPos\DataMapper\InterVirtualPosRequestDataMapper;
use EceoPos\DataMapper\KuveytVirtualPosRequestDataMapper;
use EceoPos\DataMapper\PayForVirtualPosRequestDataMapper;
use EceoPos\DataMapper\PosNetVirtualPosRequestDataMapper;
use EceoPos\Entity\Account\AbstractPosAccount;
use EceoPos\Exceptions\BankClassNullException;
use EceoPos\Exceptions\BankNotFoundException;
use EceoPos\Gateways\ESTVirtualPos;
use EceoPos\Gateways\GarantiVirtualPos;
use EceoPos\Gateways\InterVirtualPos;
use EceoPos\Gateways\KuveytVirtualPos;
use EceoPos\Gateways\PayForVirtualPos;
use EceoPos\Gateways\PosNetVirtualPos;
use EceoPos\Gateways\VakifBankVirtualPos;
use EceoPos\PosInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * PosFactory
 */
class PosFactory
{
	/**
	 * @param AbstractPosAccount $posAccount
	 * @param array|string|null $config ; yapılandırma yolu veya yapılandırma dizisi
	 * @param HttpClient|null $client
	 * @param LoggerInterface|null $logger
	 * @return PosInterface
	 * @throws BankClassNullException
	 * @throws BankNotFoundException
	 */
	public static function createPosGateway(AbstractPosAccount $posAccount, $config = null, ?HttpClient $client = null, ?LoggerInterface $logger = null): PosInterface
	{
		if (!$logger) {
			$logger = new NullLogger();
		}
		if (!$client) {
			$client = HttpClientFactory::createDefaultHttpClient();
		}
		if (is_string($config)) {
			$config = require $config;
		} elseif (empty($config)) {
			$config = require __DIR__ . '/../../config/pos-settings.php';
		}
		// Banka API'si kontrolü:
		if (!array_key_exists($posAccount->getBank(), $config['banks'])) {
			throw new BankNotFoundException();
		}
		// Ağ geçidi sınıfı:
		$class = $config['banks'][$posAccount->getBank()]['class'];
		if (!$class) {
			throw new BankClassNullException();
		}
		$currencies = [];
		if (isset($config['currencies'])) {
			$currencies = $config['currencies'];
		}
		$logger->debug('Banka İçin Ağ Geçidi Oluşturma', ['bank' => $posAccount->getBank()]);
		// Banka sınıfı nesnesi oluşturur:
		return new $class($config['banks'][$posAccount->getBank()], $posAccount, self::getGatewayMapper($class, $currencies), $client, $logger);
	}

	/**
	 * @param string $gatewayClass
	 * @param array $currencies
	 * @return AbstractRequestDataMapper
	 */
	public static function getGatewayMapper(string $gatewayClass, array $currencies = []): AbstractRequestDataMapper
	{
		switch ($gatewayClass) {
			case ESTVirtualPos::class:
				return new ESTVirtualPosRequestDataMapper($currencies);
			case GarantiVirtualPos::class:
				return new GarantiVirtualPosRequestDataMapper($currencies);
			case InterVirtualPos::class:
				return new InterVirtualPosRequestDataMapper($currencies);
			case KuveytVirtualPos::class:
				return new KuveytVirtualPosRequestDataMapper($currencies);
			case PayForVirtualPos::class:
				return new PayForVirtualPosRequestDataMapper($currencies);
			case PosNetVirtualPos::class:
				return new PosNetVirtualPosRequestDataMapper($currencies);
			case VakifBankVirtualPos::class:
				return new VakifBankVirtualPosRequestDataMapper($currencies);
		}
		throw new DomainException('Desteklenmeyen ağ geçidi!');
	}
}
