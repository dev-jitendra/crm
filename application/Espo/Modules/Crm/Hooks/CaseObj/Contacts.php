<?php


namespace Espo\Modules\Crm\Hooks\CaseObj;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\Core\InjectableFactory;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\CaseObj;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Repository\Option\SaveOptions;
use Espo\Tools\Stream\Service as StreamService;


class Contacts implements AfterSave
{
    private ?StreamService $streamService = null;

    public function __construct(
        private EntityManager $entityManager,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if (!$entity->isAttributeChanged('contactId')) {
            return;
        }

        
        $contactId = $entity->get('contactId');
        $contactIdList = $entity->get('contactsIds') ?? [];
        
        $fetchedContactId = $entity->getFetched('contactId');

        $relation = $this->entityManager
            ->getRDBRepositoryByClass(CaseObj::class)
            ->getRelation($entity, 'contacts');

        if ($fetchedContactId) {
            $previousPortalUser = $this->entityManager
                ->getRDBRepository(User::ENTITY_TYPE)
                ->select(['id'])
                ->where([
                    'contactId' => $fetchedContactId,
                    'type' => User::TYPE_PORTAL,
                ])
                ->findOne();

            if ($previousPortalUser) {
                $this->getStreamService()->unfollowEntity($entity, $previousPortalUser->getId());
            }
        }

        if (!$contactId && $fetchedContactId) {
            $relation->unrelateById($fetchedContactId);

            return;
        }

        if (!$contactId) {
            return;
        }

        $portalUser = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->select(['id'])
            ->where([
                'contactId' => $contactId,
                'type' => User::TYPE_PORTAL,
                'isActive' => true,
            ])
            ->findOne();

        if ($portalUser) {
            $this->getStreamService()->followEntity($entity, $portalUser->getId(), true);
        }

        if (in_array($contactId, $contactIdList)) {
            return;
        }

        $contact = $this->entityManager->getEntityById(Contact::ENTITY_TYPE, $contactId);

        if (!$contact) {
            return;
        }

        if ($relation->isRelated($contact)) {
            return;
        }

        $relation->relateById($contactId);
    }

    private function getStreamService(): StreamService
    {
        if (!$this->streamService) {
            $this->streamService = $this->injectableFactory->create(StreamService::class);
        }

        return $this->streamService;
    }
}
