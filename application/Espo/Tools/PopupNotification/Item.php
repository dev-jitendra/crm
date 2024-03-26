<?php


namespace Espo\Tools\PopupNotification;

use stdClass;

class Item
{
    private ?string $id;
    private stdClass $data;

    
    public function __construct(
        ?string $id,
        stdClass $data
    ) {
        $this->id = $id;
        $this->data = $data;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getData(): stdClass
    {
        return $this->data;
    }
}
