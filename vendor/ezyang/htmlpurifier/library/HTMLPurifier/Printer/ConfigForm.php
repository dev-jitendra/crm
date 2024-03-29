<?php


class HTMLPurifier_Printer_ConfigForm extends HTMLPurifier_Printer
{

    
    protected $fields = array();

    
    protected $docURL;

    
    protected $name;

    
    protected $compress = false;

    
    public function __construct(
        $name,
        $doc_url = null,
        $compress = false
    ) {
        parent::__construct();
        $this->docURL = $doc_url;
        $this->name = $name;
        $this->compress = $compress;
        
        $this->fields[0] = new HTMLPurifier_Printer_ConfigForm_default();
        $this->fields[HTMLPurifier_VarParser::C_BOOL] = new HTMLPurifier_Printer_ConfigForm_bool();
    }

    
    public function setTextareaDimensions($cols = null, $rows = null)
    {
        if ($cols) {
            $this->fields['default']->cols = $cols;
        }
        if ($rows) {
            $this->fields['default']->rows = $rows;
        }
    }

    
    public static function getCSS()
    {
        return file_get_contents(HTMLPURIFIER_PREFIX . '/HTMLPurifier/Printer/ConfigForm.css');
    }

    
    public static function getJavaScript()
    {
        return file_get_contents(HTMLPURIFIER_PREFIX . '/HTMLPurifier/Printer/ConfigForm.js');
    }

    
    public function render($config, $allowed = true, $render_controls = true)
    {
        if (is_array($config) && isset($config[0])) {
            $gen_config = $config[0];
            $config = $config[1];
        } else {
            $gen_config = $config;
        }

        $this->config = $config;
        $this->genConfig = $gen_config;
        $this->prepareGenerator($gen_config);

        $allowed = HTMLPurifier_Config::getAllowedDirectivesForForm($allowed, $config->def);
        $all = array();
        foreach ($allowed as $key) {
            list($ns, $directive) = $key;
            $all[$ns][$directive] = $config->get($ns . '.' . $directive);
        }

        $ret = '';
        $ret .= $this->start('table', array('class' => 'hp-config'));
        $ret .= $this->start('thead');
        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Directive', array('class' => 'hp-directive'));
        $ret .= $this->element('th', 'Value', array('class' => 'hp-value'));
        $ret .= $this->end('tr');
        $ret .= $this->end('thead');
        foreach ($all as $ns => $directives) {
            $ret .= $this->renderNamespace($ns, $directives);
        }
        if ($render_controls) {
            $ret .= $this->start('tbody');
            $ret .= $this->start('tr');
            $ret .= $this->start('td', array('colspan' => 2, 'class' => 'controls'));
            $ret .= $this->elementEmpty('input', array('type' => 'submit', 'value' => 'Submit'));
            $ret .= '[<a href="?">Reset</a>]';
            $ret .= $this->end('td');
            $ret .= $this->end('tr');
            $ret .= $this->end('tbody');
        }
        $ret .= $this->end('table');
        return $ret;
    }

    
    protected function renderNamespace($ns, $directives)
    {
        $ret = '';
        $ret .= $this->start('tbody', array('class' => 'namespace'));
        $ret .= $this->start('tr');
        $ret .= $this->element('th', $ns, array('colspan' => 2));
        $ret .= $this->end('tr');
        $ret .= $this->end('tbody');
        $ret .= $this->start('tbody');
        foreach ($directives as $directive => $value) {
            $ret .= $this->start('tr');
            $ret .= $this->start('th');
            if ($this->docURL) {
                $url = str_replace('%s', urlencode("$ns.$directive"), $this->docURL);
                $ret .= $this->start('a', array('href' => $url));
            }
            $attr = array('for' => "{$this->name}:$ns.$directive");

            
            if (!$this->compress || (strlen($directive) < $this->compress)) {
                $directive_disp = $directive;
            } else {
                $directive_disp = substr($directive, 0, $this->compress - 2) . '...';
                $attr['title'] = $directive;
            }

            $ret .= $this->element(
                'label',
                $directive_disp,
                
                $attr
            );
            if ($this->docURL) {
                $ret .= $this->end('a');
            }
            $ret .= $this->end('th');

            $ret .= $this->start('td');
            $def = $this->config->def->info["$ns.$directive"];
            if (is_int($def)) {
                $allow_null = $def < 0;
                $type = abs($def);
            } else {
                $type = $def->type;
                $allow_null = isset($def->allow_null);
            }
            if (!isset($this->fields[$type])) {
                $type = 0;
            } 
            $type_obj = $this->fields[$type];
            if ($allow_null) {
                $type_obj = new HTMLPurifier_Printer_ConfigForm_NullDecorator($type_obj);
            }
            $ret .= $type_obj->render($ns, $directive, $value, $this->name, array($this->genConfig, $this->config));
            $ret .= $this->end('td');
            $ret .= $this->end('tr');
        }
        $ret .= $this->end('tbody');
        return $ret;
    }

}


class HTMLPurifier_Printer_ConfigForm_NullDecorator extends HTMLPurifier_Printer
{
    
    protected $obj;

    
    public function __construct($obj)
    {
        parent::__construct();
        $this->obj = $obj;
    }

    
    public function render($ns, $directive, $value, $name, $config)
    {
        if (is_array($config) && isset($config[0])) {
            $gen_config = $config[0];
            $config = $config[1];
        } else {
            $gen_config = $config;
        }
        $this->prepareGenerator($gen_config);

        $ret = '';
        $ret .= $this->start('label', array('for' => "$name:Null_$ns.$directive"));
        $ret .= $this->element('span', "$ns.$directive:", array('class' => 'verbose'));
        $ret .= $this->text(' Null/Disabled');
        $ret .= $this->end('label');
        $attr = array(
            'type' => 'checkbox',
            'value' => '1',
            'class' => 'null-toggle',
            'name' => "$name" . "[Null_$ns.$directive]",
            'id' => "$name:Null_$ns.$directive",
            'onclick' => "toggleWriteability('$name:$ns.$directive',checked)" 
        );
        if ($this->obj instanceof HTMLPurifier_Printer_ConfigForm_bool) {
            
            $attr['onclick'] =
                "toggleWriteability('$name:Yes_$ns.$directive',checked);" .
                "toggleWriteability('$name:No_$ns.$directive',checked)";
        }
        if ($value === null) {
            $attr['checked'] = 'checked';
        }
        $ret .= $this->elementEmpty('input', $attr);
        $ret .= $this->text(' or ');
        $ret .= $this->elementEmpty('br');
        $ret .= $this->obj->render($ns, $directive, $value, $name, array($gen_config, $config));
        return $ret;
    }
}


class HTMLPurifier_Printer_ConfigForm_default extends HTMLPurifier_Printer
{
    
    public $cols = 18;

    
    public $rows = 5;

    
    public function render($ns, $directive, $value, $name, $config)
    {
        if (is_array($config) && isset($config[0])) {
            $gen_config = $config[0];
            $config = $config[1];
        } else {
            $gen_config = $config;
        }
        $this->prepareGenerator($gen_config);
        
        $ret = '';
        $def = $config->def->info["$ns.$directive"];
        if (is_int($def)) {
            $type = abs($def);
        } else {
            $type = $def->type;
        }
        if (is_array($value)) {
            switch ($type) {
                case HTMLPurifier_VarParser::LOOKUP:
                    $array = $value;
                    $value = array();
                    foreach ($array as $val => $b) {
                        $value[] = $val;
                    }
                    
                case HTMLPurifier_VarParser::ALIST:
                    $value = implode(PHP_EOL, $value);
                    break;
                case HTMLPurifier_VarParser::HASH:
                    $nvalue = '';
                    foreach ($value as $i => $v) {
                        if (is_array($v)) {
                            
                            $v = implode(";", $v);
                        }
                        $nvalue .= "$i:$v" . PHP_EOL;
                    }
                    $value = $nvalue;
                    break;
                default:
                    $value = '';
            }
        }
        if ($type === HTMLPurifier_VarParser::C_MIXED) {
            return 'Not supported';
            $value = serialize($value);
        }
        $attr = array(
            'name' => "$name" . "[$ns.$directive]",
            'id' => "$name:$ns.$directive"
        );
        if ($value === null) {
            $attr['disabled'] = 'disabled';
        }
        if (isset($def->allowed)) {
            $ret .= $this->start('select', $attr);
            foreach ($def->allowed as $val => $b) {
                $attr = array();
                if ($value == $val) {
                    $attr['selected'] = 'selected';
                }
                $ret .= $this->element('option', $val, $attr);
            }
            $ret .= $this->end('select');
        } elseif ($type === HTMLPurifier_VarParser::TEXT ||
                $type === HTMLPurifier_VarParser::ITEXT ||
                $type === HTMLPurifier_VarParser::ALIST ||
                $type === HTMLPurifier_VarParser::HASH ||
                $type === HTMLPurifier_VarParser::LOOKUP) {
            $attr['cols'] = $this->cols;
            $attr['rows'] = $this->rows;
            $ret .= $this->start('textarea', $attr);
            $ret .= $this->text($value);
            $ret .= $this->end('textarea');
        } else {
            $attr['value'] = $value;
            $attr['type'] = 'text';
            $ret .= $this->elementEmpty('input', $attr);
        }
        return $ret;
    }
}


class HTMLPurifier_Printer_ConfigForm_bool extends HTMLPurifier_Printer
{
    
    public function render($ns, $directive, $value, $name, $config)
    {
        if (is_array($config) && isset($config[0])) {
            $gen_config = $config[0];
            $config = $config[1];
        } else {
            $gen_config = $config;
        }
        $this->prepareGenerator($gen_config);
        $ret = '';
        $ret .= $this->start('div', array('id' => "$name:$ns.$directive"));

        $ret .= $this->start('label', array('for' => "$name:Yes_$ns.$directive"));
        $ret .= $this->element('span', "$ns.$directive:", array('class' => 'verbose'));
        $ret .= $this->text(' Yes');
        $ret .= $this->end('label');

        $attr = array(
            'type' => 'radio',
            'name' => "$name" . "[$ns.$directive]",
            'id' => "$name:Yes_$ns.$directive",
            'value' => '1'
        );
        if ($value === true) {
            $attr['checked'] = 'checked';
        }
        if ($value === null) {
            $attr['disabled'] = 'disabled';
        }
        $ret .= $this->elementEmpty('input', $attr);

        $ret .= $this->start('label', array('for' => "$name:No_$ns.$directive"));
        $ret .= $this->element('span', "$ns.$directive:", array('class' => 'verbose'));
        $ret .= $this->text(' No');
        $ret .= $this->end('label');

        $attr = array(
            'type' => 'radio',
            'name' => "$name" . "[$ns.$directive]",
            'id' => "$name:No_$ns.$directive",
            'value' => '0'
        );
        if ($value === false) {
            $attr['checked'] = 'checked';
        }
        if ($value === null) {
            $attr['disabled'] = 'disabled';
        }
        $ret .= $this->elementEmpty('input', $attr);

        $ret .= $this->end('div');

        return $ret;
    }
}


