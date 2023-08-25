<?php
namespace EceoPos;

use Illuminate\Support\ServiceProvider;

class EceoPosServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		$this->publishes([
			__DIR__ . '/../config/pos-settings.php' => config_path('pos-settings.php'),
		], 'config');

		config()->set('akbank.client_id', bankSettings('akbank_store_number'));
		config()->set('akbank.username', bankSettings('akbank_api_user'));
		config()->set('akbank.password', bankSettings('akbank_password'));
		config()->set('akbank.store_key', bankSettings('akbank_3d_secure_store_key'));

		config()->set('isbank.client_id', bankSettings('isbank_store_number'));
		config()->set('isbank.username', bankSettings('isbank_api_user'));
		config()->set('isbank.password', bankSettings('isbank_password'));
		config()->set('isbank.store_key', bankSettings('isbank_3d_secure_store_key'));

		config()->set('qnbfinansbank.client_id', bankSettings('qnbfinansbank_merchant_number'));
		config()->set('qnbfinansbank.username', bankSettings('qnbfinansbank_api_user'));
		config()->set('qnbfinansbank.password', bankSettings('qnbfinansbank_password'));
		config()->set('qnbfinansbank.store_key', bankSettings('qnbfinansbank_3d_secure_store_key'));

		config()->set('garanti.client_id', bankSettings('garanti_store_number'));
		config()->set('garanti.username', bankSettings('garanti_api_user'));
		config()->set('garanti.password', bankSettings('garanti_password'));
		config()->set('garanti.store_key', bankSettings('garanti_3d_secure_store_key'));
		config()->set('garanti.terminal_number', bankSettings('garanti_terminal_number'));

		config()->set('yapikredi.client_id', bankSettings('yapikredi_merchant_number'));
		config()->set('yapikredi.terminal_number', bankSettings('yapikredi_terminal_number'));
		config()->set('yapikredi.username', bankSettings('yapikredi_api_user'));
		config()->set('yapikredi.password', bankSettings('yapikredi_password'));
		config()->set('yapikredi.posnet_id', bankSettings('yapikredi_posnet_id'));
		config()->set('yapikredi.enc_key', bankSettings('yapikredi_enc_key'));

	}

	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/pos-settings.php', 'pos-settings');
	}
}