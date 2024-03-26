<?php

namespace Masterminds\HTML5\Serializer;


class Traverser
{
    
    protected static $local_ns = array(
        'http:
        'http:
        'http:
    );

    protected $dom;

    protected $options;

    protected $encode = false;

    protected $rules;

    protected $out;

    
    public function __construct($dom, $out, RulesInterface $rules, $options = array())
    {
        $this->dom = $dom;
        $this->out = $out;
        $this->rules = $rules;
        $this->options = $options;

        $this->rules->setTraverser($this);
    }

    
    public function walk()
    {
        if ($this->dom instanceof \DOMDocument) {
            $this->rules->document($this->dom);
        } elseif ($this->dom instanceof \DOMDocumentFragment) {
            
            
            if ($this->dom->hasChildNodes()) {
                $this->children($this->dom->childNodes);
            }
        }        
        elseif ($this->dom instanceof \DOMNodeList) {
            
            $this->children($this->dom);
        }         
        else {
            $this->node($this->dom);
        }

        return $this->out;
    }

    
    public function node($node)
    {
        
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:
                $this->rules->element($node);
                break;
            case XML_TEXT_NODE:
                $this->rules->text($node);
                break;
            case XML_CDATA_SECTION_NODE:
                $this->rules->cdata($node);
                break;
            case XML_PI_NODE:
                $this->rules->processorInstruction($node);
                break;
            case XML_COMMENT_NODE:
                $this->rules->comment($node);
                break;
            
            default:
                
                break;
        }
    }

    
    public function children($nl)
    {
        foreach ($nl as $node) {
            $this->node($node);
        }
    }

    
    public function isLocalElement($ele)
    {
        $uri = $ele->namespaceURI;
        if (empty($uri)) {
            return false;
        }

        return isset(static::$local_ns[$uri]);
    }
}
