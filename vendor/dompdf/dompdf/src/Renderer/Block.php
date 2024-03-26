<?php

namespace Dompdf\Renderer;

use Dompdf\Frame;
use Dompdf\FrameDecorator\Block as BlockFrameDecorator;
use Dompdf\Helpers;


class Block extends AbstractRenderer
{

    
    function render(Frame $frame)
    {
        $style = $frame->get_style();
        $node = $frame->get_node();
        $dompdf = $this->_dompdf;

        $this->_set_opacity($frame->get_opacity($style->opacity));

        [$x, $y, $w, $h] = $frame->get_border_box();

        if ($node->nodeName === "body") {
            
            $mt = $style->margin_top;
            $mb = $style->margin_bottom;
            $h = $frame->get_containing_block("h") - $mt - $mb;
        }

        $border_box = [$x, $y, $w, $h];

        
        $this->_render_background($frame, $border_box);
        $this->_render_border($frame, $border_box);
        $this->_render_outline($frame, $border_box);

        
        if ($node->nodeName === "a" && $href = $node->getAttribute("href")) {
            $href = Helpers::build_url($dompdf->getProtocol(), $dompdf->getBaseHost(), $dompdf->getBasePath(), $href) ?? $href;
            $this->_canvas->add_link($href, $x, $y, $w, $h);
        }

        $id = $frame->get_node()->getAttribute("id");
        if (strlen($id) > 0) {
            $this->_canvas->add_named_dest($id);
        }

        $this->debugBlockLayout($frame, "red", false);
    }

    protected function debugBlockLayout(Frame $frame, ?string $color, bool $lines = false): void
    {
        $options = $this->_dompdf->getOptions();
        $debugLayout = $options->getDebugLayout();

        if (!$debugLayout) {
            return;
        }

        if ($color && $options->getDebugLayoutBlocks()) {
            $this->_debug_layout($frame->get_border_box(), $color);

            if ($options->getDebugLayoutPaddingBox()) {
                $this->_debug_layout($frame->get_padding_box(), $color, [0.5, 0.5]);
            }
        }

        if ($lines && $options->getDebugLayoutLines() && $frame instanceof BlockFrameDecorator) {
            [$cx, , $cw] = $frame->get_content_box();

            foreach ($frame->get_line_boxes() as $line) {
                $lw = $cw - $line->left - $line->right;
                $this->_debug_layout([$cx + $line->left, $line->y, $lw, $line->h], "orange");
            }
        }
    }
}
