<?php


namespace Espo\Core\Htmlizer\Helper;

use LightnCandy\SafeString as LightnCandySafeString;

class SafeString
{
    private $wrappee;

    public function __construct(string $value)
    {
        $this->wrappee = new LightnCandySafeString($value);
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function getWrappee(): LightnCandySafeString
    {
        return $this->wrappee;
    }

    public function __toString()
    {
        return (string) $this->wrappee;
    }
}
