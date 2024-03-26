<?php


namespace Espo\Tools\WorkingTime;

use Espo\Core\Utils\Config;
use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\Entities\WorkingTimeCalendar;
use Espo\Entities\WorkingTimeRange;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Condition;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Where\OrGroup;
use Espo\Tools\WorkingTime\Calendar\WorkingWeekday;
use Espo\Tools\WorkingTime\Calendar\WorkingDate;

use Espo\Core\Field\Date;

use DateTimeZone;
use Espo\Tools\WorkingTime\Util\CalendarUtil;

class UserCalendar implements Calendar
{
    private User $user;
    private EntityManager $entityManager;
    private Config $config;


    private ?WorkingTimeCalendar $workingTimeCalendar = null;
    private ?CalendarUtil $util = null;

    
    private ?array $cache = null;
    private ?string $cacheKey = null;
    private DateTimeZone $timezone;

    public function __construct(User $user, EntityManager $entityManager, Config $config)
    {
        $this->user = $user;
        $this->entityManager = $entityManager;
        $this->config = $config;

        $this->timezone = new DateTimeZone($config->get('timeZone'));

        $this->init();

        if (!$this->workingTimeCalendar) {
            $this->initDefault();
        }

        if ($this->workingTimeCalendar) {
            $this->util = new CalendarUtil($this->workingTimeCalendar);

            $this->timezone = $this->workingTimeCalendar->getTimeZone() ?? $this->timezone;
        }
    }

    private function init(): void
    {
        $workingTimeCalendarLink = $this->user->getWorkingTimeCalendar();

        if ($workingTimeCalendarLink) {
            $this->workingTimeCalendar = $this->entityManager
                ->getRepositoryByClass(WorkingTimeCalendar::class)
                ->getById($workingTimeCalendarLink->getId());

            if ($this->workingTimeCalendar) {
                return;
            }
        }

        $defaultTeamLink = $this->user->getDefaultTeam();

        if (!$defaultTeamLink) {
            return;
        }

        $team = $this->entityManager
            ->getRepositoryByClass(Team::class)
            ->getById($defaultTeamLink->getId());

        if (!$team) {
            return;
        }

        $workingTimeCalendarLink = $team->getWorkingTimeCalendar();

        if (!$workingTimeCalendarLink) {
            return;
        }

        $this->workingTimeCalendar = $this->entityManager
            ->getRepositoryByClass(WorkingTimeCalendar::class)
            ->getById($workingTimeCalendarLink->getId());
    }

    private function initDefault(): void
    {
        $id = $this->config->get('workingTimeCalendarId');

        if (!$id) {
            return;
        }

        $this->workingTimeCalendar = $this->entityManager->getEntityById(WorkingTimeCalendar::ENTITY_TYPE, $id);
    }

    public function isAvailable(): bool
    {
        return $this->workingTimeCalendar !== null;
    }

    public function getTimezone(): DateTimeZone
    {
        return $this->timezone;
    }

    
    public function getWorkingWeekdays(): array
    {
        if ($this->workingTimeCalendar === null) {
            return [];
        }

        return $this->workingTimeCalendar->getWorkingWeekdays();
    }

    
    public function getNonWorkingDates(Date $from, Date $to): array
    {
        if ($this->workingTimeCalendar === null) {
            return [];
        }

        return $this->getDates($from, $to)[0];
    }

    
    public function getWorkingDates(Date $from, Date $to): array
    {
        if ($this->workingTimeCalendar === null) {
            return [];
        }

        return $this->getDates($from, $to)[1];
    }

    
    private function getDates(Date $from, Date $to): array
    {
        $cacheKey = $from->toString() . '-' . $to->toString();

        if ($this->cacheKey === $cacheKey) {
            assert($this->cache !== null);

            return $this->cache;
        }

        $notWorkingList = [];
        $workingList = [];

        $listForUser = $this->fetchUserRanges($from, $to);
        $list = $this->fetchRanges($from, $to);

        foreach ($listForUser as $range) {
            $dates = $this->rangeToDates($range);

            if ($range->getType() === WorkingTimeRange::TYPE_NON_WORKING) {
                $notWorkingList = array_merge($notWorkingList, $dates);

                continue;
            }

            $workingList = array_merge($workingList, $dates);
        }

        $metMap = [];

        foreach (array_merge($notWorkingList, $workingList) as $date) {
            $metMap[$date->getDate()->getString()] = true;
        }

        foreach ($list as $range) {
            $dates = array_filter(
                $this->rangeToDates($range),
                function (WorkingDate $date) use ($metMap) {
                    return !array_key_exists($date->getDate()->toString(), $metMap);
                }
            );

            if ($range->getType() === WorkingTimeRange::TYPE_NON_WORKING) {
                $notWorkingList = array_merge($notWorkingList, $dates);

                continue;
            }

            $workingList = array_merge($workingList, $dates);
        }

        $this->cacheKey = $cacheKey;
        $this->cache = [$notWorkingList, $workingList];

        return $this->cache;
    }

    
    private function rangeToDates(WorkingTimeRange $range): array
    {
        if (!$this->util) {
            return [];
        }

        return $this->util->rangeToDates($range);
    }

    
    private function fetchUserRanges(Date $from, Date $to): array
    {
        $list = [];

        $collection = $this->entityManager
            ->getRDBRepositoryByClass(WorkingTimeRange::class)
            ->leftJoin('users')
            ->where(
                Condition::equal(
                    Expression::column('users.id'),
                    $this->user->getId()
                )
            )
            ->where(
                OrGroup::create(
                    Condition::greaterOrEqual(
                        Expression::column('dateEnd'),
                        $from->toString()
                    ),
                    Condition::lessOrEqual(
                        Expression::column('dateStart'),
                        $to->toString()
                    ),
                )
            )
            ->find();

        foreach ($collection as $entity) {
            $list[] = $entity;
        }

        return $list;
    }

    
    private function fetchRanges(Date $from, Date $to): array
    {
        if ($this->workingTimeCalendar === null) {
            return [];
        }

        $list = [];

        $collection = $this->entityManager
            ->getRDBRepositoryByClass(WorkingTimeRange::class)
            ->leftJoin('calendars')
            ->where(
                Condition::equal(
                    Expression::column('calendars.id'),
                    $this->workingTimeCalendar->getId()
                )
            )
            ->where(
                OrGroup::create(
                    Condition::greaterOrEqual(
                        Expression::column('dateEnd'),
                        $from->toString()
                    ),
                    Condition::lessOrEqual(
                        Expression::column('dateStart'),
                        $to->toString()
                    ),
                )
            )
            ->find();

        foreach ($collection as $entity) {
            $list[] = $entity;
        }

        return $list;
    }
}
