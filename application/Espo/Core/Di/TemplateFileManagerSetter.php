<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\TemplateFileManager;

trait TemplateFileManagerSetter
{
    
    protected $templateFileManager;

    public function setTemplateFileManager(TemplateFileManager $templateFileManager): void
    {
        $this->templateFileManager = $templateFileManager;
    }
}
