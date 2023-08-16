<?php

namespace EceoPos\Exceptions;

use DomainException;
use Throwable;

/**
 * Kart tipi sağlanmazsa çalışacak metod.
 */
class CardTypeRequiredException extends DomainException
{
    /**
     * @var string
     */
    private $gatewayName;

    /**
     * BankNotFoundException yapıcı metodu:
     *
     * @param  string  $gatewayName
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(string $gatewayName, string $message = 'Bu ağ geçidi için kart tipi gereklidir!', int $code = 73, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->gatewayName = $gatewayName;
    }

    /**
     * @return string
     */
    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }
}
