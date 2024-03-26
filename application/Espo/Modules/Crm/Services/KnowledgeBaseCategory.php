<?php


namespace Espo\Modules\Crm\Services;


class KnowledgeBaseCategory extends \Espo\Services\RecordTree
{
    protected $subjectEntityType = 'KnowledgeBaseArticle';

    protected $categoryField = 'categories';
}
