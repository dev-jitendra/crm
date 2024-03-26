<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\NumberUtil;

trait NumberSetter
{
    
    protected $number;

    public function setNumber(NumberUtil $number): void
    {
        $this->number = $number;
    }
}
