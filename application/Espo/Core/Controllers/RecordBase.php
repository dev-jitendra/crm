<?php


namespace Espo\Core\Controllers;

use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\NotFoundSilent;
use Espo\Core\Record\ServiceContainer as RecordServiceContainer;
use Espo\Core\Record\SearchParamsFetcher;
use Espo\Core\Record\CreateParamsFetcher;
use Espo\Core\Record\ReadParamsFetcher;
use Espo\Core\Record\UpdateParamsFetcher;
use Espo\Core\Record\DeleteParamsFetcher;
use Espo\Core\Record\FindParamsFetcher;
use Espo\Core\Record\Service as RecordService;
use Espo\Core\Container;
use Espo\Core\Acl;
use Espo\Core\AclManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\ServiceFactory;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Select\SearchParams;
use Espo\Core\Di;
use Espo\Entities\User;
use Espo\Entities\Preferences;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use stdClass;

class RecordBase extends Base implements

    Di\EntityManagerAware,
    Di\InjectableFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\InjectableFactorySetter;

    
    public static $defaultAction = 'list';

    
    protected $recordServiceContainer;
    
    protected $config;
    
    protected $user;
    
    protected $acl;

    
    protected $entityManager;

    public function __construct(
        protected SearchParamsFetcher $searchParamsFetcher,
        protected CreateParamsFetcher $createParamsFetcher,
        protected ReadParamsFetcher $readParamsFetcher,
        protected UpdateParamsFetcher $updateParamsFetcher,
        protected DeleteParamsFetcher $deleteParamsFetcher,
        RecordServiceContainer $recordServiceContainer,
        protected FindParamsFetcher $findParamsFetcher,
        Config $config,
        User $user,
        Acl $acl,
        
        Container $container,
        AclManager $aclManager,
        Preferences $preferences,
        Metadata $metadata,
        ServiceFactory $serviceFactory
    ) {
        $this->recordServiceContainer = $recordServiceContainer;
        $this->config = $config;
        $this->user = $user;
        $this->acl = $acl;

        parent::__construct(
            $container,
            $user,
            $acl,
            $aclManager,
            $config,
            $preferences,
            $metadata,
            $serviceFactory
        );
    }

    protected function getEntityType(): string
    {
        return $this->name;
    }

    
    protected function getRecordService(?string $entityType = null): RecordService
    {
        return $this->recordServiceContainer->get($entityType ?? $this->getEntityType());
    }

    
    public function getActionRead(Request $request, Response $response): stdClass
    {
        if (method_exists($this, 'actionRead')) {
            
            return (object) $this->actionRead($request->getRouteParams(), $request->getParsedBody(), $request);
        }

        $id = $request->getRouteParam('id');
        $params = $this->readParamsFetcher->fetch($request);

        if (!$id) {
            throw new BadRequest("No ID.");
        }

        $entity = $this->getRecordService()->read($id, $params);

        return $entity->getValueMap();
    }

    
    public function postActionCreate(Request $request, Response $response): stdClass
    {
        if (method_exists($this, 'actionCreate')) {
            
            return (object) $this->actionCreate($request->getRouteParams(), $request->getParsedBody(), $request);
        }

        $data = $request->getParsedBody();
        $params = $this->createParamsFetcher->fetch($request);

        $entity = $this->getRecordService()->create($data, $params);

        return $entity->getValueMap();
    }

    
    public function patchActionUpdate(Request $request, Response $response): stdClass
    {
        return $this->putActionUpdate($request, $response);
    }

    
    public function putActionUpdate(Request $request, Response $response): stdClass
    {
        if (method_exists($this, 'actionUpdate')) {
            
            return (object) $this->actionUpdate($request->getRouteParams(), $request->getParsedBody(), $request);
        }

        $id = $request->getRouteParam('id');
        $data = $request->getParsedBody();

        if (!$id) {
            throw new BadRequest("No ID.");
        }

        $params = $this->updateParamsFetcher->fetch($request);

        $entity = $this->getRecordService()->update($id, $data, $params);

        return $entity->getValueMap();
    }

    
    public function getActionList(Request $request, Response $response): stdClass
    {
        if (method_exists($this, 'actionList')) {
            
            return (object) $this->actionList($request->getRouteParams(), $request->getParsedBody(), $request);
        }

        $searchParams = $this->fetchSearchParamsFromRequest($request);
        $findParams = $this->findParamsFetcher->fetch($request);

        $recordCollection = $this->getRecordService()->find($searchParams, $findParams);

        return (object) [
            'total' => $recordCollection->getTotal(),
            'list' => $recordCollection->getValueMapList(),
        ];
    }

    
    public function deleteActionDelete(Request $request, Response $response): bool
    {
        if (method_exists($this, 'actionDelete')) {
            
            return $this->actionDelete($request->getRouteParams(), $request->getParsedBody(), $request);
        }

        $id = $request->getRouteParam('id');
        $params = $this->deleteParamsFetcher->fetch($request);

        if (!$id) {
            throw new BadRequest("No ID.");
        }

        $this->getRecordService()->delete($id, $params);

        return true;
    }

    
    protected function fetchSearchParamsFromRequest(Request $request): SearchParams
    {
        return $this->searchParamsFetcher->fetch($request);
    }

    
    public function postActionGetDuplicateAttributes(Request $request): stdClass
    {
        $id = $request->getParsedBody()->id ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        return $this->getRecordService()->getDuplicateAttributes($id);
    }

    
    public function postActionRestoreDeleted(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $id = $request->getParsedBody()->id ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        $this->getRecordService()->restoreDeleted($id);

        return true;
    }

    
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
}
