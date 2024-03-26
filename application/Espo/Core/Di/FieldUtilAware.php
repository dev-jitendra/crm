<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\FieldUtil;

interface FieldUtilAware
{
    public function setFieldUtil(FieldUtil $fieldUtil): void;
}
