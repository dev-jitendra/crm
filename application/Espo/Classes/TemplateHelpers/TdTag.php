<?php


namespace Espo\Classes\TemplateHelpers;

use Espo\Core\Htmlizer\Helper;
use Espo\Core\Htmlizer\Helper\Data;
use Espo\Core\Htmlizer\Helper\Result;

class TdTag implements Helper
{
    public function render(Data $data): Result
    {
        $align = strtolower($data->getOption('align') ?? 'left');

        if (!in_array($align, ['left', 'right', 'center'])) {
            $align = 'left';
        }

        $width = $data->getOption('width') ?? null;

        $attributesPart = "align=\"{$align}\"";

        if ($width) {
            $attributesPart .= " width=\"{$width}\"";
        }

        $function = $data->getFunction();

        $content = $function !== null ? $function() : '';

        return Result::createSafeString(
            "<td {$attributesPart}>{$content}</td>"
        );
    }
}
