<?php



class Smarty_Internal_Resource_Stream extends Smarty_Resource_Recompiled
{
    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        if (strpos($source->resource, ':
            $source->filepath = $source->resource;
        } else {
            $source->filepath = str_replace(':', ':
        }
        $source->uid = false;
        $source->content = $this->getContent($source);
        $source->timestamp = false;
        $source->exists = !!$source->content;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        $t = '';
        
        $fp = fopen($source->filepath, 'r+');
        if ($fp) {
            while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
                $t .= $current_line;
            }
            fclose($fp);

            return $t;
        } else {
            return false;
        }
    }

    
    protected function buildUniqueResourceName(Smarty $smarty, $resource_name, $is_config = false)
    {
        return get_class($this) . '#' . $resource_name;
    }
}
