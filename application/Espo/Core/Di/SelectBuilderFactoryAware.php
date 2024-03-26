<?php


namespace Espo\Core\Di;

use Espo\Core\Select\SelectBuilderFactory;

interface SelectBuilderFactoryAware
{
    public function setSelectBuilderFactory(SelectBuilderFactory $selectBuilderFactory): void;
}
