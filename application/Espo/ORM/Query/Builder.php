<?php


namespace Espo\ORM\Query;


interface Builder
{
    
    public function build(): Query;
}
