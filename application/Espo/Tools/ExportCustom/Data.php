<?php


namespace Espo\Tools\ExportCustom;

class Data
{
    
    public function __construct(
        public string $folder,
        public array $customEntityTypeList,
        private string $module
    ) {}

    public function getDir(): string
    {
        return 'data/tmp/' . $this->folder;
    }

    public function getDestDir(): string
    {
        return $this->getDir() . '/files/custom/Espo/Modules/' . $this->module;
    }
}
