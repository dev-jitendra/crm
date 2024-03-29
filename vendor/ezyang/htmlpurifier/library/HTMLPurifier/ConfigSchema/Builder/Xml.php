<?php


class HTMLPurifier_ConfigSchema_Builder_Xml extends XMLWriter
{

    
    protected $interchange;

    
    private $namespace;

    
    protected function writeHTMLDiv($html)
    {
        $this->startElement('div');

        $purifier = HTMLPurifier::getInstance();
        $html = $purifier->purify($html);
        $this->writeAttribute('xmlns', 'http:
        $this->writeRaw($html);

        $this->endElement(); 
    }

    
    protected function export($var)
    {
        if ($var === array()) {
            return 'array()';
        }
        return var_export($var, true);
    }

    
    public function build($interchange)
    {
        
        $this->interchange = $interchange;

        $this->setIndent(true);
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('configdoc');
        $this->writeElement('title', $interchange->name);

        foreach ($interchange->directives as $directive) {
            $this->buildDirective($directive);
        }

        if ($this->namespace) {
            $this->endElement();
        } 

        $this->endElement(); 
        $this->flush();
    }

    
    public function buildDirective($directive)
    {
        
        
        
        if (!$this->namespace || $this->namespace !== $directive->id->getRootNamespace()) {
            if ($this->namespace) {
                $this->endElement();
            } 
            $this->namespace = $directive->id->getRootNamespace();
            $this->startElement('namespace');
            $this->writeAttribute('id', $this->namespace);
            $this->writeElement('name', $this->namespace);
        }

        $this->startElement('directive');
        $this->writeAttribute('id', $directive->id->toString());

        $this->writeElement('name', $directive->id->getDirective());

        $this->startElement('aliases');
        foreach ($directive->aliases as $alias) {
            $this->writeElement('alias', $alias->toString());
        }
        $this->endElement(); 

        $this->startElement('constraints');
        if ($directive->version) {
            $this->writeElement('version', $directive->version);
        }
        $this->startElement('type');
        if ($directive->typeAllowsNull) {
            $this->writeAttribute('allow-null', 'yes');
        }
        $this->text($directive->type);
        $this->endElement(); 
        if ($directive->allowed) {
            $this->startElement('allowed');
            foreach ($directive->allowed as $value => $x) {
                $this->writeElement('value', $value);
            }
            $this->endElement(); 
        }
        $this->writeElement('default', $this->export($directive->default));
        $this->writeAttribute('xml:space', 'preserve');
        if ($directive->external) {
            $this->startElement('external');
            foreach ($directive->external as $project) {
                $this->writeElement('project', $project);
            }
            $this->endElement();
        }
        $this->endElement(); 

        if ($directive->deprecatedVersion) {
            $this->startElement('deprecated');
            $this->writeElement('version', $directive->deprecatedVersion);
            $this->writeElement('use', $directive->deprecatedUse->toString());
            $this->endElement(); 
        }

        $this->startElement('description');
        $this->writeHTMLDiv($directive->description);
        $this->endElement(); 

        $this->endElement(); 
    }
}


