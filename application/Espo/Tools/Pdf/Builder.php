<?php


namespace Espo\Tools\Pdf;

use Espo\Core\InjectableFactory;
use RuntimeException;

class Builder
{
    private ?Template $template = null;
    private ?string $engine = null;

    public function __construct(private InjectableFactory $injectableFactory) {}

    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function setEngine(string $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    public function build(): PrinterController
    {
        if (!$this->engine) {
            throw new RuntimeException('Engine is not set.');
        }

        if (!$this->template) {
            throw new RuntimeException('Template is not set.');
        }

        return $this->injectableFactory->createWith(
            PrinterController::class,
            [
                'template' => $this->template,
                'engine' => $this->engine,
            ]
        );
    }
}
