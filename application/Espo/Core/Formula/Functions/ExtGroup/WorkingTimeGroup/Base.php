<?php


namespace Espo\Core\Formula\Functions\ExtGroup\WorkingTimeGroup;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\BadArgumentValue;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Functions\BaseFunction;

use Espo\Entities\Team;
use Espo\Entities\User;

use Espo\Core\Di;
use Espo\ORM\Entity;
use Espo\Tools\WorkingTime\Calendar;
use Espo\Tools\WorkingTime\CalendarFactory;
use Espo\Tools\WorkingTime\CalendarUtility;

abstract class Base extends BaseFunction implements

    Di\EntityManagerAware,
    Di\InjectableFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\InjectableFactorySetter;

    private function getCalendarFactory(): CalendarFactory
    {
        return $this->injectableFactory->create(CalendarFactory::class);
    }

    protected function createCalendarUtility(Calendar $calendar): CalendarUtility
    {
        return $this->injectableFactory->createWithBinding(
            CalendarUtility::class,
            BindingContainerBuilder::create()
                ->bindInstance(Calendar::class, $calendar)
                ->build()
        );
    }

    
    protected function createCalendar(array $evaluatedArgs, int $argumentPosition = 1): Calendar
    {
        $target = $this->obtainTarget($evaluatedArgs, $argumentPosition);

        if ($target instanceof User) {
            return $this->getCalendarFactory()->createForUser($target);
        }

        if ($target instanceof Team) {
            return $this->getCalendarFactory()->createForTeam($target);
        }

        return $this->getCalendarFactory()->createGlobal();
    }

    
    private function obtainTarget(array $evaluatedArgs, int $argumentPosition = 1): ?Entity
    {
        if (count($evaluatedArgs) < $argumentPosition + 2) {
            return null;
        }

        $entityType = $evaluatedArgs[$argumentPosition];
        $entityId = $evaluatedArgs[$argumentPosition + 1];

        if (!is_string($entityType)) {
            $this->throwBadArgumentType($argumentPosition + 1, 'string');
        }

        if (!is_string($entityId)) {
            $this->throwBadArgumentType($argumentPosition + 2, 'string');
        }

        if (!in_array($entityType, [User::ENTITY_TYPE, Team::ENTITY_TYPE])) {
            $this->throwBadArgumentValue($argumentPosition + 1);
        }

        $entity = $this->entityManager->getEntityById($entityType, $entityId);

        if (!$entity) {
            $this->throwError("Entity {$entityType} {$entityId} not found.");
        }

        return $entity;
    }
}
