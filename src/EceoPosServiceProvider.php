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
	}

	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/pos-settings.php', 'pos-settings');
	}
}