<?php

namespace EceoPos\Exceptions;

use Exception;
use Throwable;

/**
 * Class UnsupportedPaymentModelException
 */
class UnsupportedPaymentModelException extends Exception
{
    /**
     * UnsupportedPaymentModelException yapıcı metodu:
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct($message = 'Desteklenmeyen ödeme modeli!', $code = 333, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
