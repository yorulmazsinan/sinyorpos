<?php

/**
 * @license MIT
 */

namespace SinyorPos\Factory;

use DomainException;
use SinyorPos\Crypt\CryptInterface;
use SinyorPos\DataMapper\RequestDataMapper\AkbankPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\EstPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\EstV3PosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\GarantiPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\InterPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\KuveytPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\ParamPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\PayFlexCPV4PosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\PayFlexV4PosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\PayForPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\PosNetRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\PosNetV1PosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\RequestDataMapperInterface;
use SinyorPos\DataMapper\RequestDataMapper\ToslaPosRequestDataMapper;
use SinyorPos\DataMapper\RequestDataMapper\VakifKatilimPosRequestDataMapper;
use SinyorPos\Gateways\AkbankPos;
use SinyorPos\Gateways\EstPos;
use SinyorPos\Gateways\EstV3Pos;
use SinyorPos\Gateways\GarantiPos;
use SinyorPos\Gateways\InterPos;
use SinyorPos\Gateways\KuveytPos;
use SinyorPos\Gateways\ParamPos;
use SinyorPos\Gateways\PayFlexCPV4Pos;
use SinyorPos\Gateways\PayFlexV4Pos;
use SinyorPos\Gateways\PayForPos;
use SinyorPos\Gateways\PosNet;
use SinyorPos\Gateways\PosNetV1Pos;
use SinyorPos\Gateways\ToslaPos;
use SinyorPos\Gateways\VakifKatilimPos;
use SinyorPos\PosInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * RequestDataMapperFactory
 */
class RequestDataMapperFactory
{
    /**
     * @param class-string                            $gatewayClass
     * @param EventDispatcherInterface                $eventDispatcher
     * @param CryptInterface                          $crypt
     * @param array<PosInterface::CURRENCY_*, string> $currencies
     *
     * @return RequestDataMapperInterface
     */
    public static function createGatewayRequestMapper(string $gatewayClass, EventDispatcherInterface $eventDispatcher, CryptInterface $crypt, array $currencies = []): RequestDataMapperInterface
    {
        $classMappings = [
            AkbankPos::class       => AkbankPosRequestDataMapper::class,
            EstPos::class          => EstPosRequestDataMapper::class,
            EstV3Pos::class        => EstV3PosRequestDataMapper::class,
            GarantiPos::class      => GarantiPosRequestDataMapper::class,
            InterPos::class        => InterPosRequestDataMapper::class,
            KuveytPos::class       => KuveytPosRequestDataMapper::class,
            ParamPos::class        => ParamPosRequestDataMapper::class,
            PayFlexCPV4Pos::class  => PayFlexCPV4PosRequestDataMapper::class,
            PayFlexV4Pos::class    => PayFlexV4PosRequestDataMapper::class,
            PayForPos::class       => PayForPosRequestDataMapper::class,
            PosNet::class          => PosNetRequestDataMapper::class,
            PosNetV1Pos::class     => PosNetV1PosRequestDataMapper::class,
            ToslaPos::class        => ToslaPosRequestDataMapper::class,
            VakifKatilimPos::class => VakifKatilimPosRequestDataMapper::class,
        ];
        if (isset($classMappings[$gatewayClass])) {
            return new $classMappings[$gatewayClass]($eventDispatcher, $crypt, $currencies);
        }

        throw new DomainException(\sprintf('Request data mapper not found for the gateway %s', $gatewayClass));
    }
}
