<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\ORM\Entity;

class SetLinkMultipleColumnType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 4) {
            $this->throwTooFewArguments(4);
        }

        $link = $this->evaluate($args[0]);
        $id = $this->evaluate($args[1]);
        $column = $this->evaluate($args[2]);
        $value = $this->evaluate($args[3]);

        $entity = $this->getEntity();

        if (!$entity instanceof Entity) {
            throw new Error();
        }

        $entity->setLinkMultipleColumn($link, $column, $id, $value);
    }
}
