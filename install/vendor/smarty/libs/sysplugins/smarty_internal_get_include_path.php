<?php



class Smarty_Internal_Get_Include_Path
{
    
    public static function getIncludePath($filepath)
    {
        static $_include_path = null;

        if (function_exists('stream_resolve_include_path')) {
            
            return stream_resolve_include_path($filepath);
        }

        if ($_include_path === null) {
            $_include_path = explode(PATH_SEPARATOR, get_include_path());
        }

        foreach ($_include_path as $_path) {
            if (file_exists($_path . DS . $filepath)) {
                return $_path . DS . $filepath;
            }
        }

        return false;
    }

}
