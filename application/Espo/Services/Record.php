<?php


namespace Espo\Services;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\Core\Acl\Table as AclTable;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Record\Service as RecordService;
use Espo\Core\Utils\Util;
use Espo\Tools\Export\Export as ExportTool;
use Espo\Tools\Export\Params as ExportParams;
use Espo\Core\Di;


class Record extends RecordService implements

    Di\AclManagerAware,
    Di\FileManagerAware,
    Di\SelectManagerFactoryAware,
    Di\InjectableFactoryAware,
    Di\SelectBuilderFactoryAware,
    Di\LogAware,
    \Espo\Core\Interfaces\Injectable
{
    use Di\AclManagerSetter;
    use Di\FileManagerSetter;
    use Di\SelectManagerFactorySetter;
    use Di\InjectableFactorySetter;
    use Di\SelectBuilderFactorySetter;
    use Di\LogSetter;

    
    use \Espo\Core\Traits\Injectable;

    
    protected $dependencyList = []; 

    public function __construct(string $entityType = '')
    {
        parent::__construct($entityType);

        if (!$this->entityType) {
            
            $name = get_class($this);

            $matches = null;

            if (preg_match('@\\\\([\w]+)$@', $name, $matches)) {
                $name = $matches[1];
            }

            $this->entityType = Util::normalizeScopeName($name);
        }

        
        $this->init();
    }

    
    protected function init() {}

    
    public function setEntityType(string $entityType): void {}

    
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    
    protected function getConfig()
    {
        return $this->config;
    }

    
    protected function getServiceFactory()
    {
        return $this->serviceFactory;
    }

    
    protected function getSelectManagerFactory()
    {
        return $this->selectManagerFactory;
    }

    
    protected function getAcl()
    {
        return $this->acl;
    }

    
    protected function getUser()
    {
        return $this->user;
    }

    
    protected function getAclManager()
    {
        return $this->aclManager;
    }

    
    protected function getFileManager()
    {
        return $this->fileManager;
    }

    
    protected function getMetadata()
    {
        return $this->metadata;
    }

    
    protected function getFieldManagerUtil()
    {
        return $this->fieldUtil;
    }

    
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    
    protected function getSelectManager($entityType = null)
    {
        if (!$entityType) {
            $entityType = $this->entityType;
        }

        return $this->getSelectManagerFactory()->create($entityType);
    }

    
    protected function getSelectParams($params)
    {
        $selectManager = $this->getSelectManager($this->entityType);

        $selectParams = $selectManager->getSelectParams($params, true, true, true);

        if (empty($selectParams['orderBy'])) {
            $selectManager->applyDefaultOrder($selectParams);
        }

        return $selectParams;
    }

    
    protected function getRecordService($name)
    {
        return $this->recordServiceContainer->get($name);
    }

    
    public function exportCollection(array $params, Collection $collection): string
    {
        if ($this->acl->getPermissionLevel('exportPermission') !== AclTable::LEVEL_YES) {
            throw new ForbiddenSilent("No 'export' permission.");
        }

        if (!$this->acl->check($this->entityType, AclTable::ACTION_READ)) {
            throw new ForbiddenSilent("No 'read' access.");
        }

        $params['entityType'] = $this->entityType;

        $export = $this->injectableFactory->create(ExportTool::class);

        $exportParams = ExportParams::fromRaw($params);

        if (isset($params['params'])) {
            foreach (get_object_vars($params['params']) as $k => $v) {
                $exportParams = $exportParams->withParam($k, $v);
            }
        }

        return $export
            ->setParams($exportParams)
            ->setCollection($collection)
            ->run()
            ->getAttachmentId();
    }

    
    public function loadLinkMultipleFieldsForList(Entity $entity, array $selectAttributeList): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        foreach ($selectAttributeList as $attribute) {
            if (!$entity->getAttributeParam($attribute, 'isLinkMultipleIdList')) {
                continue;
            }

            $field = $entity->getAttributeParam($attribute, 'relation');

            if (!$field) {
                continue;
            }

            if ($entity->has($attribute)) {
                continue;
            }

            $entity->loadLinkMultipleField($field);
        }
    }

    
    public function loadAdditionalFieldsForList(Entity $entity)
    {
        $this->loadListAdditionalFields($entity);
    }

    
    public function loadAdditionalFieldsForExport(Entity $entity)
    {}
}
