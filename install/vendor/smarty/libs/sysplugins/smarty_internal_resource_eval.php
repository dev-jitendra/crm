<?php



class Smarty_Internal_Resource_Eval extends Smarty_Resource_Recompiled
{
    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $source->uid = $source->filepath = sha1($source->name);
        $source->timestamp = false;
        $source->exists = true;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        return $this->decode($source->name);
    }

    
    protected function decode($string)
    {
        
        if (($pos = strpos($string, ':')) !== false) {
            if (!strncmp($string, 'base64', 6)) {
                return base64_decode(substr($string, 7));
            } elseif (!strncmp($string, 'urlencode', 9)) {
                return urldecode(substr($string, 10));
            }
        }

        return $string;
    }

    
    protected function buildUniqueResourceName(Smarty $smarty, $resource_name, $is_config = false)
    {
        return get_class($this) . '#' .$this->decode($resource_name);
    }

    
    protected function getBasename(Smarty_Template_Source $source)
    {
        return '';
    }

}
