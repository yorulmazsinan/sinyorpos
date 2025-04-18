<?php
namespace SinyorPos;

use Illuminate\Support\ServiceProvider;

class SinyorPosServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		$this->publishes([
			__DIR__ . '/../config/pos-settings.php' => config_path('pos-settings.php'),
		], 'config');
	}

	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/pos-settings.php', 'pos-settings');
	}
}