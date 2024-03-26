<?php


namespace Espo\Core\Mail\Account\Hook;

use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Message;

interface BeforeFetch
{
    public function process(Account $account, Message $message): BeforeFetchResult;
}
