<?php
/**
 * @license MIT
 */

namespace SinyorPos\Exceptions;

use BadMethodCallException;
use Throwable;

/**
 * Class NotImplementedException
 */
class NotImplementedException extends BadMethodCallException
{
	/**
	 * @inheritDoc
	 */
	public function __construct(string $message = 'Not implemented!', int $code = 500, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
