<?php


namespace Espo\Core\Di;

use Espo\Core\Select\SelectBuilderFactory;

trait SelectBuilderFactorySetter
{
    
    protected $selectBuilderFactory;

    public function setSelectBuilderFactory(SelectBuilderFactory $selectBuilderFactory): void
    {
        $this->selectBuilderFactory = $selectBuilderFactory;
    }
}
