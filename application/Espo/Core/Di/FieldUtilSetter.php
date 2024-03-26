<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\FieldUtil;

trait FieldUtilSetter
{
    
    protected $fieldUtil;

    
    protected $fieldManagerUtil;

    public function setFieldUtil(FieldUtil $fieldUtil): void
    {
        $this->fieldUtil = $fieldUtil;

        
        
        $this->fieldManagerUtil = $fieldUtil;
    }
}
