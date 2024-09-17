<?php

use Illuminate\Support\Facades\Auth;
use SinyorPos\Factory\CreditCardFactory;
use SinyorPos\Factory\PosFactory;
use SinyorPos\Factory\AccountFactory;
use SinyorPos\Gateways\AbstractGateway;
use SinyorPos\PosInterface;
use SinyorPos\Entity\Card\AbstractCreditCard;
use SinyorPos\Entity\Account\AbstractPosAccount;
use App\Models\Order;
use App\Models\User;
use App\Models\SiteSetting;
use Symfony\Component\EventDispatcher\EventDispatcher;

if (!function_exists('checkCardType')) {
	function checkCardType($number)
	{
		$number = str_replace(' ', '', $number);

		$cardType = [
			'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
			'master' => '/^5[1-5][0-9]{14}$/',
			'amex' => '/^3[47][0-9]{13}$/',
			'troy' => "/^(?:9792|65\d{2}|36|2205)\d{12}$/",
		];

		foreach ($cardType as $key => $value) {
			if (preg_match($value, $number)) {
				$type = $key;
				break;
			} else {
				$type = null;
			}
		}

		return $type;
	}
}
if (!function_exists('getGateway')) {
	function getGateway(AbstractPosAccount $account, \Psr\EventDispatcher\EventDispatcherInterface $eventDispatcher): PosInterface
	{
		try {
			$handler = new \Monolog\Handler\StreamHandler(__DIR__.'/../var/log/pos.log', \Psr\Log\LogLevel::DEBUG);
			$logger = new \Monolog\Logger('pos', [$handler]);

			$pos = PosFactory::createPosGateway($account, [], $eventDispatcher, null, $logger);
			$pos->setTestMode(false);

			return $pos;
		} catch (Exception $e) {
			dd($e);
		}
	}
}
if (!function_exists('createCard')) {
	function createCard(PosInterface $pos, array $card): \SinyorPos\Entity\Card\CreditCardInterface
	{
		try {
			return CreditCardFactory::createForGateway(
				$pos,
				$card['number'],
				$card['year'],
				$card['month'],
				$card['cvc'],
				$card['name'],
				$card['type'] ?? null
			);
		} catch (\SinyorPos\Exceptions\CardTypeRequiredException $e) {
			// bu gateway için kart tipi zorunlu
			dd($e);
		} catch (\SinyorPos\Exceptions\CardTypeNotSupportedException $e) {
			// sağlanan kart tipi bu gateway tarafından desteklenmiyor
			dd($e);
		} catch (\Exception $e) {
			dd($e);
		}
	}
}
if (!function_exists('createPosAccount')) {
	function createPosAccount($bank, $status)
	{
		$config = require config_path('pos-settings.php'); // Ayarları config/pos-settings.php dosyasından çekiyor. Kurulumdan sonra config/pos-settings.php dosyasını düzenleyin.

		if ($bank == 'akbank') {
			$account = AccountFactory::createEstPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				PosInterface::MODEL_NON_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key'],
				PosInterface::LANG_TR
			);
		} elseif ($bank == 'isbank') {
			$account = AccountFactory::createEstPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				PosInterface::MODEL_NON_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key'],
				PosInterface::LANG_TR
			);
		} elseif ($bank == 'qnbfinansbank-payfor') {
			$account = AccountFactory::createPayForAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				PosInterface::MODEL_NON_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key']
			);
		} elseif ($bank == 'garanti') {
			$account = AccountFactory::createGarantiPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				$config['banks'][$bank]['accounts'][$status]['terminal_number'],
				PosInterface::MODEL_NON_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key']
			);
		} elseif ($bank == 'yapikredi') {
			$account = AccountFactory::createPosNetAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['merchant_number'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				$config['banks'][$bank]['accounts'][$status]['terminal_number'],
				$config['banks'][$bank]['accounts'][$status]['posnet_id'],
				PosInterface::MODEL_NON_SECURE,
				$config['banks'][$bank]['accounts'][$status]['enc_key']
			);
		}

		if (isset($account)) {
			return $account;
		} else {
			return null;
		}
	}
}
if (!function_exists('paymentReadiness')) {
	function paymentReadiness($getOrder = false, $getBankSession = false)
	{
		if (Auth::check()) { // Eğer kullanıcı giriş yapmışsa, kullanıcı bilgilerini alıyoruz.
			$user = User::firstWhere('id', Auth::id()) ?? null;
		} elseif(session('buyer_id')) { // Eğer kullanıcı giriş yapmamışsa, kullanıcı bilgilerini "buyer_id"den alıp giriş yaptırıyoruz.
			$user = session('buyer_id') ? User::firstWhere('id', session('buyer_id')) : null;

			Auth::login($user); // Kullanıcıyı otomatik olarak giriş yaptırıyoruz.
		} else { // Sipariş bilgileri yoksa, ulaşılamadığı için ödeme işlemi tamamlanamıyor ve ödeme sayfasına yönlendiriliyor.
			$user = null;
		}

		if (session()->has('order_id')) { // Eğer "order_id" kaydı varsa, bu kaydı kullanıyoruz.
			$orderId = session('order_id'); // "order_id" kaydını değişkene atıyoruz.
		} else { // Eğer "order_id" kaydı yoksa, yeni bir kayıt oluşturuyoruz.
			$orderId = substr(hash('sha256', mt_rand() . microtime()), 0, 18); // "order_id" kaydı için 18 karakterlik bir random string oluşturuyoruz.

			session()->put('order_id', $orderId); // Oluşturduğumuz random stringi "order_id" olarak kaydediyoruz.
		}

		if ($getOrder) { // Eğer "getOrder" parametresi true ise, "order_id" kaydına ait sipariş bilgilerini çekiyoruz.
			$order = Order::where('payment_id', $orderId); // Sipariş bilgilerini alıyoruz.
		}

		if ($getBankSession) { // Eğer "getBankSession" parametresi true ise, banka oturumunu çekiyoruz.
			$bankSession = session()->get('bank'); // Banka bilgilerini alıyoruz.

			if ($bankSession !== 'other' && $bankSession !== '' && $bankSession !== null) { // Eğer banka bilgileri varsa, bu bilgileri kullanıyoruz.
				$bank = session()->get('bank');
			} else {
				$bank = siteSettings('virtualpos');
			}
		}

		if ($bank == 'yapikredi') {
			$currency = 'TL';
		} else {
			$currency = 'TRY';
		}

		return [
			'user' => $user,
			'orderId' => $orderId,
			'order' => $order ?? null,
			'currency' => $currency,
			'bank' => $bank,
		];
	}
}
if (!function_exists('receivePayment')) {
	function receivePayment($orderId, $isUser = true, $userInformations = true)
	{
		$order = Order::where('payment_id', $orderId)->first(); // Sipariş bilgilerini alıyoruz.

		if ($isUser == true) {
			$user = User::find($order->user_id); // Kullanıcı bilgilerini alıyoruz.
			Auth::login($user); // Kullanıcıyı otomatik olarak giriş yaptırıyoruz.
		} else {
			$user = null;
		}

		$eventDispatcher = new EventDispatcher(); // EventDispatcher nesnesini oluşturuyoruz.
		$pos = getGateway(createPosAccount($order->payment_bank, 'production'), $eventDispatcher); // PosGateway nesnesini alıyoruz.

		if ($userInformations == true) {
			$userInformations = json_decode($order->buying_informations, true)['user']; // Kullanıcı bilgilerini alıyoruz.
		} else {
			$userInformations = null;
		}
		$orderInformations = json_decode($order->buying_informations, true)['order']; // Sipariş bilgilerini alıyoruz.
		$cardInformations = json_decode($order->buying_informations, true)['card']; // Kart bilgilerini alıyoruz.

		$card = createCard($pos, $cardInformations); // Kart bilgilerini oluşturuyoruz.

		$pos->prepare($orderInformations, PosInterface::TX_TYPE_PAY_AUTH); // Ödeme için hazırlık yapıyoruz.

		if ($pos->getAccount()->getModel() === PosInterface::MODEL_NON_SECURE && PosInterface::TX_TYPE_PAY_POST_AUTH !== PosInterface::TX_TYPE_PAY_AUTH) {
			$pos->payment($card);
		} else {
			$pos->payment();
		}

		$response = $pos->getResponse(); // Ödeme işlemi sonucunu alıyoruz.
		$yapikrediResponse = json_decode(json_encode([$response][0]), true); // Yapıkredi ödeme sonucunu alıyoruz.

		return [
			'pos' => $pos,
			'order' => $order,
			'userInformations' => $userInformations,
			'orderInformations' => $orderInformations,
			'response' => $response,
			'yapikrediResponse' => $yapikrediResponse,
		];
	}
}