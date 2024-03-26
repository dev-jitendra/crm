<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class ExceptionHandler
{
    
    public function __construct()
    {
        set_error_handler([Exception::class, 'errorHandlerCallback'], E_ALL);
    }

    
    public function __destruct()
    {
        restore_error_handler();
    }
}
