<?php


namespace Espo\Core\Htmlizer;

use Espo\Core\Htmlizer\Helper\Data;
use Espo\Core\Htmlizer\Helper\Result;

interface Helper
{
    public function render(Data $data): Result;
}
