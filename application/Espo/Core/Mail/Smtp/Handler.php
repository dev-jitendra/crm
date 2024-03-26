<?php


namespace Espo\Core\Mail\Smtp;

use Espo\Core\Mail\SmtpParams;

interface Handler
{
    public function handle(SmtpParams $params, ?string $id): SmtpParams;
}
