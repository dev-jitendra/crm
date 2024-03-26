<?php


namespace Espo\Tools\FieldManager\Hooks;

use Espo\Core\Di;
use Espo\Entities\NextNumber;

class NumberType implements Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    
    public function onRead(string $scope, string $name, &$defs, $options): void
    {
        $number = $this->entityManager
            ->getRDBRepository(NextNumber::ENTITY_TYPE)
            ->where([
                'entityType' => $scope,
                'fieldName' => $name,
            ])
            ->findOne();

        $value = null;

        if (!$number) {
            $value = 1;
        } else {
            if (!$number->get('value')) {
                $value = 1;
            }
        }

        if (!$value && $number) {
            $value = $number->get('value');
        }

        $defs['nextNumber'] = $value;
    }

    
    public function afterSave(string $scope, string $name, $defs, $options): void
    {
        if (!isset($defs['nextNumber'])) {
            return;
        }

        $number = $this->entityManager
            ->getRDBRepository(NextNumber::ENTITY_TYPE)
            ->where([
                'entityType' => $scope,
                'fieldName' => $name
            ])
            ->findOne();

        if (!$number) {
            $number = $this->entityManager->getNewEntity(NextNumber::ENTITY_TYPE);

            $number->set('entityType', $scope);
            $number->set('fieldName', $name);
        }

        $number->set('value', $defs['nextNumber']);

        $this->entityManager->saveEntity($number);
    }

    
    public function afterRemove(string $scope, string $name, $defs, $options): void
    {
        $number = $this->entityManager
            ->getRDBRepository(NextNumber::ENTITY_TYPE)
            ->where([
                'entityType' => $scope,
                'fieldName' => $name
            ])
            ->findOne();

        if (!$number) {
            return;
        }

        $this->entityManager->removeEntity($number);
    }
}
