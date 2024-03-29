<?php



include 'smarty_internal_parsetree.php';


class Smarty_Internal_SmartyTemplateCompiler extends Smarty_Internal_TemplateCompilerBase
{
    
    public $lexer_class;

    
    public $parser_class;

    
    public $lex;

    
    public $parser;

    
    public $smarty;

    
    public $local_var = array();

    
    public function __construct($lexer_class, $parser_class, $smarty)
    {
        $this->smarty = $smarty;
        parent::__construct();
        
        $this->lexer_class = $lexer_class;
        $this->parser_class = $parser_class;
    }

    
    protected function doCompile($_content)
    {
        
        
        $this->lex = new $this->lexer_class($_content, $this);
        $this->parser = new $this->parser_class($this->lex, $this);
        if ($this->inheritance_child) {
            
            $this->lex->yypushstate(Smarty_Internal_Templatelexer::CHILDBODY);
        }
        if ($this->smarty->_parserdebug) {
            $this->parser->PrintTrace();
            $this->lex->PrintTrace();
        }
        
        while ($this->lex->yylex() && !$this->abort_and_recompile) {
            if ($this->smarty->_parserdebug) {
                echo "<pre>Line {$this->lex->line} Parsing  {$this->parser->yyTokenName[$this->lex->token]} Token " .
                    htmlentities($this->lex->value) . "</pre>";
            }
            $this->parser->doParse($this->lex->token, $this->lex->value);
        }

        if ($this->abort_and_recompile) {
            
            return false;
        }
        
        $this->parser->doParse(0, 0);
        
        if (count($this->_tag_stack) > 0) {
            
            list($openTag, $_data) = array_pop($this->_tag_stack);
            $this->trigger_template_error("unclosed {$this->smarty->left_delimiter}" . $openTag . "{$this->smarty->right_delimiter} tag");
        }
        
        
        return $this->parser->retvalue;
    }

}
