<?php



class Smarty_Internal_Config_File_Compiler
{
    
    public $lex;

    
    public $parser;

    
    public $smarty;

    
    public $config;

    
    public $config_data = array();

    
    public function __construct($smarty)
    {
        $this->smarty = $smarty;
        $this->config_data['sections'] = array();
        $this->config_data['vars'] = array();
    }

    
    public function compileSource(Smarty_Internal_Config $config)
    {
        
        $this->config = $config;
        
        $_content = $config->source->content . "\n";
        
        if ($_content == '') {
            return true;
        }
        
        $lex = new Smarty_Internal_Configfilelexer($_content, $this->smarty);
        $parser = new Smarty_Internal_Configfileparser($lex, $this);
        if ($this->smarty->_parserdebug) $parser->PrintTrace();
        
        while ($lex->yylex()) {
            if ($this->smarty->_parserdebug) echo "<br>Parsing  {$parser->yyTokenName[$lex->token]} Token {$lex->value} Line {$lex->line} \n";
            $parser->doParse($lex->token, $lex->value);
        }
        
        $parser->doParse(0, 0);
        $config->compiled_config = '<?php $_config_vars = ' . var_export($this->config_data, true) . '; ?>';
    }

    
    public function trigger_config_file_error($args = null)
    {
        $this->lex = Smarty_Internal_Configfilelexer::instance();
        $this->parser = Smarty_Internal_Configfileparser::instance();
        
        $line = $this->lex->line;
        if (isset($args)) {
            
        }
        $match = preg_split("/\n/", $this->lex->data);
        $error_text = "Syntax error in config file '{$this->config->source->filepath}' on line {$line} '{$match[$line-1]}' ";
        if (isset($args)) {
            
            $error_text .= $args;
        } else {
            
            foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
                $exp_token = $this->parser->yyTokenName[$token];
                if (isset($this->lex->smarty_token_names[$exp_token])) {
                    
                    $expect[] = '"' . $this->lex->smarty_token_names[$exp_token] . '"';
                } else {
                    
                    $expect[] = $this->parser->yyTokenName[$token];
                }
            }
            
            $error_text .= ' - Unexpected "' . $this->lex->value . '", expected one of: ' . implode(' , ', $expect);
        }
        throw new SmartyCompilerException($error_text);
    }

}
