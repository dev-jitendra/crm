<?php


class HTMLPurifier_AttrTransform_Name extends HTMLPurifier_AttrTransform
{

    
    public function transform($attr, $config, $context)
    {
        
        if ($config->get('HTML.Attr.Name.UseCDATA')) {
            return $attr;
        }
        if (!isset($attr['name'])) {
            return $attr;
        }
        $id = $this->confiscateAttr($attr, 'name');
        if (isset($attr['id'])) {
            return $attr;
        }
        $attr['id'] = $id;
        return $attr;
    }
}


