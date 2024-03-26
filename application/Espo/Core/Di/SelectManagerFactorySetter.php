<?php


namespace Espo\Core\Di;

use Espo\Core\Select\SelectManagerFactory;

trait SelectManagerFactorySetter
{
    
    protected $selectManagerFactory;

    public function setSelectManagerFactory(SelectManagerFactory $selectManagerFactory): void
    {
        $this->selectManagerFactory = $selectManagerFactory;
    }
}
