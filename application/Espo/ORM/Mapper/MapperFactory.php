<?php


namespace Espo\ORM\Mapper;

interface MapperFactory
{
    public function create(string $name): Mapper;
}
