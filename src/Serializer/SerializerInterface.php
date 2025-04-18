<?php
/**
 * @license MIT
 */

namespace SinyorPos\Serializer;

use SinyorPos\Exceptions\UnsupportedTransactionTypeException;
use SinyorPos\PosInterface;

interface SerializerInterface
{
    /**
     * @param class-string $gatewayClass
     *
     * @return bool
     */
    public static function supports(string $gatewayClass): bool;

    /**
     * @phpstan-param PosInterface::TX_TYPE_* $txType
     *
     * @param array<string, mixed> $data
     * @param string               $txType
     *
     * @return string|array<string, mixed> returns XML/JSON string or $data itself when encoding is not needed
     *
     * @throws UnsupportedTransactionTypeException
     */
    public function encode(array $data, string $txType);

    /**
     * @phpstan-param PosInterface::TX_TYPE_* $txType
     *
     * @param string $data response received from the bank
     * @param string $txType
     *
     * @return array<string, mixed>
     *
     * @throws \RuntimeException
     */
    public function decode(string $data, string $txType): array;
}
