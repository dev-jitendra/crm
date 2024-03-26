<?php


namespace Espo\Core\Di;

use Espo\Core\Mail\EmailSender as EmailSender;

trait EmailSenderSetter
{
    
    protected $emailSender;

    public function setEmailSender(EmailSender $emailSender): void
    {
        $this->emailSender = $emailSender;
    }
}
