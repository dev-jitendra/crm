<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\NumberUtil;

interface NumberAware
{
    public function setNumber(NumberUtil $number): void;
}
