<?php


class Smarty_Resource_Extendsall extends Smarty_Internal_Resource_Extends
{
    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $uid = '';
        $sources = array();
        $exists = true;
        foreach ($_template->smarty->getTemplateDir() as $key => $directory) {
            try {
                $s = Smarty_Resource::source(null, $source->smarty, '[' . $key . ']' . $source->name );
                if (!$s->exists) {
                    continue;
                }
                $sources[$s->uid] = $s;
                $uid .= $s->filepath;
            } catch (SmartyException $e) {}
        }

        if (!$sources) {
            $source->exists = false;
            $source->template = $_template;

            return;
        }

        $sources = array_reverse($sources, true);
        reset($sources);
        $s = current($sources);

        $source->components = $sources;
        $source->filepath = $s->filepath;
        $source->uid = sha1($uid);
        $source->exists = $exists;
        if ($_template && $_template->smarty->compile_check) {
            $source->timestamp = $s->timestamp;
        }
        
        $source->template = $_template;
    }
}
