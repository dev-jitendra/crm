<?php



abstract class Smarty_Resource_Uncompiled extends Smarty_Resource
{
    
    abstract public function renderUncompiled(Smarty_Template_Source $source, Smarty_Internal_Template $_template);

    
    public function populateCompiledFilepath(Smarty_Template_Compiled $compiled, Smarty_Internal_Template $_template)
    {
        $compiled->filepath = false;
        $compiled->timestamp = false;
        $compiled->exists = false;
    }

}
