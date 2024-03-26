<?php


namespace Espo\Tools\Pdf;

use Espo\Core\Acl;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Utils\Config;
use Espo\Entities\Template as TemplateEntity;
use Espo\ORM\EntityManager;
use Espo\Tools\Pdf\Data\DataLoaderManager;

class Service
{
    private const DEFAULT_ENGINE = 'Dompdf';

    private EntityManager $entityManager;
    private Acl $acl;
    private ServiceContainer $serviceContainer;
    private DataLoaderManager $dataLoaderManager;
    private Config $config;
    private Builder $builder;

    public function __construct(
        EntityManager $entityManager,
        Acl $acl,
        ServiceContainer $serviceContainer,
        DataLoaderManager $dataLoaderManager,
        Config $config,
        Builder $builder
    ) {
        $this->entityManager = $entityManager;
        $this->acl = $acl;
        $this->serviceContainer = $serviceContainer;
        $this->dataLoaderManager = $dataLoaderManager;
        $this->config = $config;
        $this->builder = $builder;
    }

    
    public function generate(
        string $entityType,
        string $id,
        string $templateId,
        ?Params $params = null,
        ?Data $data = null
    ): Contents {

        $params = $params ?? Params::create()->withAcl(true);

        $applyAcl = $params->applyAcl();

        $entity = $this->entityManager->getEntityById($entityType, $id);

        if (!$entity) {
            throw new NotFound("Record not found.");
        }

        
        $template = $this->entityManager->getEntityById(TemplateEntity::ENTITY_TYPE, $templateId);

        if (!$template) {
            throw new NotFound("Template not found.");
        }

        if ($applyAcl && !$this->acl->checkEntityRead($entity)) {
            throw new Forbidden("No access to record.");
        }

        if ($applyAcl && !$this->acl->checkEntityRead($template)) {
            throw new Forbidden("No access to template.");
        }

        $service = $this->serviceContainer->get($entityType);

        $service->loadAdditionalFields($entity);

        if (method_exists($service, 'loadAdditionalFieldsForPdf')) {
            
            $service->loadAdditionalFieldsForPdf($entity);
        }

        if ($template->getTargetEntityType() !== $entityType) {
            throw new Error("Not matching entity types.");
        }

        $data = $this->dataLoaderManager->load($entity, $params, $data);
        $engine = $this->config->get('pdfEngine') ?? self::DEFAULT_ENGINE;

        $printer = $this->builder
            ->setTemplate(new TemplateWrapper($template))
            ->setEngine($engine)
            ->build();

        return $printer->printEntity($entity, $params, $data);
    }
}
