<?php

namespace Dompdf\FrameDecorator;

use Dompdf\Dompdf;
use Dompdf\Frame;
use Dompdf\Exception;


class Text extends AbstractFrameDecorator
{
    
    protected $text_spacing;

    
    function __construct(Frame $frame, Dompdf $dompdf)
    {
        if (!$frame->is_text_node()) {
            throw new Exception("Text_Decorator can only be applied to #text nodes.");
        }

        parent::__construct($frame, $dompdf);
        $this->text_spacing = 0.0;
    }

    function reset()
    {
        parent::reset();
        $this->text_spacing = 0.0;
    }

    

    
    public function get_text_spacing(): float
    {
        return $this->text_spacing;
    }

    
    function get_text()
    {
        













        return $this->_frame->get_node()->data;
    }

    

    
    public function get_margin_height(): float
    {
        
        
        $style = $this->get_style();
        $font = $style->font_family;
        $size = $style->font_size;
        $fontHeight = $this->_dompdf->getFontMetrics()->getFontHeight($font, $size);

        return ($style->line_height / ($size > 0 ? $size : 1)) * $fontHeight;
    }

    public function get_padding_box(): array
    {
        $style = $this->_frame->get_style();
        $pb = $this->_frame->get_padding_box();
        $pb[3] = $pb["h"] = (float) $style->length_in_pt($style->height);
        return $pb;
    }

    
    public function set_text_spacing(float $spacing): void
    {
        $this->text_spacing = $spacing;
        $this->recalculate_width();
    }

    
    public function recalculate_width(): float
    {
        $fontMetrics = $this->_dompdf->getFontMetrics();
        $style = $this->get_style();
        $text = $this->get_text();
        $font = $style->font_family;
        $size = $style->font_size;
        $word_spacing = $this->text_spacing + $style->word_spacing;
        $letter_spacing = $style->letter_spacing;
        $text_width = $fontMetrics->getTextWidth($text, $font, $size, $word_spacing, $letter_spacing);

        $style->set_used("width", $text_width);
        return $text_width;
    }

    

    
    function split_text($offset)
    {
        if ($offset == 0) {
            return null;
        }

        $split = $this->_frame->get_node()->splitText($offset);
        if ($split === false) {
            return null;
        }
        
        $deco = $this->copy($split);

        $p = $this->get_parent();
        $p->insert_child_after($deco, $this, false);

        if ($p instanceof Inline) {
            $p->split($deco);
        }

        return $deco;
    }

    
    function delete_text($offset, $count)
    {
        $this->_frame->get_node()->deleteData($offset, $count);
    }

    
    function set_text($text)
    {
        $this->_frame->get_node()->data = $text;
    }
}
