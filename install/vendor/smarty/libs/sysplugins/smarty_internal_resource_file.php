<?php



class Smarty_Internal_Resource_File extends Smarty_Resource
{
    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $source->filepath = $this->buildFilepath($source, $_template);

        if ($source->filepath !== false) {
            if (is_object($source->smarty->security_policy)) {
                $source->smarty->security_policy->isTrustedResourceDir($source->filepath);
            }

            $source->uid = sha1($source->filepath);
            if ($source->smarty->compile_check && !isset($source->timestamp)) {
                $source->timestamp = @filemtime($source->filepath);
                $source->exists = !!$source->timestamp;
            }
        }
        
        $source->recompiled = true;
    }

    
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->timestamp = @filemtime($source->filepath);
        $source->exists = !!$source->timestamp;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        if ($source->timestamp) {
            return file_get_contents($source->filepath);
        }
        if ($source instanceof Smarty_Config_Source) {
            throw new SmartyException("Unable to read config {$source->type} '{$source->name}'");
        }
        throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
    }

    
    public function getBasename(Smarty_Template_Source $source)
    {
        $_file = $source->name;
        if (($_pos = strpos($_file, ']')) !== false) {
            $_file = substr($_file, $_pos + 1);
        }

        return basename($_file);
    }

}
