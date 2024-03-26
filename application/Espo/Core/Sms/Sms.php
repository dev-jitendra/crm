<?php


namespace Espo\Core\Sms;


interface Sms
{
    public function getBody(): string;

    public function getFromNumber(): ?string;

    public function getFromName(): ?string;

    
    public function getToNumberList(): array;
}
