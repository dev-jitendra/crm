<?php


namespace Espo\Core\Di;

use Espo\Core\Mail\EmailSender as EmailSender;

interface EmailSenderAware
{
    public function setEmailSender(EmailSender $emailSender): void;
}
