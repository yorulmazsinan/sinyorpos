<?php
/**
 * @license MIT
 */

namespace SinyorPos\Factory;

use DomainException;
use SinyorPos\Serializer\AkbankPosSerializer;
use SinyorPos\Serializer\EstPosSerializer;
use SinyorPos\Serializer\GarantiPosSerializer;
use SinyorPos\Serializer\InterPosSerializer;
use SinyorPos\Serializer\KuveytPosSerializer;
use SinyorPos\Serializer\PayFlexCPV4PosSerializer;
use SinyorPos\Serializer\PayFlexV4PosSerializer;
use SinyorPos\Serializer\PayForPosSerializer;
use SinyorPos\Serializer\PosNetSerializer;
use SinyorPos\Serializer\PosNetV1PosSerializer;
use SinyorPos\Serializer\SerializerInterface;
use SinyorPos\Serializer\ToslaPosSerializer;
use SinyorPos\Serializer\VakifKatilimPosSerializer;

/**
 * SerializerFactory
 */
class SerializerFactory
{
    /**
     * @param class-string $gatewayClass
     *
     * @return SerializerInterface
     */
    public static function createGatewaySerializer(string $gatewayClass): SerializerInterface
    {
        /** @var SerializerInterface[] $serializers */
        $serializers = [
            AkbankPosSerializer::class,
            EstPosSerializer::class,
            GarantiPosSerializer::class,
            InterPosSerializer::class,
            KuveytPosSerializer::class,
            PayFlexCPV4PosSerializer::class,
            PayFlexV4PosSerializer::class,
            PayForPosSerializer::class,
            PosNetSerializer::class,
            PosNetV1PosSerializer::class,
            ToslaPosSerializer::class,
            VakifKatilimPosSerializer::class,
        ];

        foreach ($serializers as $serializer) {
            if ($serializer::supports($gatewayClass)) {
                return new $serializer();
            }
        }

        throw new DomainException(\sprintf('Serializer not found for the gateway %s', $gatewayClass));
    }
}
