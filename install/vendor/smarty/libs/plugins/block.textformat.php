<?php



function smarty_block_textformat($params, $content, $template, &$repeat)
{
    if (is_null($content)) {
        return;
    }

    $style = null;
    $indent = 0;
    $indent_first = 0;
    $indent_char = ' ';
    $wrap = 80;
    $wrap_char = "\n";
    $wrap_cut = false;
    $assign = null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'style':
            case 'indent_char':
            case 'wrap_char':
            case 'assign':
                $$_key = (string) $_val;
                break;

            case 'indent':
            case 'indent_first':
            case 'wrap':
                $$_key = (int) $_val;
                break;

            case 'wrap_cut':
                $$_key = (bool) $_val;
                break;

            default:
                trigger_error("textformat: unknown attribute '$_key'");
        }
    }

    if ($style == 'email') {
        $wrap = 72;
    }
    
    $_paragraphs = preg_split('![\r\n]{2}!', $content);
    $_output = '';

    foreach ($_paragraphs as &$_paragraph) {
        if (!$_paragraph) {
            continue;
        }
        
        $_paragraph = preg_replace(array('!\s+!' . Smarty::$_UTF8_MODIFIER, '!(^\s+)|(\s+$)!' . Smarty::$_UTF8_MODIFIER), array(' ', ''), $_paragraph);
        
        if ($indent_first > 0) {
            $_paragraph = str_repeat($indent_char, $indent_first) . $_paragraph;
        }
        
        if (Smarty::$_MBSTRING) {
            require_once(SMARTY_PLUGINS_DIR . 'shared.mb_wordwrap.php');
            $_paragraph = smarty_mb_wordwrap($_paragraph, $wrap - $indent, $wrap_char, $wrap_cut);
        } else {
            $_paragraph = wordwrap($_paragraph, $wrap - $indent, $wrap_char, $wrap_cut);
        }
        
        if ($indent > 0) {
            $_paragraph = preg_replace('!^!m', str_repeat($indent_char, $indent), $_paragraph);
        }
    }
    $_output = implode($wrap_char . $wrap_char, $_paragraphs);

    if ($assign) {
        $template->assign($assign, $_output);
    } else {
        return $_output;
    }
}
