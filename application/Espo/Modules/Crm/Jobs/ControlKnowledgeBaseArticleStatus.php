<?php


namespace Espo\Modules\Crm\Jobs;

use Espo\Core\Utils\DateTime;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\Core\Job\JobDataLess;
use Espo\Core\ORM\EntityManager;

class ControlKnowledgeBaseArticleStatus implements JobDataLess
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(): void
    {
        $list = $this->entityManager
            ->getRDBRepository(KnowledgeBaseArticle::ENTITY_TYPE)
            ->where([
                'expirationDate<=' => date(DateTime::SYSTEM_DATE_FORMAT),
                'status' => KnowledgeBaseArticle::STATUS_PUBLISHED,
            ])
            ->find();

        foreach ($list as $e) {
            $e->set('status', KnowledgeBaseArticle::STATUS_ARCHIVED);

            $this->entityManager->saveEntity($e);
        }
    }
}
