<?php


namespace Espo\Modules\Crm\Services;


class DocumentFolder extends \Espo\Services\RecordTree
{
    protected $subjectEntityType = 'Document';

    protected $categoryField = 'folder';
}
