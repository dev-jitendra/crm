<?php


namespace Espo\Tools\Pdf;

use Espo\Entities\Template as TemplateEntity;

class TemplateWrapper implements Template
{
    protected TemplateEntity $template;

    public function __construct(TemplateEntity $template)
    {
        $this->template = $template;
    }

    public function getFontFace(): ?string
    {
        return $this->template->get('fontFace');
    }

    public function getBottomMargin(): float
    {
        return $this->template->get('bottomMargin') ?? 0.0;
    }

    public function getTopMargin(): float
    {
        return $this->template->get('topMargin') ?? 0.0;
    }

    public function getLeftMargin(): float
    {
        return $this->template->get('leftMargin') ?? 0.0;
    }

    public function getRightMargin(): float
    {
        return $this->template->get('rightMargin') ?? 0.0;
    }

    public function hasFooter(): bool
    {
        return $this->template->get('printFooter') ?? false;
    }

    public function getFooter(): string
    {
        return $this->template->get('footer') ?? '';
    }

    public function getFooterPosition(): float
    {
        return $this->template->get('footerPosition') ?? 0.0;
    }

    public function hasHeader(): bool
    {
        return $this->template->get('printHeader') ?? false;
    }

    public function getHeader(): string
    {
        return $this->template->get('header') ?? '';
    }

    public function getHeaderPosition(): float
    {
        return $this->template->get('headerPosition') ?? 0.0;
    }

    public function getBody(): string
    {
        return $this->template->get('body') ?? '';
    }

    public function getPageOrientation(): string
    {
        return $this->template->get('pageOrientation') ?? 'Portrait';
    }

    public function getPageFormat(): string
    {
        return $this->template->get('pageFormat') ?? 'A4';
    }

    public function getPageWidth(): float
    {
        return $this->template->get('pageWidth') ?? 0.0;
    }

    public function getPageHeight(): float
    {
        return $this->template->get('pageHeight') ?? 0.0;
    }

    public function hasTitle(): bool
    {
        return $this->template->get('title') !== null;
    }

    public function getTitle(): string
    {
        return $this->template->get('title') ?? '';
    }
}
