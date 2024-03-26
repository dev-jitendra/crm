<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Common\Exception\InvalidArgumentException;


final class Style
{
    
    public const DEFAULT_FONT_SIZE = 11;
    public const DEFAULT_FONT_COLOR = Color::BLACK;
    public const DEFAULT_FONT_NAME = 'Arial';

    
    private int $id = -1;

    
    private bool $fontBold = false;

    
    private bool $hasSetFontBold = false;

    
    private bool $fontItalic = false;

    
    private bool $hasSetFontItalic = false;

    
    private bool $fontUnderline = false;

    
    private bool $hasSetFontUnderline = false;

    
    private bool $fontStrikethrough = false;

    
    private bool $hasSetFontStrikethrough = false;

    
    private int $fontSize = self::DEFAULT_FONT_SIZE;

    
    private bool $hasSetFontSize = false;

    
    private string $fontColor = self::DEFAULT_FONT_COLOR;

    
    private bool $hasSetFontColor = false;

    
    private string $fontName = self::DEFAULT_FONT_NAME;

    
    private bool $hasSetFontName = false;

    
    private bool $shouldApplyFont = false;

    
    private bool $shouldApplyCellAlignment = false;

    
    private string $cellAlignment;

    
    private bool $hasSetCellAlignment = false;

    
    private bool $shouldApplyCellVerticalAlignment = false;

    
    private string $cellVerticalAlignment;

    
    private bool $hasSetCellVerticalAlignment = false;

    
    private bool $shouldWrapText = false;

    
    private bool $hasSetWrapText = false;

    
    private bool $shouldShrinkToFit = false;

    
    private bool $hasSetShrinkToFit = false;

    private ?Border $border = null;

    
    private ?string $backgroundColor = null;

    
    private ?string $format = null;

    private bool $isRegistered = false;

    private bool $isEmpty = true;

    public function __sleep(): array
    {
        $vars = get_object_vars($this);
        unset($vars['id'], $vars['isRegistered']);

        return array_keys($vars);
    }

    public function getId(): int
    {
        \assert(0 <= $this->id);

        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getBorder(): ?Border
    {
        return $this->border;
    }

    public function setBorder(Border $border): self
    {
        $this->border = $border;
        $this->isEmpty = false;

        return $this;
    }

    public function isFontBold(): bool
    {
        return $this->fontBold;
    }

    public function setFontBold(): self
    {
        $this->fontBold = true;
        $this->hasSetFontBold = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontBold(): bool
    {
        return $this->hasSetFontBold;
    }

    public function isFontItalic(): bool
    {
        return $this->fontItalic;
    }

    public function setFontItalic(): self
    {
        $this->fontItalic = true;
        $this->hasSetFontItalic = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontItalic(): bool
    {
        return $this->hasSetFontItalic;
    }

    public function isFontUnderline(): bool
    {
        return $this->fontUnderline;
    }

    public function setFontUnderline(): self
    {
        $this->fontUnderline = true;
        $this->hasSetFontUnderline = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontUnderline(): bool
    {
        return $this->hasSetFontUnderline;
    }

    public function isFontStrikethrough(): bool
    {
        return $this->fontStrikethrough;
    }

    public function setFontStrikethrough(): self
    {
        $this->fontStrikethrough = true;
        $this->hasSetFontStrikethrough = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontStrikethrough(): bool
    {
        return $this->hasSetFontStrikethrough;
    }

    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    
    public function setFontSize(int $fontSize): self
    {
        $this->fontSize = $fontSize;
        $this->hasSetFontSize = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontSize(): bool
    {
        return $this->hasSetFontSize;
    }

    public function getFontColor(): string
    {
        return $this->fontColor;
    }

    
    public function setFontColor(string $fontColor): self
    {
        $this->fontColor = $fontColor;
        $this->hasSetFontColor = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontColor(): bool
    {
        return $this->hasSetFontColor;
    }

    public function getFontName(): string
    {
        return $this->fontName;
    }

    
    public function setFontName(string $fontName): self
    {
        $this->fontName = $fontName;
        $this->hasSetFontName = true;
        $this->shouldApplyFont = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetFontName(): bool
    {
        return $this->hasSetFontName;
    }

    public function getCellAlignment(): string
    {
        return $this->cellAlignment;
    }

    public function getCellVerticalAlignment(): string
    {
        return $this->cellVerticalAlignment;
    }

    
    public function setCellAlignment(string $cellAlignment): self
    {
        if (!CellAlignment::isValid($cellAlignment)) {
            throw new InvalidArgumentException('Invalid cell alignment value');
        }

        $this->cellAlignment = $cellAlignment;
        $this->hasSetCellAlignment = true;
        $this->shouldApplyCellAlignment = true;
        $this->isEmpty = false;

        return $this;
    }

    
    public function setCellVerticalAlignment(string $cellVerticalAlignment): self
    {
        if (!CellVerticalAlignment::isValid($cellVerticalAlignment)) {
            throw new InvalidArgumentException('Invalid cell vertical alignment value');
        }

        $this->cellVerticalAlignment = $cellVerticalAlignment;
        $this->hasSetCellVerticalAlignment = true;
        $this->shouldApplyCellVerticalAlignment = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetCellAlignment(): bool
    {
        return $this->hasSetCellAlignment;
    }

    public function hasSetCellVerticalAlignment(): bool
    {
        return $this->hasSetCellVerticalAlignment;
    }

    
    public function shouldApplyCellAlignment(): bool
    {
        return $this->shouldApplyCellAlignment;
    }

    public function shouldApplyCellVerticalAlignment(): bool
    {
        return $this->shouldApplyCellVerticalAlignment;
    }

    public function shouldWrapText(): bool
    {
        return $this->shouldWrapText;
    }

    
    public function setShouldWrapText(bool $shouldWrap = true): self
    {
        $this->shouldWrapText = $shouldWrap;
        $this->hasSetWrapText = true;
        $this->isEmpty = false;

        return $this;
    }

    public function hasSetWrapText(): bool
    {
        return $this->hasSetWrapText;
    }

    
    public function shouldApplyFont(): bool
    {
        return $this->shouldApplyFont;
    }

    
    public function setBackgroundColor(string $color): self
    {
        $this->backgroundColor = $color;
        $this->isEmpty = false;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    
    public function setFormat(string $format): self
    {
        $this->format = $format;
        $this->isEmpty = false;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    public function markAsRegistered(?int $id): void
    {
        $this->setId($id);
        $this->isRegistered = true;
    }

    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    
    public function setShouldShrinkToFit(bool $shrinkToFit = true): self
    {
        $this->hasSetShrinkToFit = true;
        $this->shouldShrinkToFit = $shrinkToFit;

        return $this;
    }

    
    public function shouldShrinkToFit(): bool
    {
        return $this->shouldShrinkToFit;
    }

    public function hasSetShrinkToFit(): bool
    {
        return $this->hasSetShrinkToFit;
    }
}
