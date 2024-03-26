<?php


namespace Espo\Modules\Crm\Services;

use Espo\Services\Record;


class Account extends Record
{
    protected $linkMandatorySelectAttributeList = [
        'contacts' => ['accountIsInactive'],
        'targetLists' => ['isOptedOut'],
    ];
}
