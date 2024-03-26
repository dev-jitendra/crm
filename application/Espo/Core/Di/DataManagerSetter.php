<?php


namespace Espo\Core\Di;

use Espo\Core\DataManager;

trait DataManagerSetter
{
    
    protected $dataManager;

    public function setDataManager(DataManager $dataManager): void
    {
        $this->dataManager = $dataManager;
    }
}
