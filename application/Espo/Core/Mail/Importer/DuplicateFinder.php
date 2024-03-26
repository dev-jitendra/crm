<?php


namespace Espo\Core\Mail\Importer;

use Espo\Core\Mail\Message;
use Espo\Entities\Email;


interface DuplicateFinder
{
    public function find(Email $email, Message $message): ?Email;
}
