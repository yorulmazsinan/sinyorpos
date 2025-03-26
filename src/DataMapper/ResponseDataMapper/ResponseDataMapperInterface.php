<?php

/**
 * @license MIT
 */

namespace SinyorPos\DataMapper\ResponseDataMapper;

interface ResponseDataMapperInterface extends PaymentResponseMapperInterface, NonPaymentResponseMapperInterface
{
    /** @var string */
    public const TX_APPROVED = 'approved';

    /** @var string */
    public const TX_DECLINED = 'declined';
}
