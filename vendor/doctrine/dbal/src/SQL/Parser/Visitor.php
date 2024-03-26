<?php

namespace Doctrine\DBAL\SQL\Parser;


interface Visitor
{
    
    public function acceptPositionalParameter(string $sql): void;

    
    public function acceptNamedParameter(string $sql): void;

    
    public function acceptOther(string $sql): void;
}
