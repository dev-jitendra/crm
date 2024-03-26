<?php


namespace Espo\Classes\AppParams;

use Espo\Core\Acl;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Entities\Template;
use Espo\Tools\App\AppParam;


class TemplateEntityTypeList implements AppParam
{
    private Acl $acl;
    private SelectBuilderFactory $selectBuilderFactory;
    private EntityManager $entityManager;

    public function __construct(
        Acl $acl,
        SelectBuilderFactory $selectBuilderFactory,
        EntityManager $entityManager
    ) {
        $this->acl = $acl;
        $this->selectBuilderFactory = $selectBuilderFactory;
        $this->entityManager = $entityManager;
    }

    
    public function get(): array
    {
        if (!$this->acl->checkScope(Template::ENTITY_TYPE)) {
            return [];
        }

        $list = [];

        $query = $this->selectBuilderFactory
            ->create()
            ->from(Template::ENTITY_TYPE)
            ->withAccessControlFilter()
            ->buildQueryBuilder()
            ->select(['entityType'])
            ->group(['entityType'])
            ->build();

        $templateCollection = $this->entityManager
            ->getRDBRepositoryByClass(Template::class)
            ->clone($query)
            ->find();

        foreach ($templateCollection as $template) {
            $list[] = $template->getTargetEntityType();
        }

        return $list;
    }
}
