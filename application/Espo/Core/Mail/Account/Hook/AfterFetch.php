<?php


namespace Espo\Core\Mail\Account\Hook;

use Espo\Core\Mail\Account\Account;
use Espo\Entities\Email;

interface AfterFetch
{
    public function process(Account $account, Email $email, BeforeFetchResult $beforeFetchResult): void;
}
