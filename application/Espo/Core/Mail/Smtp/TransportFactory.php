<?php


namespace Espo\Core\Mail\Smtp;

use Laminas\Mail\Transport\Smtp as SmtpTransport;

class TransportFactory
{
    public function create(): SmtpTransport
    {
        return new SmtpTransport();
    }
}
