<?php


namespace Espo\Core\Exceptions;


interface HasBody
{
    
    public function getBody(): ?string;
}
