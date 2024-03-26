<?php


namespace Espo\Controllers;

use Espo\Tools\PopupNotification\Service as Service;

use stdClass;

class PopupNotification
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function getActionGrouped(): stdClass
    {
        $grouped = $this->service->getGrouped();

        $result = (object) [];

        foreach ($grouped as $type => $itemList) {
            $rawList = array_map(
                function ($item) {
                    return (object) [
                        'id' => $item->getId(),
                        'data' => $item->getData(),
                    ];
                },
                $itemList
            );
            $result->$type = $rawList;
        }

        return $result;
    }
}
