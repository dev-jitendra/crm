<?php


class HTMLPurifier_HTMLModule_SafeObject extends HTMLPurifier_HTMLModule
{
    
    public $name = 'SafeObject';

    
    public function setup($config)
    {
        
        

        $max = $config->get('HTML.MaxImgLength');
        $object = $this->addElement(
            'object',
            'Inline',
            'Optional: param | Flow | #PCDATA',
            'Common',
            array(
                
                
                'type' => 'Enum#application/x-shockwave-flash',
                'width' => 'Pixels#' . $max,
                'height' => 'Pixels#' . $max,
                'data' => 'URI#embedded',
                'codebase' => new HTMLPurifier_AttrDef_Enum(
                    array(
                        'http:
                    )
                ),
            )
        );
        $object->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeObject();

        $param = $this->addElement(
            'param',
            false,
            'Empty',
            false,
            array(
                'id' => 'ID',
                'name*' => 'Text',
                'value' => 'Text'
            )
        );
        $param->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeParam();
        $this->info_injector[] = 'SafeObject';
    }
}


