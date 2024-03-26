<?php


namespace Espo\Tools\Pdf\Dompdf;

use Espo\ORM\Entity;
use Espo\Tools\Pdf\Contents;
use Espo\Tools\Pdf\Data;
use Espo\Tools\Pdf\Dompdf\Contents as DompdfContents;
use Espo\Tools\Pdf\EntityPrinter as EntityPrinterInterface;
use Espo\Tools\Pdf\Params;
use Espo\Tools\Pdf\Template;

class EntityPrinter implements EntityPrinterInterface
{
    public function __construct(
        private DompdfInitializer $dompdfInitializer,
        private HtmlComposer $htmlComposer
    ) {}

    public function print(Template $template, Entity $entity, Params $params, Data $data): Contents
    {
        $pdf = $this->dompdfInitializer->initialize($template);

        $headHtml = $this->htmlComposer->composeHead($template, $entity);
        $headerFooterHtml = $this->htmlComposer->composeHeaderFooter($template, $entity, $params, $data);
        $mainHtml = $this->htmlComposer->composeMain($template, $entity, $params, $data);

        $html = $headHtml . "\n<body>" . $headerFooterHtml . $mainHtml . "</body>";

        $pdf->loadHtml($html);
        $pdf->render();

        return new DompdfContents($pdf);
    }
}
