<?php


namespace Espo\Core\Sms;


interface Sender
{
    public function send(Sms $sms): void;
}
