<?php


namespace Espo\Tools\WorkingTime;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Entities\Team;
use Espo\Entities\User;

class CalendarFactory
{
    private InjectableFactory $injectableFactory;

    public function __construct(InjectableFactory $injectableFactory)
    {
        $this->injectableFactory = $injectableFactory;
    }

    public function createGlobal(): GlobalCalendar
    {
        return $this->injectableFactory->create(GlobalCalendar::class);
    }

    public function createForUser(User $user): UserCalendar
    {
        $binding = BindingContainerBuilder::create()
            ->bindInstance(User::class, $user)
            ->build();

        return $this->injectableFactory->createWithBinding(UserCalendar::class, $binding);
    }

    public function createForTeam(Team $team): TeamCalendar
    {
        $binding = BindingContainerBuilder::create()
            ->bindInstance(Team::class, $team)
            ->build();

        return $this->injectableFactory->createWithBinding(TeamCalendar::class, $binding);
    }
}
