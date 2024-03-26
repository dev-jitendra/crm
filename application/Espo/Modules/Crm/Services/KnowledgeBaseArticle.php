<?php


namespace Espo\Modules\Crm\Services;

use Espo\Services\Record;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle as KnowledgeBaseArticleEntity;


class KnowledgeBaseArticle extends Record
{

    protected $readOnlyAttributeList = ['order'];
}
