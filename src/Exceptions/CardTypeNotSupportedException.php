<?php

namespace EceoPos\Exceptions;

use DomainException;
use Throwable;

/**
 * Kart tipi, ağ geçidi tarafından desteklenmediğinde atılan istisna.
 */
class CardTypeNotSupportedException extends DomainException
{
    private $type;

    /**
     * BankNotFoundException yapıcı metodu:
     *
     * @param  string  $type
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(string $type, string $message = 'Kart tipi bu ağ geçidi tarafından desteklenmiyor!', int $code = 74, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
