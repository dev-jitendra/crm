<?php


namespace Espo\Core\Notification;

use Espo\Core\ORM\EntityManager;

class UserEnabledChecker
{
    
    private $assignmentCache = [];

    public function __construct(private EntityManager $entityManager)
    {}

    public function checkAssignment(string $entityType, string $userId): bool
    {
        $key = $entityType . '_' . $userId;

        if (!array_key_exists($key, $this->assignmentCache)) {
            $preferences = $this->entityManager->getEntity('Preferences', $userId);

            $isEnabled = false;

            $ignoreList = [];

            if ($preferences) {
                $isEnabled = true;

                $ignoreList = $preferences->get('assignmentNotificationsIgnoreEntityTypeList') ?? [];
            }

            if ($preferences && in_array($entityType, $ignoreList)) {
                $isEnabled = false;
            }

            $this->assignmentCache[$key] = $isEnabled;
        }

        return $this->assignmentCache[$key];
    }
}
