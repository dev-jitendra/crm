<?php


namespace Espo\Modules\Crm\Hooks\Meeting;

use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Config;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


class Users implements BeforeSave
{
    public static int $order = 12;

    public function __construct(
        private Config $config,
        private User $user
    ) {}

    
    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if (!$this->config->get('eventAssignedUserIsAttendeeDisabled')) {
            if ($entity->hasLinkMultipleField('assignedUsers')) {
                $assignedUserIdList = $entity->getLinkMultipleIdList('assignedUsers');

                foreach ($assignedUserIdList as $assignedUserId) {
                    $entity->addLinkMultipleId('users', $assignedUserId);
                    $entity->setLinkMultipleName(
                        'users',
                        $assignedUserId,
                        $entity->getLinkMultipleName('assignedUsers', $assignedUserId)
                    );
                }
            }
            else {
                $assignedUserId = $entity->get('assignedUserId');

                if ($assignedUserId) {
                    $entity->addLinkMultipleId('users', $assignedUserId);
                    $entity->setLinkMultipleName('users', $assignedUserId, $entity->get('assignedUserName'));
                }
            }
        }

        if (!$entity->isNew()) {
            return;
        }

        $currentUserId = $this->user->getId();

        if (!$entity->hasLinkMultipleId('users', $currentUserId)) {
            return;
        }

        $status = $entity->getLinkMultipleColumn('users', 'status', $currentUserId);

        if (!$status || $status === Meeting::ATTENDEE_STATUS_NONE) {
            $entity->setLinkMultipleColumn('users', 'status', $currentUserId, Meeting::ATTENDEE_STATUS_ACCEPTED);
        }
    }
}
