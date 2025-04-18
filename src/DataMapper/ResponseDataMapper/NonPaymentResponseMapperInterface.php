<?php
/**
 * @license MIT
 */

namespace SinyorPos\DataMapper\ResponseDataMapper;

interface NonPaymentResponseMapperInterface
{
    /**
     * @param array<string, string> $rawResponseData
     *
     * @return array<string, string>
     */
    public function mapRefundResponse(array $rawResponseData): array;

    /**
     * @param array<string, string> $rawResponseData
     *
     * @return array<string, string>
     */
    public function mapCancelResponse(array $rawResponseData): array;

    /**
     * @param array<string, mixed> $rawResponseData
     *
     * @return array<string, mixed>
     */
    public function mapStatusResponse(array $rawResponseData): array;

    /**
     * @param array<string, array<string, string>|string> $rawResponseData
     *
     * @return array<string, array<string, string|null>>
     */
    public function mapHistoryResponse(array $rawResponseData): array;

    /**
     * @param array<string, array<string, string>|string> $rawResponseData
     *
     * @return array<string, array<string, string|null>>
     */
    public function mapOrderHistoryResponse(array $rawResponseData): array;
}
