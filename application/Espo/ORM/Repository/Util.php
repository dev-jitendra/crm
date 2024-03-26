<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Entity;

use ReflectionClass;
use InvalidArgumentException;

class Util
{
    
    public static function getEntityTypeByClass(string $className): string
    {
        $class = new ReflectionClass($className);

        if (!$class->implementsInterface(Entity::class)) {
            throw new InvalidArgumentException();
        }

        if ($class->hasConstant('ENTITY_TYPE'))  {
            return (string) $class->getConstant('ENTITY_TYPE');
        }

        return $class->getShortName();
    }
}
