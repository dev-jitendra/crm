<?php


namespace Espo\Tools\MassUpdate;

use Espo\ORM\Entity;
use Espo\ORM\Defs as OrmDefs;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\ObjectUtil;

use stdClass;

class ValueMapPreparator
{
    private OrmDefs $ormDefs;

    public function __construct(OrmDefs $ormDefs)
    {
        $this->ormDefs = $ormDefs;
    }

    public function prepare(Entity $entity, Data $data): stdClass
    {
        $map = (object) [];

        $this->loadAdditionalFields($entity, $data);

        foreach ($data->getAttributeList() as $attribute) {
            if ($data->getAction($attribute) === Action::UPDATE) {
                $map->$attribute = $data->getValue($attribute);

                continue;
            }

            if ($data->getValue($attribute) === null) {
                continue;
            }

            if (!$entity->has($attribute)) {
                continue;
            }

            if ($data->getAction($attribute) === Action::ADD) {
                $map->$attribute = $this->prepareItemAdd($entity->get($attribute), $data->getValue($attribute));

                continue;
            }

            if ($data->getAction($attribute) === Action::REMOVE) {
                $map->$attribute = $this->prepareItemRemove($entity->get($attribute), $data->getValue($attribute));

                continue;
            }
        }

        return $map;
    }

    private function loadAdditionalFields(Entity $entity, Data $data): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        foreach ($data->getAttributeList() as $attribute) {
            if ($entity->has($attribute)) {
                continue;
            }

            if (
                $entity->getAttributeParam($attribute, 'isLinkMultipleIdList') &&
                $entity->getAttributeParam($attribute, 'relation')
            ) {
                $field = $entity->getAttributeParam($attribute, 'relation');

                $columns = $this->ormDefs
                    ->getEntity($entity->getEntityType())
                    ->getField($field)
                    ->getParam('columns');

                $entity->loadLinkMultipleField($field, $columns);
            }
        }
    }

    
    private function prepareItemAdd($set, $ch)
    {
        if ($set === null && $ch === null) {
            return null;
        }

        if (is_array($set) || is_array($ch)) {
            $set = $set ?? [];
            $ch = $ch ?? [];

            if (!is_array($set) || !is_array($ch)) {
                return $set;
            }

            return $this->prepareItemAddArray($set, $ch);
        }

        if ($set instanceof stdClass || $ch instanceof stdClass) {
            $set = $set ?? (object) [];
            $ch = $ch ?? (object) [];

            if (!$set instanceof stdClass || !$ch instanceof stdClass) {
                return $set;
            }

            return $this->prepareItemAddObject($set, $ch);
        }

        return $set;
    }

    
    private function prepareItemAddArray(array $set, array $ch): array
    {
        if ($ch === []) {
            return $set;
        }

        $result = $set;

        foreach ($ch as $value) {
            if (in_array($value, $result)) {
                continue;
            }

            $result[] = $value;
        }

        return $result;
    }

    private function prepareItemAddObject(stdClass $set, stdClass $ch): stdClass
    {
        $result = ObjectUtil::clone($set);

        foreach (get_object_vars($ch) as $key => $value) {
            $result->$key = $value;
        }

        return $result;
    }

    
    private function prepareItemRemove($set, $ch)
    {
        if ($set === null && $ch === null) {
            return null;
        }

        if (is_array($set) || is_array($ch)) {
            $set = $set ?? [];
            $ch = $ch ?? [];

            if (!is_array($set) || !is_array($ch)) {
                return $set;
            }

            return $this->prepareItemRemoveArray($set, $ch);
        }

        if ($set instanceof stdClass || $ch instanceof stdClass) {
            $set = $set ?? (object) [];
            $ch = $ch ?? (object) [];

            if (!$set instanceof stdClass || !$ch instanceof stdClass) {
                return $set;
            }

            return $this->prepareItemRemoveObject($set, $ch);
        }

        return $set;
    }

    
    private function prepareItemRemoveArray(array $set, array $ch): array
    {
        if ($ch === []) {
            return $set;
        }

        $result = $set;

        foreach ($result as $i => $value) {
            if (!in_array($value, $ch)) {
                continue;
            }

            unset($result[$i]);
        }

        return array_values($result);
    }

    private function prepareItemRemoveObject(stdClass $set, stdClass $ch): stdClass
    {
        $result = ObjectUtil::clone($set);

        foreach (array_keys(get_object_vars($ch)) as $key) {
            unset($result->$key);
        }

        return $result;
    }
}
