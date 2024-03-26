<?php


namespace Espo\Core\Mail;

use Espo\Core\Mail\Importer\Data;
use Espo\Entities\Email;


interface Importer
{
    public function import(Message $message, Data $data): ?Email;
}
