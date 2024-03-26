<?php


namespace Espo\Modules\Crm\Classes\Pdf\Account;

use Espo\Tools\Pdf\Data\DataLoader;
use Espo\Tools\Pdf\Params;
use Espo\ORM\Entity;

use stdClass;

class ExampleDataLoader implements DataLoader
{
    public function load(Entity $entity, Params $params): stdClass
    {
        

        return (object) [

        ];
    }
}
