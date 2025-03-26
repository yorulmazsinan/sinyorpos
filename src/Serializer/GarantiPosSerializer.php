<?php

/**
 * @license MIT
 */

namespace SinyorPos\Serializer;

use SinyorPos\Gateways\GarantiPos;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;

class GarantiPosSerializer implements SerializerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $encoder = new XmlEncoder([
            XmlEncoder::ROOT_NODE_NAME => 'GVPSRequest',
            XmlEncoder::ENCODING       => 'UTF-8',
        ]);

        $this->serializer = new Serializer([], [$encoder]);
    }

    /**
     * @inheritDoc
     */
    public static function supports(string $gatewayClass): bool
    {
        return GarantiPos::class === $gatewayClass;
    }

    /**
     * @inheritDoc
     */
    public function encode(array $data, ?string $txType = null): string
    {
        return $this->serializer->encode($data, XmlEncoder::FORMAT);
    }

    /**
     * @inheritDoc
     */
    public function decode(string $data, ?string $txType = null): array
    {
        return $this->serializer->decode($data, XmlEncoder::FORMAT);
    }
}
