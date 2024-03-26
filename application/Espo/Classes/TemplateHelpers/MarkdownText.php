<?php


namespace Espo\Classes\TemplateHelpers;

use Espo\Core\Htmlizer\Helper;
use Espo\Core\Htmlizer\Helper\Data;
use Espo\Core\Htmlizer\Helper\Result;
use Michelf\MarkdownExtra as MarkdownTransformer;

class MarkdownText implements Helper
{
    public function render(Data $data): Result
    {
        $value = $data->getArgumentList()[0] ?? null;

        if (!$value || !is_string($value)) {
            return Result::createEmpty();
        }

        $transformed = MarkdownTransformer::defaultTransform($value);

        return Result::createSafeString($transformed);
    }
}
