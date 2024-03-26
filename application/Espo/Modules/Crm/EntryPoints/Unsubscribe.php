<?php


namespace Espo\Modules\Crm\EntryPoints;

use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Client\ActionRenderer;
use Espo\Entities\EmailAddress;
use Espo\Modules\Crm\Entities\Campaign;
use Espo\Modules\Crm\Entities\EmailQueueItem;
use Espo\Modules\Crm\Entities\MassEmail;
use Espo\Modules\Crm\Entities\TargetList;
use Espo\Modules\Crm\Tools\Campaign\LogService;
use Espo\ORM\Collection;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use Espo\Modules\Crm\Tools\MassEmail\Util as MassEmailUtil;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\EntryPoint\Traits\NoAuth;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\HookManager;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Hasher;
use Espo\Core\Utils\Metadata;

class Unsubscribe implements EntryPoint
{
    use NoAuth;

    public function __construct(
        private EntityManager $entityManager,
        private HookManager $hookManager,
        private Metadata $metadata,
        private Hasher $hasher,
        private LogService $service,
        private MassEmailUtil $util,
        private ActionRenderer $actionRenderer
    ) {}

    
    public function run(Request $request, Response $response): void
    {
        $id = $request->getQueryParam('id') ?? null;
        $emailAddress = $request->getQueryParam('emailAddress') ?? null;
        $hash = $request->getQueryParam('hash') ?? null;

        if ($emailAddress && $hash) {
            $this->processWithHash($response, $emailAddress, $hash);

            return;
        }

        if (!$id) {
            throw new BadRequest();
        }

        $queueItemId = $id;

        
        $queueItem = $this->entityManager->getEntityById(EmailQueueItem::ENTITY_TYPE, $queueItemId);

        if (!$queueItem) {
            throw new NotFound();
        }

        $campaign = null;
        $target = null;
        $massEmail = null;
        $massEmailId = $queueItem->getMassEmailId();

        if ($massEmailId) {
            
            $massEmail = $this->entityManager->getEntityById(MassEmail::ENTITY_TYPE, $massEmailId);
        }

        if ($massEmail) {
            $campaignId = $massEmail->getCampaignId();

            if ($campaignId) {
                
                $campaign = $this->entityManager->getEntityById(Campaign::ENTITY_TYPE, $campaignId);
            }

            $targetType = $queueItem->getTargetType();
            $targetId = $queueItem->getTargetId();

            $target = $this->entityManager->getEntityById($targetType, $targetId);

            if (!$target) {
                throw new NotFound();
            }

            if ($massEmail->optOutEntirely()) {
                $emailAddress = $target->get('emailAddress');

                if ($emailAddress) {
                    $ea = $this->getEmailAddressRepository()->getByAddress($emailAddress);

                    if ($ea) {
                        $ea->set('optOut', true);
                        $this->entityManager->saveEntity($ea);
                    }
                }
            }

            $link = $this->util->getLinkByEntityType($target->getEntityType());

            
            $targetListList = $this->entityManager
                ->getRDBRepository(MassEmail::ENTITY_TYPE)
                ->getRelation($massEmail, 'targetLists')
                ->find();

            foreach ($targetListList as $targetList) {
                $relation = $this->entityManager
                    ->getRDBRepository(TargetList::ENTITY_TYPE)
                    ->getRelation($targetList, $link);

                if ($relation->getColumn($target, 'optedOut')) {
                    continue;
                }

                $relation->updateColumnsById($target->getId(), ['optedOut' => true]);

                $hookData = [
                   'link' => $link,
                   'targetId' => $targetId,
                   'targetType' => $targetType,
                ];

                $this->hookManager->process(
                    TargetList::ENTITY_TYPE,
                    'afterOptOut',
                    $targetList,
                    [],
                    $hookData
                );
            }

            $this->hookManager->process($target->getEntityType(), 'afterOptOut', $target, [], []);

            $this->display($response, ['queueItemId' => $queueItemId]);
        }

        if ($campaign && $target) {
            $this->service->logOptedOut($campaign->getId(), $queueItem, $target);
        }
    }

    
    protected function display(Response $response, array $actionData): void
    {
        $data = [
            'actionData' => $actionData,
            'view' => $this->metadata->get(['clientDefs', 'Campaign', 'unsubscribeView']),
            'template' => $this->metadata->get(['clientDefs', 'Campaign', 'unsubscribeTemplate']),
        ];

        $params = ActionRenderer\Params::create('crm:controllers/unsubscribe', 'unsubscribe', $data);

        $this->actionRenderer->write($response, $params);
    }

    
    protected function processWithHash(Response $response, string $emailAddress, string $hash): void
    {
        $hash2 = $this->hasher->hash($emailAddress);

        if ($hash2 !== $hash) {
            throw new NotFound();
        }

        $repository = $this->getEmailAddressRepository();

        $ea = $repository->getByAddress($emailAddress);

        if (!$ea) {
            throw new NotFound();
        }

        $entityList = $repository->getEntityListByAddressId($ea->getId());

        if (!$ea->isOptedOut()) {
            $ea->set('optOut', true);

            $this->entityManager->saveEntity($ea);

            foreach ($entityList as $entity) {
                $this->hookManager->process($entity->getEntityType(), 'afterOptOut', $entity, [], []);
            }
        }

        $this->display($response,[
            'emailAddress' => $emailAddress,
            'hash' => $hash,
        ]);
    }

    private function getEmailAddressRepository(): EmailAddressRepository
    {
        
        return $this->entityManager->getRepository(EmailAddress::ENTITY_TYPE);
    }
}
