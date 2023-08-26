<?php

use Illuminate\Support\Facades\Auth;
use EceoPos\Factory\AccountFactory;
use EceoPos\Gateways\AbstractGateway;
use App\Models\Order;
use App\Models\User;
use App\Models\SiteSetting;

if (! function_exists('checkCardType')) {
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
if (! function_exists('createPosAccount')) {
	function createPosAccount($bank, $status)
	{
		$config = require config_path('pos-settings.php'); // Ayarları config/pos-settings.php dosyasından çekiyor. Kurulumdan sonra config/pos-settings.php dosyasını düzenleyin.

		if ($bank == 'halkbank') {
			$account = AccountFactory::createESTVirtualPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				AbstractGateway::MODEL_3D_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key'],
				AbstractGateway::LANG_TR
			);
		} elseif ($bank == 'akbank') {
			$account = AccountFactory::createESTVirtualPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				AbstractGateway::MODEL_3D_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key'],
				AbstractGateway::LANG_TR
			);
		} elseif ($bank == 'isbank') {
			$account = AccountFactory::createESTVirtualPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				AbstractGateway::MODEL_3D_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key'],
				AbstractGateway::LANG_TR
			);
		} elseif ($bank == 'qnbfinansbank-payfor') {
			$account = AccountFactory::createPayForVirtualPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				AbstractGateway::MODEL_3D_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key']
			);
		} elseif ($bank == 'garanti') {
			$account = AccountFactory::createGarantiVirtualPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['client_id'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				$config['banks'][$bank]['accounts'][$status]['terminal_number'],
				AbstractGateway::MODEL_3D_SECURE,
				$config['banks'][$bank]['accounts'][$status]['store_key']
			);
		} elseif ($bank == 'yapikredi') {
			$account = AccountFactory::createPosNetVirtualPosAccount(
				$bank,
				$config['banks'][$bank]['accounts'][$status]['merchant_number'],
				$config['banks'][$bank]['accounts'][$status]['username'],
				$config['banks'][$bank]['accounts'][$status]['password'],
				$config['banks'][$bank]['accounts'][$status]['terminal_number'],
				$config['banks'][$bank]['accounts'][$status]['posnet_id'],
				AbstractGateway::MODEL_3D_SECURE,
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
if (! function_exists('paymentReadiness')) {
	function paymentReadiness($getOrder = false, $getBankSession = false)
	{
		if (Auth::check()) { // Eğer kullanıcı giriş yapmışsa, kullanıcı bilgilerini alıyoruz.
			$user = User::firstWhere('id', Auth::id()) ?? null;
		} elseif(session('buyer_id')) { // Eğer kullanıcı giriş yapmamışsa, kullanıcı bilgilerini "buyer_id"den alıp giriş yaptırıyoruz.
			$user = session('buyer_id') ? User::firstWhere('id', session('buyer_id')) : null;

			Auth::login($user); // Kullanıcıyı otomatik olarak giriş yaptırıyoruz.
		} else {
			return back()->with('error', __('Ödeme yapabilmek için giriş yapılı olmanız gerekmektedir.'));
		}

		if (session()->has('order_id')) { // Eğer "order_id" kaydı varsa, bu kaydı kullanıyoruz.
			$orderId = session('order_id'); // "order_id" kaydını değişkene atıyoruz.

			if ($getOrder) { // Eğer "getOrder" parametresi true ise, "order_id" kaydına ait sipariş bilgilerini çekiyoruz.
				$order = Order::where('payment_id', $orderId); // Sipariş bilgilerini alıyoruz.
			}
		} else { // Eğer "order_id" kaydı yoksa, yeni bir kayıt oluşturuyoruz.
			$orderId = substr(hash('sha256', mt_rand() . microtime()), 0, 18); // "order_id" kaydı için 18 karakterlik bir random string oluşturuyoruz.

			session()->put('order_id', $orderId); // Oluşturduğumuz random stringi "order_id" olarak kaydediyoruz.
		}

		if ($getBankSession) { // Eğer "getBankSession" parametresi true ise, banka oturumunu çekiyoruz.
			$bankSession = session()->get('bank'); // Banka bilgilerini alıyoruz.

			if ($bankSession !== 'other' && $bankSession !== '' && $bankSession !== null) { // Eğer banka bilgileri varsa, bu bilgileri kullanıyoruz.
				$bank = session()->get('bank');
			} else {
				$bank = siteSettings('virtualpos');
			}
		}

		return [
			'user' => $user,
			'orderId' => $orderId,
			'order' => $order ?? null,
			'bank' => $bank,
		];
	}
}