<?php


namespace Espo\Tools\LeadCapture;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Utils\Util;
use Espo\Entities\InboundEmail;
use Espo\Entities\LeadCapture as LeadCaptureEntity;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use stdClass;

class Service
{
    private EntityManager $entityManager;
    private ServiceContainer $recordServiceContainer;
    private User $user;

    public function __construct(
        EntityManager $entityManager,
        ServiceContainer $recordServiceContainer,
        User $user
    ) {
        $this->entityManager = $entityManager;
        $this->recordServiceContainer = $recordServiceContainer;
        $this->user = $user;
    }

    public function isApiKeyValid(string $apiKey): bool
    {
        $leadCapture = $this->entityManager
            ->getRDBRepositoryByClass(LeadCaptureEntity::class)
            ->where([
                'apiKey' => $apiKey,
                'isActive' => true,
            ])
            ->findOne();

        if ($leadCapture) {
            return true;
        }

        return false;
    }

    
    public function generateNewApiKeyForEntity(string $id): Entity
    {
        $service = $this->recordServiceContainer->get(LeadCaptureEntity::ENTITY_TYPE);

        $entity = $service->getEntity($id);

        if (!$entity) {
            throw new NotFound();
        }

        $entity->set('apiKey', $this->generateApiKey());

        $this->entityManager->saveEntity($entity);

        $service->prepareEntityForOutput($entity);

        return $entity;
    }

    public function generateApiKey(): string
    {
        return Util::generateApiKey();
    }

    
    public function getSmtpAccountDataList(): array
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $dataList = [];

        $inboundEmailList = $this->entityManager
            ->getRDBRepositoryByClass(InboundEmail::class)
            ->where([
                'useSmtp' => true,
                'status' => InboundEmail::STATUS_ACTIVE,
                ['emailAddress!=' => ''],
                ['emailAddress!=' => null],
            ])
            ->find();

        foreach ($inboundEmailList as $inboundEmail) {
            $item = (object) [];

            $key = 'inboundEmail:' . $inboundEmail->getId();

            $item->key = $key;
            $item->emailAddress = $inboundEmail->getEmailAddress();
            $item->fromName = $inboundEmail->getFromName();

            $dataList[] = $item;
        }

        return $dataList;
    }
}
