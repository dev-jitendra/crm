<?php


namespace Espo\Tools\Dashboard;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Entities\DashboardTemplate;
use Espo\Entities\Preferences;
use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

class Service
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function deployTemplateToUsers(string $id, array $userIdList, bool $append = false): void
    {
        $template = $this->entityManager->getEntityById(DashboardTemplate::ENTITY_TYPE, $id);

        if (!$template) {
            throw new NotFound();
        }

        foreach ($userIdList as $userId) {
            
            $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

            if (!$user) {
                throw new NotFound("User not found.");
            }

            if ($user->isPortal() || $user->isApi()) {
                throw new Forbidden("Not allowed user type.");
            }
        }

        foreach ($userIdList as $userId) {
            $preferences = $this->entityManager->getEntityById(Preferences::ENTITY_TYPE, $userId);

            if (!$preferences) {
                continue;
            }

            $this->applyTemplate($preferences, $template, $append);

            $this->entityManager->saveEntity($preferences);
        }
    }

    
    public function deployTemplateToTeam(string $id, string $teamId, bool $append = false): void
    {
        
        $template = $this->entityManager->getEntityById(DashboardTemplate::ENTITY_TYPE, $id);

        if (!$template) {
            throw new NotFound();
        }

        $team = $this->entityManager->getEntityById(Team::ENTITY_TYPE, $teamId);

        if (!$team) {
            throw new NotFound();
        }

        $userList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->join('teams')
            ->distinct()
            ->where([
                'teams.id' => $teamId,
            ])
            ->find();

        foreach ($userList as $user) {
            $preferences = $this->entityManager->getEntityById(Preferences::ENTITY_TYPE, $user->getId());

            if (!$preferences) {
                continue;
            }

            $this->applyTemplate($preferences, $template, $append);

            $this->entityManager->saveEntity($preferences);
        }
    }

    private function applyTemplate(Entity $preferences, DashboardTemplate $template, bool $append): void
    {
        if (!$append) {
            $preferences->set([
                'dashboardLayout' => $template->get('layout'),
                'dashletsOptions' => $template->get('dashletsOptions'),
            ]);
        }
        else {
            $dashletsOptions = $preferences->get('dashletsOptions');

            if (!$dashletsOptions) {
                $dashletsOptions = (object) [];
            }

            $dashboardLayout = $preferences->get('dashboardLayout');

            if (!$dashboardLayout) {
                $dashboardLayout = [];
            }

            foreach ($template->get('layout') as $item) {
                $exists = false;

                foreach ($dashboardLayout as $k => $item2) {
                    if (isset($item->id) && isset($item2->id)) {
                        if ($item->id === $item2->id) {
                            $exists = true;
                            $dashboardLayout[$k] = $item;
                        }
                    }
                }

                if (!$exists) {
                    $dashboardLayout[] = $item;
                }
            }

            foreach ($template->get('dashletsOptions') as $id => $item) {
                $dashletsOptions->$id = $item;
            }

            $preferences->set([
                'dashboardLayout' => $dashboardLayout,
                'dashletsOptions' => $dashletsOptions,
            ]);
        }
    }
}
