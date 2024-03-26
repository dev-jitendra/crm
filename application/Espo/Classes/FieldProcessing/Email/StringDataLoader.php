<?php


namespace Espo\Classes\FieldProcessing\Email;

use Espo\ORM\Entity;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;
use Espo\Entities\Email;
use Espo\Entities\User;


class StringDataLoader implements Loader
{
    private EntityManager $entityManager;
    private User $user;

    
    private $fromEmailAddressNameCache = [];

    public function __construct(EntityManager $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function process(Entity $entity, Params $params): void
    {
        

        $userEmailAddressIdList = [];

        $emailAddressCollection = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->getRelation($this->user, 'emailAddresses')
            ->select(['id'])
            ->find();

        foreach ($emailAddressCollection as $emailAddress) {
            $userEmailAddressIdList[] = $emailAddress->getId();
        }

        if (
            in_array($entity->get('fromEmailAddressId'), $userEmailAddressIdList) ||
            $entity->get('createdById') === $this->user->getId() &&
            $entity->getStatus() === Email::STATUS_SENT
        ) {
            $entity->loadLinkMultipleField('toEmailAddresses');

            $idList = $entity->get('toEmailAddressesIds');
            $names = $entity->get('toEmailAddressesNames');

            if (empty($idList)) {
                return;
            }

            $list = [];

            foreach ($idList as $emailAddressId) {
                $person = $this->getEmailAddressRepository()->getEntityByAddressId($emailAddressId, null, true);

                $list[] = $person ? $person->get('name') : $names->$emailAddressId;
            }

            $entity->set('personStringData', 'To: ' . implode(', ', $list));

            return;
        }

        
        $fromEmailAddressId = $entity->get('fromEmailAddressId');

        if (!$fromEmailAddressId) {
            return;
        }

        if (!array_key_exists($fromEmailAddressId, $this->fromEmailAddressNameCache)) {
            $person = $this->getEmailAddressRepository()->getEntityByAddressId($fromEmailAddressId, null, true);

            $fromName = $person?->get('name');

            $this->fromEmailAddressNameCache[$fromEmailAddressId] = $fromName;
        }

        $fromName =
            $this->fromEmailAddressNameCache[$fromEmailAddressId] ??
            $entity->get('fromName') ??
            $entity->get('fromEmailAddressName');

        $entity->set('personStringData', $fromName);
    }

    private function getEmailAddressRepository(): EmailAddressRepository
    {
        
        return $this->entityManager->getRepository('EmailAddress');
    }
}
