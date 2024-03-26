<?php


namespace Espo\Tools\WorkingTime;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Entities\Team;
use Espo\Entities\User;

class CalendarUtilityFactory
{
    private InjectableFactory $injectableFactory;
    private CalendarFactory $calendarFactory;

    public function __construct(
        InjectableFactory $injectableFactory,
        CalendarFactory $calendarFactory
    ) {
        $this->injectableFactory = $injectableFactory;
        $this->calendarFactory = $calendarFactory;
    }

    public function create(Calendar $calendar): CalendarUtility
    {
        return $this->injectableFactory->createWithBinding(
            CalendarUtility::class,
            BindingContainerBuilder::create()
                ->bindInstance(Calendar::class, $calendar)
                ->build()
        );
    }

    public function createForUser(User $user): CalendarUtility
    {
        $calendar = $this->calendarFactory->createForUser($user);

        return $this->create($calendar);
    }

    public function createForTeam(Team $team): CalendarUtility
    {
        $calendar = $this->calendarFactory->createForTeam($team);

        return $this->create($calendar);
    }

    public function createGlobal(): CalendarUtility
    {
        $calendar = $this->calendarFactory->createGlobal();

        return $this->create($calendar);
    }
}
