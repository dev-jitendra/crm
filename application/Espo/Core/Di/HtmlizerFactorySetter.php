<?php


namespace Espo\Core\Di;

use Espo\Core\Htmlizer\HtmlizerFactory as HtmlizerFactory;

trait HtmlizerFactorySetter
{
    
    protected $htmlizerFactory;

    public function setHtmlizerFactory(HtmlizerFactory $htmlizerFactory): void
    {
        $this->htmlizerFactory = $htmlizerFactory;
    }
}
