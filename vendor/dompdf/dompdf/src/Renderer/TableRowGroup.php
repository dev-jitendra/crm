<?php

namespace Dompdf\Renderer;

use Dompdf\Frame;


class TableRowGroup extends Block
{

    
    function render(Frame $frame)
    {
        $style = $frame->get_style();

        $this->_set_opacity($frame->get_opacity($style->opacity));

        $border_box = $frame->get_border_box();

        $this->_render_border($frame, $border_box);
        $this->_render_outline($frame, $border_box);

        $id = $frame->get_node()->getAttribute("id");
        if (strlen($id) > 0) {
            $this->_canvas->add_named_dest($id);
        }

        $this->debugBlockLayout($frame, "red");
    }
}
