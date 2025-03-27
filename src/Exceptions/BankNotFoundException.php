<?php

namespace SinyorPos\Exceptions;

use Exception;
use Throwable;

/**
 * Class BankNotFoundException
 * @package SinyorPos\Exceptions
 */
class BankNotFoundException extends Exception
{
    /**
     * BankNotFoundException yapıcı metodu:
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null $previous
     */
    public function __construct($message = 'Banka bulunamadı!', $code = 330, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
