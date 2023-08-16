<?php
namespace EceoPos\Factory;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use EceoPos\Client\HttpClient;

class HttpClientFactory
{
	public static function createDefaultHttpClient(): HttpClient
	{
		return new HttpClient(Psr18ClientDiscovery::find(), Psr17FactoryDiscovery::findRequestFactory(), Psr17FactoryDiscovery::findStreamFactory());
	}
}
