<?php


namespace Espo\Tools\Pdf\Dompdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Espo\Core\Utils\Config;
use Espo\Tools\Pdf\Template;

class DompdfInitializer
{
    private string $defaultFontFace = 'DejaVu Sans';

    public function __construct(
        private Config $config
    ) {}

    public function initialize(Template $template): Dompdf
    {
        $options = new Options();

        $options->setDefaultFont($this->getFontFace($template));

        $pdf = new Dompdf($options);

        $size = $template->getPageFormat() === Template::PAGE_FORMAT_CUSTOM ?
            [0.0, 0.0, $template->getPageWidth(), $template->getPageHeight()] :
            $template->getPageFormat();

        $orientation = $template->getPageOrientation() === Template::PAGE_ORIENTATION_PORTRAIT ?
            'portrait' :
            'landscape';

        $pdf->setPaper($size, $orientation);

        return $pdf;
    }

    private function getFontFace(Template $template): string
    {
        return
            $template->getFontFace() ??
            $this->config->get('pdfFontFace') ??
            $this->defaultFontFace;
    }
}
