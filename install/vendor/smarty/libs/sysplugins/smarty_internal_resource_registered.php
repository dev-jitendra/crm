<?php



class Smarty_Internal_Resource_Registered extends Smarty_Resource
{
    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $source->filepath = $source->type . ':' . $source->name;
        $source->uid = sha1($source->filepath);
        if ($source->smarty->compile_check) {
            $source->timestamp = $this->getTemplateTimestamp($source);
            $source->exists = !!$source->timestamp;
        }
    }

    
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->timestamp = $this->getTemplateTimestamp($source);
        $source->exists = !!$source->timestamp;
    }

    
    public function getTemplateTimestamp(Smarty_Template_Source $source)
    {
        
        $time_stamp = false;
        call_user_func_array($source->smarty->registered_resources[$source->type][0][1], array($source->name, &$time_stamp, $source->smarty));

        return is_numeric($time_stamp) ? (int) $time_stamp : $time_stamp;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        
        $t = call_user_func_array($source->smarty->registered_resources[$source->type][0][0], array($source->name, &$source->content, $source->smarty));
        if (is_bool($t) && !$t) {
            throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
        }

        return $source->content;
    }

    
    protected function getBasename(Smarty_Template_Source $source)
    {
        return basename($source->name);
    }

}
