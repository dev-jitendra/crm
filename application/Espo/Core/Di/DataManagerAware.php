<?php


namespace Espo\Core\Di;

use Espo\Core\DataManager;

interface DataManagerAware
{
    public function setDataManager(DataManager $dataManager): void;
}
