<?php



class Smarty_Internal_Write_File
{
    
    public static function writeFile($_filepath, $_contents, Smarty $smarty)
    {
        $_error_reporting = error_reporting();
        error_reporting($_error_reporting & ~E_NOTICE & ~E_WARNING);
        if ($smarty->_file_perms !== null) {
            $old_umask = umask(0);
        }

        $_dirpath = dirname($_filepath);
        
        if ($_dirpath !== '.' && !file_exists($_dirpath)) {
            mkdir($_dirpath, $smarty->_dir_perms === null ? 0777 : $smarty->_dir_perms, true);
        }

        
        $_tmp_file = $_dirpath . DS . uniqid('wrt', true);
        if (!file_put_contents($_tmp_file, $_contents)) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_tmp_file}");

            return false;
        }

        
        if (Smarty::$_IS_WINDOWS) {
            
            @unlink($_filepath);
            
            $success = @rename($_tmp_file, $_filepath);
        } else {
            
            $success = @rename($_tmp_file, $_filepath);
            if (!$success) {
                
                @unlink($_filepath);
                
                $success = @rename($_tmp_file, $_filepath);
            }
        }

        if (!$success) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_filepath}");

            return false;
        }

        if ($smarty->_file_perms !== null) {
            
            chmod($_filepath, $smarty->_file_perms);
            umask($old_umask);
        }
        error_reporting($_error_reporting);

        return true;
    }

}
