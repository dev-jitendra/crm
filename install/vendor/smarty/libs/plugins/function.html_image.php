<?php



function smarty_function_html_image($params, $template)
{
    require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');

    $alt = '';
    $file = '';
    $height = '';
    $width = '';
    $extra = '';
    $prefix = '';
    $suffix = '';
    $path_prefix = '';
    $basedir = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'file':
            case 'height':
            case 'width':
            case 'dpi':
            case 'path_prefix':
            case 'basedir':
                $$_key = $_val;
                break;

            case 'alt':
                if (!is_array($_val)) {
                    $$_key = smarty_function_escape_special_chars($_val);
                } else {
                    throw new SmartyException ("html_image: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;

            case 'link':
            case 'href':
                $prefix = '<a href="' . $_val . '">';
                $suffix = '</a>';
                break;

            default:
                if (!is_array($_val)) {
                    $extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
                } else {
                    throw new SmartyException ("html_image: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (empty($file)) {
        trigger_error("html_image: missing 'file' parameter", E_USER_NOTICE);

        return;
    }

    if ($file[0] == '/') {
        $_image_path = $basedir . $file;
    } else {
        $_image_path = $file;
    }

    
    if (stripos($params['file'], 'file:
        $params['file'] = substr($params['file'], 7);
    }

    $protocol = strpos($params['file'], ':
    if ($protocol !== false) {
        $protocol = strtolower(substr($params['file'], 0, $protocol));
    }

    if (isset($template->smarty->security_policy)) {
        if ($protocol) {
            
            if (!$template->smarty->security_policy->isTrustedUri($params['file'])) {
                return;
            }
        } else {
            
            if (!$template->smarty->security_policy->isTrustedResourceDir($params['file'])) {
                return;
            }
        }
    }

    if (!isset($params['width']) || !isset($params['height'])) {
        
        if (!$_image_data = @getimagesize($_image_path)) {
            if (!file_exists($_image_path)) {
                trigger_error("html_image: unable to find '$_image_path'", E_USER_NOTICE);

                return;
            } elseif (!is_readable($_image_path)) {
                trigger_error("html_image: unable to read '$_image_path'", E_USER_NOTICE);

                return;
            } else {
                trigger_error("html_image: '$_image_path' is not a valid image file", E_USER_NOTICE);

                return;
            }
        }

        if (!isset($params['width'])) {
            $width = $_image_data[0];
        }
        if (!isset($params['height'])) {
            $height = $_image_data[1];
        }
    }

    if (isset($params['dpi'])) {
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
            
            
            $dpi_default = 72;
        } else {
            $dpi_default = 96;
        }
        $_resize = $dpi_default / $params['dpi'];
        $width = round($width * $_resize);
        $height = round($height * $_resize);
    }

    return $prefix . '<img src="' . $path_prefix . $file . '" alt="' . $alt . '" width="' . $width . '" height="' . $height . '"' . $extra . ' />' . $suffix;
}
