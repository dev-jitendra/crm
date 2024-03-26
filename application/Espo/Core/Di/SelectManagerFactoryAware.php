<?php


namespace Espo\Core\Di;

use Espo\Core\Select\SelectManagerFactory;

interface SelectManagerFactoryAware
{
    public function setSelectManagerFactory(SelectManagerFactory $selectManagerFactory): void;
}
