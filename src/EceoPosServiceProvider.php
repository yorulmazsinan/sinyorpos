<?php
namespace EceoPos;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;

class EceoPosServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		$this->publishes([
			__DIR__ . '/../config/pos-settings.php' => config_path('pos-settings.php'),
		], 'config');

		$this->app->booted(function () {
			$this->configureBankSettings();
		});
	}

	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/pos-settings.php', 'pos-settings');
	}

	protected function configureBankSettings()
	{
		$bankSettings = function ($name) {
			$value = DB::connection('mysql')->table('site_settings')->where('name', $name)->first();
			return $value ? $value->value : null;
		};

		config([
			'akbank.client_id' => $bankSettings('akbank_store_number'),
			'akbank.username' => $bankSettings('akbank_api_user'),
			'akbank.password' => $bankSettings('akbank_password'),
			'akbank.store_key' => $bankSettings('akbank_3d_secure_store_key'),
			'isbank.client_id' => $bankSettings('isbank_store_number'),
			'isbank.username' => $bankSettings('isbank_api_user'),
			'isbank.password' => $bankSettings('isbank_password'),
			'isbank.store_key' => $bankSettings('isbank_3d_secure_store_key'),
			'qnbfinansbank.client_id' => $bankSettings('qnbfinansbank_merchant_number'),
			'qnbfinansbank.username' => $bankSettings('qnbfinansbank_api_user'),
			'qnbfinansbank.password' => $bankSettings('qnbfinansbank_password'),
			'qnbfinansbank.store_key' => $bankSettings('qnbfinansbank_3d_secure_store_key'),
			'garanti.client_id' => $bankSettings('garanti_store_number'),
			'garanti.username' => $bankSettings('garanti_api_user'),
			'garanti.password' => $bankSettings('garanti_password'),
			'garanti.store_key' => $bankSettings('garanti_3d_secure_store_key'),
			'garanti.terminal_number' => $bankSettings('garanti_terminal_number'),
			'yapikredi.client_id' => $bankSettings('yapikredi_merchant_number'),
			'yapikredi.terminal_number' => $bankSettings('yapikredi_terminal_number'),
			'yapikredi.username' => $bankSettings('yapikredi_api_user'),
			'yapikredi.password' => $bankSettings('yapikredi_password'),
			'yapikredi.posnet_id' => $bankSettings('yapikredi_posnet_id'),
			'yapikredi.enc_key' => $bankSettings('yapikredi_enc_key'),
		]);
	}
}