<?php

namespace EceoPos\Exceptions;

use BadMethodCallException;
use Throwable;

/**
 * Class NotImplementedException
 */
class NotImplementedException extends BadMethodCallException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message = 'Uygulanmadı!', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
