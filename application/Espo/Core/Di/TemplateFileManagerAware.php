<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\TemplateFileManager;

interface TemplateFileManagerAware
{
    public function setTemplateFileManager(TemplateFileManager $templateFileManager): void;
}
