<?php



class Smarty_Config_Source extends Smarty_Template_Source
{
    
    public function __construct(Smarty_Resource $handler, Smarty $smarty, $resource, $type, $name, $unique_resource)
    {
        $this->handler = $handler; 

        
        
        
        

        $this->smarty = $smarty;
        $this->resource = $resource;
        $this->type = $type;
        $this->name = $name;
        $this->unique_resource = $unique_resource;
    }

    
    public function __set($property_name, $value)
    {
        switch ($property_name) {
            case 'content':
            case 'timestamp':
            case 'exists':
                $this->$property_name = $value;
                break;

            default:
                throw new SmartyException("invalid config property '$property_name'.");
        }
    }

    
    public function __get($property_name)
    {
        switch ($property_name) {
            case 'timestamp':
            case 'exists':
                $this->handler->populateTimestamp($this);

                return $this->$property_name;

            case 'content':
                return $this->content = $this->handler->getContent($this);

            default:
                throw new SmartyException("config property '$property_name' does not exist.");
        }
    }

}
