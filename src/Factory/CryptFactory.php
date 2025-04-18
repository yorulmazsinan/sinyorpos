<?php
/**
 * @license MIT
 */

namespace SinyorPos\Factory;

use SinyorPos\Crypt\AkbankPosCrypt;
use SinyorPos\Crypt\CryptInterface;
use SinyorPos\Crypt\EstPosCrypt;
use SinyorPos\Crypt\EstV3PosCrypt;
use SinyorPos\Crypt\GarantiPosCrypt;
use SinyorPos\Crypt\InterPosCrypt;
use SinyorPos\Crypt\KuveytPosCrypt;
use SinyorPos\Crypt\NullCrypt;
use SinyorPos\Crypt\PayFlexCPV4Crypt;
use SinyorPos\Crypt\PayForPosCrypt;
use SinyorPos\Crypt\PosNetCrypt;
use SinyorPos\Crypt\PosNetV1PosCrypt;
use SinyorPos\Crypt\ToslaPosCrypt;
use SinyorPos\Gateways\AkbankPos;
use SinyorPos\Gateways\EstPos;
use SinyorPos\Gateways\EstV3Pos;
use SinyorPos\Gateways\GarantiPos;
use SinyorPos\Gateways\InterPos;
use SinyorPos\Gateways\KuveytPos;
use SinyorPos\Gateways\PayFlexCPV4Pos;
use SinyorPos\Gateways\PayForPos;
use SinyorPos\Gateways\PosNet;
use SinyorPos\Gateways\PosNetV1Pos;
use SinyorPos\Gateways\ToslaPos;
use SinyorPos\Gateways\VakifKatilimPos;
use Psr\Log\LoggerInterface;

/**
 * CryptFactory
 */
class CryptFactory
{
    /**
     * @param class-string    $gatewayClass
     * @param LoggerInterface $logger
     *
     * @return CryptInterface
     */
    public static function createGatewayCrypt(string $gatewayClass, LoggerInterface $logger): CryptInterface
    {
        $classMappings = [
            AkbankPos::class       => AkbankPosCrypt::class,
            EstPos::class          => EstPosCrypt::class,
            EstV3Pos::class        => EstV3PosCrypt::class,
            GarantiPos::class      => GarantiPosCrypt::class,
            InterPos::class        => InterPosCrypt::class,
            KuveytPos::class       => KuveytPosCrypt::class,
            PayFlexCPV4Pos::class  => PayFlexCPV4Crypt::class,
            PayForPos::class       => PayForPosCrypt::class,
            PosNet::class          => PosNetCrypt::class,
            PosNetV1Pos::class     => PosNetV1PosCrypt::class,
            ToslaPos::class        => ToslaPosCrypt::class,
            VakifKatilimPos::class => KuveytPosCrypt::class,
        ];

        if (isset($classMappings[$gatewayClass])) {
            return new $classMappings[$gatewayClass]($logger);
        }

        return new NullCrypt($logger);
    }
}
