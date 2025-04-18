<?php
/**
 * @license MIT
 */

namespace SinyorPos\Exceptions;

use LogicException;
use Throwable;

/**
 * Thrown when generated hash and the hash from the bank response does not match
 */
class HashMismatchException extends LogicException
{
    /**
     * @inheritDoc
     */
    public function __construct(string $message = 'Hash Mismatch!', int $code = 575, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
