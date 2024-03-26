<?php



class Smarty_Internal_Resource_Extends extends Smarty_Resource
{
    
    public $mbstring_overload = 0;

    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $uid = '';
        $sources = array();
        $components = explode('|', $source->name);
        $exists = true;
        foreach ($components as $component) {
            $s = Smarty_Resource::source(null, $source->smarty, $component);
            if ($s->type == 'php') {
                throw new SmartyException("Resource type {$s->type} cannot be used with the extends resource type");
            }
            $sources[$s->uid] = $s;
            $uid .= $s->filepath;
            if ($_template && $_template->smarty->compile_check) {
                $exists = $exists && $s->exists;
            }
        }
        $source->components = $sources;
        $source->filepath = $s->filepath;
        $source->uid = sha1($uid);
        if ($_template && $_template->smarty->compile_check) {
            $source->timestamp = $s->timestamp;
            $source->exists = $exists;
        }
        
        $source->template = $_template;
    }

    
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->exists = true;
        foreach ($source->components as $s) {
            $source->exists = $source->exists && $s->exists;
        }
        $source->timestamp = $s->timestamp;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        if (!$source->exists) {
            throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
        }

        $_components = array_reverse($source->components);

        $_content = '';
        foreach ($_components as $_component) {
            
            $_content .= $_component->content;
        }
        return $_content;
    }

    
    public function getBasename(Smarty_Template_Source $source)
    {
        return str_replace(':', '.', basename($source->filepath));
    }

}
