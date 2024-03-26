<?php


namespace Espo\Tools\Export\Format;

use Espo\Core\Field\Currency;
use Espo\Core\Field\Date;
use Espo\Core\Field\DateTime;
use Espo\ORM\Entity;

interface CellValuePreparator
{
    
    public function prepare(
        Entity $entity,
        string $name
    ): string|bool|int|float|Date|DateTime|Currency|null;
}
