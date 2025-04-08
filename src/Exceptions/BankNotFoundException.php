<?php

/**
 * @license MIT
 */

namespace SinyorPos\Exceptions;

use Exception;
use Throwable;

/**
 * Class BankNotFoundException
 */
class BankNotFoundException extends Exception
{
    /**
     * BankNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = 'Bank not found!', int $code = 330, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
