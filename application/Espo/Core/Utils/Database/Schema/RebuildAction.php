<?php


namespace Espo\Core\Utils\Database\Schema;

use Doctrine\DBAL\Schema\Schema as DbalSchema;

interface RebuildAction
{
    public function process(DbalSchema $oldSchema, DbalSchema $newSchema): void;
}
