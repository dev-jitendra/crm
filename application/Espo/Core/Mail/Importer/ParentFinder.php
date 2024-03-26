<?php


namespace Espo\Core\Mail\Importer;

use Espo\Entities\Email;
use Espo\Core\Mail\Message;
use Espo\ORM\Entity;


interface ParentFinder
{
    public function find(Email $email, Message $message): ?Entity;
}
