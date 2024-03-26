<?php


namespace Espo\Core\Mail\Account;

use Espo\Core\Mail\Account\Storage\Params;

interface StorageFactory
{
    public function create(Account $account): Storage;

    public function createWithParams(Params $params): Storage;
}
