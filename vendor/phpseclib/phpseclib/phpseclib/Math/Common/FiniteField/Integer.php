<?php



namespace phpseclib3\Math\Common\FiniteField;


abstract class Integer implements \JsonSerializable
{
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['hex' => $this->toHex(true)];
    }

    
    abstract public function toHex();
}
