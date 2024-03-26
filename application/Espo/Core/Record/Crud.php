<?php


namespace Espo\Core\Record;

use Espo\ORM\Entity;

use stdClass;


interface Crud
{
    
    public function create(stdClass $data, CreateParams $params): Entity;

    
    public function read(string $id, ReadParams $params): Entity;

    
    public function update(string $id, stdClass $data, UpdateParams $params): Entity;

    
    public function delete(string $id, DeleteParams $params): void;
}
