<?php


namespace Espo\Modules\Crm\Tools\MassEmail;

use Espo\Modules\Crm\Tools\MassEmail\MessagePreparator\Data;
use Laminas\Mail\Headers;


interface MessageHeadersPreparator
{
    public function prepare(Headers $headers, Data $data): void;
}
