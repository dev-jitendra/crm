<?php

namespace PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;



















use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;


class File extends PPS
{
    
    public function __construct($name)
    {
        parent::__construct(null, $name, OLE::OLE_PPS_TYPE_FILE, null, null, null, null, null, '', []);
    }

    
    public function init()
    {
        return true;
    }

    
    public function append($data): void
    {
        $this->_data .= $data;
    }
}
