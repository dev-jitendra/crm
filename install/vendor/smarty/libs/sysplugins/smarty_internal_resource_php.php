<?php


class Smarty_Internal_Resource_PHP extends Smarty_Resource_Uncompiled
{
    
    protected $short_open_tag;

    
    public function __construct()
    {
        $this->short_open_tag = ini_get( 'short_open_tag' );
    }

    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $source->filepath = $this->buildFilepath($source, $_template);

        if ($source->filepath !== false) {
            if (is_object($source->smarty->security_policy)) {
                $source->smarty->security_policy->isTrustedResourceDir($source->filepath);
            }

            $source->uid = sha1($source->filepath);
            if ($source->smarty->compile_check) {
                $source->timestamp = @filemtime($source->filepath);
                $source->exists = !!$source->timestamp;
            }
        }
    }

    
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->timestamp = @filemtime($source->filepath);
        $source->exists = !!$source->timestamp;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        if ($source->timestamp) {
            return '';
        }
        throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
    }

    
    public function renderUncompiled(Smarty_Template_Source $source, Smarty_Internal_Template $_template)
    {
        $_smarty_template = $_template;

        if (!$source->smarty->allow_php_templates) {
            throw new SmartyException("PHP templates are disabled");
        }
        if (!$source->exists) {
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $parent_resource = " in '{$_template->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new SmartyException("Unable to load template {$source->type} '{$source->name}'{$parent_resource}");
        }

        
        extract($_template->getTemplateVars());

        
        ini_set( 'short_open_tag', '1' );
        include($source->filepath);
        ini_set( 'short_open_tag', $this->short_open_tag );
    }
}
