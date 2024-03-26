<?php


namespace Espo\Classes\TemplateHelpers;

use Espo\Core\Htmlizer\Helper;
use Espo\Core\Htmlizer\Helper\Data;
use Espo\Core\Htmlizer\Helper\Result;

class TrTag implements Helper
{
    public function render(Data $data): Result
    {
        $function = $data->getFunction();

        $content = $function !== null ? $function() : '';

        return Result::createSafeString(
            "<tr>" . $content . "</tr>"
        );
    }
}
