<?php


namespace Espo\Tools\Pdf;

interface Template
{
    public const PAGE_FORMAT_CUSTOM = 'Custom';

    public const PAGE_ORIENTATION_PORTRAIT = 'Portrait';
    public const PAGE_ORIENTATION_LANDSCAPE = 'Landscape';

    public function getFontFace(): ?string;

    public function getBottomMargin(): float;

    public function getTopMargin(): float;

    public function getLeftMargin(): float;

    public function getRightMargin(): float;

    public function hasFooter(): bool;

    public function getFooter(): string;

    public function getFooterPosition(): float;

    public function hasHeader(): bool;

    public function getHeader(): string;

    public function getHeaderPosition(): float;

    public function getBody(): string;

    public function getPageOrientation(): string;

    public function getPageFormat(): string;

    public function getPageWidth(): float;

    public function getPageHeight(): float;

    public function hasTitle(): bool;

    public function getTitle(): string;
}
