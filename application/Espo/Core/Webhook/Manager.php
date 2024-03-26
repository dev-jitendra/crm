<?php


namespace Espo\Core\Webhook;

use Espo\Core\ORM\Entity;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\FieldUtil;
use Espo\Core\Utils\Log;

use Espo\Entities\Webhook;
use Espo\Entities\WebhookEventQueueItem;

use RuntimeException;


class Manager
{
    private string $cacheKey = 'webhooks';

    
    protected $skipAttributeList = [
        'isFollowed',
        'modifiedAt',
        'modifiedBy'
    ];

    
    private $data = null;

    public function __construct(
        private Config $config,
        private DataCache $dataCache,
        private EntityManager $entityManager,
        private FieldUtil $fieldUtil,
        private Log $log
    ) {

        $this->loadData();
    }

    private function loadData(): void
    {
        if ($this->config->get('useCache') && $this->dataCache->has($this->cacheKey)) {
            
            $data = $this->dataCache->get($this->cacheKey);

            $this->data = $data;
        }

        if (is_null($this->data)) {
            $this->data = $this->buildData();

            if ($this->config->get('useCache')) {
                $this->storeDataToCache();
            }
        }
    }

    private function storeDataToCache(): void
    {
        if ($this->data === null) {
            throw new RuntimeException("No data to store.");
        }

        $this->dataCache->store($this->cacheKey, $this->data);
    }

    
    private function buildData(): array
    {
        $data = [];

        $list = $this->entityManager
            ->getRDBRepository(Webhook::ENTITY_TYPE)
            ->select(['event'])
            ->group(['event'])
            ->where([
                'isActive' => true,
                'event!=' => null,
            ])
            ->find();

        foreach ($list as $webhook) {
            
            $event = $webhook->getEvent();

            $data[$event] = true;
        }

        return $data;
    }

    
    public function addEvent(string $event): void
    {
        $this->data[$event] = true;

        if ($this->config->get('useCache')) {
            $this->storeDataToCache();
        }
    }

    
    public function removeEvent(string $event): void
    {
        $notExists = !$this->entityManager
            ->getRDBRepository(Webhook::ENTITY_TYPE)
            ->select(['id'])
            ->where([
                'event' => $event,
                'isActive' => true,
            ])
            ->findOne();

        if ($notExists) {
            unset($this->data[$event]);

            if ($this->config->get('useCache')) {
                $this->storeDataToCache();
            }
        }
    }

    protected function eventExists(string $event): bool
    {
        return isset($this->data[$event]);
    }

    protected function logDebugEvent(string $event, Entity $entity): void
    {
        $this->log->debug("Webhook: {$event} on record {$entity->getId()}.");
    }

    
    public function processCreate(Entity $entity): void
    {
        $event = $entity->getEntityType() . '.create';

        if (!$this->eventExists($event)) {
            return;
        }

        $this->entityManager->createEntity(WebhookEventQueueItem::ENTITY_TYPE, [
            'event' => $event,
            'targetType' => $entity->getEntityType(),
            'targetId' => $entity->getId(),
            'data' => $entity->getValueMap(),
        ]);

        $this->logDebugEvent($event, $entity);
    }

    
    public function processDelete(Entity $entity): void
    {
        $event = $entity->getEntityType() . '.delete';

        if (!$this->eventExists($event)) {
            return;
        }

        $this->entityManager->createEntity(WebhookEventQueueItem::ENTITY_TYPE, [
            'event' => $event,
            'targetType' => $entity->getEntityType(),
            'targetId' => $entity->getId(),
            'data' => (object) [
                'id' => $entity->getId(),
            ],
        ]);

        $this->logDebugEvent($event, $entity);
    }

    
    public function processUpdate(Entity $entity): void
    {
        $event = $entity->getEntityType() . '.update';

        $data = (object) [];

        foreach ($entity->getAttributeList() as $attribute) {
            if (in_array($attribute, $this->skipAttributeList)) {
                continue;
            }

            if ($entity->isAttributeChanged($attribute)) {
                $data->$attribute = $entity->get($attribute);
            }
        }

        if (!count(get_object_vars($data))) {
            return;
        }

        $data->id = $entity->getId();

        if ($this->eventExists($event)) {
            $this->entityManager->createEntity(WebhookEventQueueItem::ENTITY_TYPE, [
                'event' => $event,
                'targetType' => $entity->getEntityType(),
                'targetId' => $entity->getId(),
                'data' => $data,
            ]);

            $this->logDebugEvent($event, $entity);
        }

        foreach ($this->fieldUtil->getEntityTypeFieldList($entity->getEntityType()) as $field) {
            $itemEvent = $entity->getEntityType() . '.fieldUpdate.' . $field;

            if (!$this->eventExists($itemEvent)) {
                continue;
            }

            $attributeList = $this->fieldUtil->getActualAttributeList($entity->getEntityType(), $field);

            $isChanged = false;

            foreach ($attributeList as $attribute) {
                if (in_array($attribute, $this->skipAttributeList)) {
                    continue;
                }

                if (property_exists($data, $attribute)) {
                    $isChanged = true;

                    break;
                }
            }

            if ($isChanged) {
                $itemData = (object) [];

                $itemData->id = $entity->getId();

                $attributeList = $this->fieldUtil->getAttributeList($entity->getEntityType(), $field);

                foreach ($attributeList as $attribute) {
                    if (in_array($attribute, $this->skipAttributeList)) {
                        continue;
                    }

                    $itemData->$attribute = $entity->get($attribute);
                }

                $this->entityManager->createEntity(WebhookEventQueueItem::ENTITY_TYPE, [
                    'event' => $itemEvent,
                    'targetType' => $entity->getEntityType(),
                    'targetId' => $entity->getId(),
                    'data' => $itemData,
                ]);

                $this->logDebugEvent($itemEvent, $entity);
            }
        }
    }
}
