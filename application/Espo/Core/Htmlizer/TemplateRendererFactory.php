<?php


namespace Espo\Core\Htmlizer;

use Espo\Core\InjectableFactory;

class TemplateRendererFactory
{
    private $injectableFactory;

    public function __construct(InjectableFactory $injectableFactory)
    {
        $this->injectableFactory = $injectableFactory;
    }

    public function create(): TemplateRenderer
    {
        return $this->injectableFactory->create(TemplateRenderer::class);
    }
}
