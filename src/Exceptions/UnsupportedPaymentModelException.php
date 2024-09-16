<?php
/**
 * @license MIT
 */

namespace SinyorPos\Exceptions;

use Exception;
use Throwable;

/**
 * Class UnsupportedPaymentModelException
 * @package SinyorPos\Exceptions
 */
class UnsupportedPaymentModelException extends Exception
{
	/**
	 * UnsupportedPaymentModelException constructor.
	 *
	 * @param string         $message
	 * @param int            $code
	 * @param Throwable|null $previous
	 */
	public function __construct(string $message = 'Unsupported payment model!', int $code = 333, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
