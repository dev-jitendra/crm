<?php


namespace Espo\Tools\Currency\Conversion;

use Espo\Core\Currency\Rates;
use Espo\ORM\Entity;


interface EntityConverter
{
    
    public function convert(Entity $entity, string $targetCurrency, Rates $rates): void;
}
