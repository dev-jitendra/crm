<?php



abstract class Smarty_Resource_Custom extends Smarty_Resource
{
    
    abstract protected function fetch($name, &$source, &$mtime);

    
    protected function fetchTimestamp($name)
    {
        return null;
    }

    
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $source->filepath = strtolower($source->type . ':' . $source->name);
        $source->uid = sha1($source->type . ':' . $source->name);

        $mtime = $this->fetchTimestamp($source->name);
        if ($mtime !== null) {
            $source->timestamp = $mtime;
        } else {
            $this->fetch($source->name, $content, $timestamp);
            $source->timestamp = isset($timestamp) ? $timestamp : false;
            if( isset($content) )
                $source->content = $content;
        }
        $source->exists = !!$source->timestamp;
    }

    
    public function getContent(Smarty_Template_Source $source)
    {
        $this->fetch($source->name, $content, $timestamp);
        if (isset($content)) {
            return $content;
        }

        throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
    }

    
    protected function getBasename(Smarty_Template_Source $source)
    {
        return basename($source->name);
    }

}
