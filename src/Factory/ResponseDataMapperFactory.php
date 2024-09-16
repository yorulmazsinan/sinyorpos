<?php
/**
 * @license MIT
 */

namespace SinyorPos\Factory;

use DomainException;
use SinyorPos\DataMapper\RequestDataMapper\RequestDataMapperInterface;
use SinyorPos\DataMapper\ResponseDataMapper\AkbankPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\EstPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\GarantiPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\InterPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\KuveytPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PayFlexCPV4PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PayFlexV4PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PayForPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PosNetResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\PosNetV1PosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\ResponseDataMapperInterface;
use SinyorPos\DataMapper\ResponseDataMapper\ToslaPosResponseDataMapper;
use SinyorPos\DataMapper\ResponseDataMapper\VakifKatilimPosResponseDataMapper;
use SinyorPos\Gateways\AkbankPos;
use SinyorPos\Gateways\EstPos;
use SinyorPos\Gateways\EstV3Pos;
use SinyorPos\Gateways\GarantiPos;
use SinyorPos\Gateways\InterPos;
use SinyorPos\Gateways\KuveytPos;
use SinyorPos\Gateways\PayFlexCPV4Pos;
use SinyorPos\Gateways\PayFlexV4Pos;
use SinyorPos\Gateways\PayForPos;
use SinyorPos\Gateways\PosNet;
use SinyorPos\Gateways\PosNetV1Pos;
use SinyorPos\Gateways\ToslaPos;
use SinyorPos\Gateways\VakifKatilimPos;
use Psr\Log\LoggerInterface;

/**
 * ResponseDataMapperFactory
 */
class ResponseDataMapperFactory
{
    /**
     * @param class-string               $gatewayClass
     * @param RequestDataMapperInterface $requestDataMapper
     * @param LoggerInterface            $logger
     *
     * @return ResponseDataMapperInterface
     */
    public static function createGatewayResponseMapper(string $gatewayClass, RequestDataMapperInterface $requestDataMapper, LoggerInterface $logger): ResponseDataMapperInterface
    {
        $classMappings = [
            ToslaPos::class        => ToslaPosResponseDataMapper::class,
            AkbankPos::class       => AkbankPosResponseDataMapper::class,
            EstV3Pos::class        => EstPosResponseDataMapper::class,
            EstPos::class          => EstPosResponseDataMapper::class,
            GarantiPos::class      => GarantiPosResponseDataMapper::class,
            InterPos::class        => InterPosResponseDataMapper::class,
            KuveytPos::class       => KuveytPosResponseDataMapper::class,
            VakifKatilimPos::class => VakifKatilimPosResponseDataMapper::class,
            PayForPos::class       => PayForPosResponseDataMapper::class,
            PosNet::class          => PosNetResponseDataMapper::class,
            PosNetV1Pos::class     => PosNetV1PosResponseDataMapper::class,
            PayFlexV4Pos::class    => PayFlexV4PosResponseDataMapper::class,
            PayFlexCPV4Pos::class  => PayFlexCPV4PosResponseDataMapper::class,
        ];

        if (isset($classMappings[$gatewayClass])) {
            return new $classMappings[$gatewayClass](
                $requestDataMapper->getCurrencyMappings(),
                $requestDataMapper->getTxTypeMappings(),
                $requestDataMapper->getSecureTypeMappings(),
                $logger
            );
        }

        throw new DomainException('unsupported gateway');
    }
}
