<?php


namespace Espo\Core\Di;

use Espo\Core\Htmlizer\HtmlizerFactory as HtmlizerFactory;

interface HtmlizerFactoryAware
{
    public function setHtmlizerFactory(HtmlizerFactory $htmlizerFactory): void;
}
