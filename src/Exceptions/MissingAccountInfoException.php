<?php

namespace EceoPos\Exceptions;

use Exception;
use Throwable;

/**
 * Class MissingAccountInfoException
 */
class MissingAccountInfoException extends Exception
{
    /**
     * BankNotFoundException yapıcı metodu:
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct($message = 'Eksik hesap bilgileri!', $code = 430, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
