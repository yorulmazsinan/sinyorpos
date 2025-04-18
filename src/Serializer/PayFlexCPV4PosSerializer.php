<?php
/**
 * @license MIT
 */

namespace SinyorPos\Serializer;

use SinyorPos\Exceptions\UnsupportedTransactionTypeException;
use SinyorPos\Gateways\PayFlexCPV4Pos;
use SinyorPos\PosInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Serializer;

class PayFlexCPV4PosSerializer implements SerializerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $encoder = new XmlEncoder([
            XmlEncoder::ROOT_NODE_NAME             => 'VposRequest',
            XmlEncoder::ENCODING                   => 'UTF-8',
            XmlEncoder::ENCODER_IGNORED_NODE_TYPES => [
                XML_PI_NODE,
            ],
        ]);

        $this->serializer = new Serializer([], [$encoder]);
    }

    /**
     * @inheritDoc
     */
    public static function supports(string $gatewayClass): bool
    {
        return PayFlexCPV4Pos::class === $gatewayClass;
    }

    /**
     * @inheritDoc
     */
    public function encode(array $data, string $txType)
    {
        if (PosInterface::TX_TYPE_HISTORY === $txType || PosInterface::TX_TYPE_ORDER_HISTORY === $txType || PosInterface::TX_TYPE_STATUS === $txType) {
            throw new UnsupportedTransactionTypeException(
                \sprintf('Serialization of the transaction %s is not supported', $txType)
            );
        }

        $supportedTxTypes = [
            PosInterface::TX_TYPE_REFUND,
            PosInterface::TX_TYPE_REFUND_PARTIAL,
            PosInterface::TX_TYPE_CANCEL,
            PosInterface::TX_TYPE_CUSTOM_QUERY,
        ];

        if (\in_array($txType, $supportedTxTypes, true)) {
            return $this->serializer->encode($data, XmlEncoder::FORMAT);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function decode(string $data, ?string $txType = null): array
    {
        try {
            return $this->serializer->decode($data, XmlEncoder::FORMAT);
        } catch (NotEncodableValueException $notEncodableValueException) {
            if ($this->isHTML($data)) {
                // if something wrong server responds with HTML content
                throw new \RuntimeException($data, $notEncodableValueException->getCode(), $notEncodableValueException);
            }
        }

        throw $notEncodableValueException;
    }

    /**
     * must be called after making sure that $str does not contain XML string.
     * Because for XML strings it will also return true.
     *
     * @param string $str
     *
     * @return bool
     */
    private function isHTML(string $str): bool
    {
        return $str !== \strip_tags($str);
    }
}
