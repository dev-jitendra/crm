<?php


namespace Espo\Modules\Crm\Services;

use Espo\Services\Record;


class Opportunity extends Record
{
    protected $mandatorySelectAttributeList = [
        'accountId',
        'accountName',
    ];
}
