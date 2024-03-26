<?php




namespace LightnCandy;


class Parser extends Token
{
    
    const BLOCKPARAM = 9999;
    const PARTIALBLOCK = 9998;
    const LITERAL = -1;
    const SUBEXP = -2;

    
    public static function getPartialBlock(&$vars)
    {
        if (isset($vars[static::PARTIALBLOCK])) {
            $id = $vars[static::PARTIALBLOCK];
            unset($vars[static::PARTIALBLOCK]);
            return $id;
        }
        return 0;
    }

    
    public static function getBlockParams(&$vars)
    {
        if (isset($vars[static::BLOCKPARAM])) {
            $list = $vars[static::BLOCKPARAM];
            unset($vars[static::BLOCKPARAM]);
            return $list;
        }
    }

    
    protected static function getLiteral($name, $asis, $quote = false)
    {
        return $asis ? array($name) : array(static::LITERAL, $quote ? "'$name'" : $name);
    }

    
    protected static function getExpression($v, &$context, $pos)
    {
        $asis = ($pos === 0);

        
        if (is_numeric($v)) {
            return static::getLiteral(strval(1 * $v), $asis);
        }

        
        if (preg_match('/^"(.*)"$/', $v, $matched)) {
            return static::getLiteral(preg_replace('/([^\\\\])\\\\\\\\"/', '$1"', preg_replace('/^\\\\\\\\"/', '"', $matched[1])), $asis, true);
        }

        
        if (preg_match('/^\\\\\'(.*)\\\\\'$/', $v, $matched)) {
            return static::getLiteral($matched[1], $asis, true);
        }

        
        if (preg_match('/^(true|false|null|undefined)$/', $v)) {
            return static::getLiteral($v, $asis);
        }

        $ret = array();
        $levels = 0;

        
        if ($v === '..') {
            $v = '../';
        }

        
        $v = preg_replace_callback('/\\.\\.\\
            $levels++;
            return '';
        }, trim($v));

        
        $v = preg_replace('/\\.\\

        $strp = (($pos !== 0) && $context['flags']['strpar']);
        if ($levels && !$strp) {
            $ret[] = $levels;
            if (!$context['flags']['parent']) {
                $context['error'][] = 'Do not support {{../var}}, you should do compile with LightnCandy::FLAG_PARENT flag';
            }
            $context['usedFeature']['parent'] ++;
        }

        if ($context['flags']['advar'] && preg_match('/\\]/', $v)) {
            preg_match_all(static::VARNAME_SEARCH, $v, $matchedall);
        } else {
            preg_match_all('/([^\\.\\/]+)/', $v, $matchedall);
        }

        if ($v !== '.') {
            $vv = implode('.', $matchedall[1]);
            if (strlen($v) !== strlen($vv)) {
                $context['error'][] = "Unexpected charactor in '$v' ! (should it be '$vv' ?)";
            }
        }

        foreach ($matchedall[1] as $m) {
            if ($context['flags']['advar'] && substr($m, 0, 1) === '[') {
                $ret[] = substr($m, 1, -1);
            } elseif ((!$context['flags']['this'] || ($m !== 'this')) && ($m !== '.')) {
                $ret[] = $m;
            } else {
                $scoped++;
            }
        }

        if ($strp) {
            return array(static::LITERAL, "'" . implode('.', $ret) . "'");
        }

        if (($scoped > 0) && ($levels === 0) && (count($ret) > 0)) {
            array_unshift($ret, 0);
        }

        return $ret;
    }

    
    public static function parse(&$token, &$context)
    {
        $vars = static::analyze($token[static::POS_INNERTAG], $context);
        if ($token[static::POS_OP] === '>') {
            $fn = static::getPartialName($vars);
        } elseif ($token[static::POS_OP] === '#*') {
            $fn = static::getPartialName($vars, 1);
        }

        $avars = static::advancedVariable($vars, $context, static::toString($token));

        if (isset($fn) && ($fn !== null)) {
            if ($token[static::POS_OP] === '>') {
                $avars[0] = $fn;
            } elseif ($token[static::POS_OP] === '#*') {
                $avars[1] = $fn;
            }
        }

        return array(($token[static::POS_BEGINRAW] === '{') || ($token[static::POS_OP] === '&') || $context['flags']['noesc'] || $context['rawblock'], $avars);
    }

    
    public static function getPartialName(&$vars, $pos = 0)
    {
        if (!isset($vars[$pos])) {
            return;
        }
        return preg_match(SafeString::IS_SUBEXP_SEARCH, $vars[$pos]) ? null : array(preg_replace('/^("(.+)")|(\\[(.+)\\])|(\\\\\'(.+)\\\\\')$/', '$2$4$6', $vars[$pos]));
    }

    
    public static function subexpression($expression, &$context)
    {
        $context['usedFeature']['subexp']++;
        $vars = static::analyze(substr($expression, 1, -1), $context);
        $avars = static::advancedVariable($vars, $context, $expression);
        if (isset($avars[0][0]) && !$context['flags']['exhlp']) {
            if (!Validator::helper($context, $avars, true)) {
                $context['error'][] = "Can not find custom helper function defination {$avars[0][0]}() !";
            }
        }
        return array(static::SUBEXP, $avars, $expression);
    }

    
    public static function isSubExp($var)
    {
        return is_array($var) && (count($var) === 3) && ($var[0] === static::SUBEXP) && is_string($var[2]);
    }

    
    protected static function advancedVariable($vars, &$context, $token)
    {
        $ret = array();
        $i = 0;
        foreach ($vars as $idx => $var) {
            
            if (preg_match(SafeString::IS_SUBEXP_SEARCH, $var)) {
                $ret[$i] = static::subexpression($var, $context);
                $i++;
                continue;
            }

            
            if (preg_match(SafeString::IS_BLOCKPARAM_SEARCH, $var, $matched)) {
                $ret[static::BLOCKPARAM] = explode(' ', $matched[1]);
                continue;
            }

            if ($context['flags']['namev']) {
                if (preg_match('/^((\\[([^\\]]+)\\])|([^=^["\']+))=(.+)$/', $var, $m)) {
                    if (!$context['flags']['advar'] && $m[3]) {
                        $context['error'][] = "Wrong argument name as '[$m[3]]' in $token ! You should fix your template or compile with LightnCandy::FLAG_ADVARNAME flag.";
                    }
                    $idx = $m[3] ? $m[3] : $m[4];
                    $var = $m[5];
                    
                    if (preg_match(SafeString::IS_SUBEXP_SEARCH, $var)) {
                        $ret[$idx] = static::subexpression($var, $context);
                        continue;
                    }
                }
            }

            if ($context['flags']['advar'] && !preg_match("/^(\"|\\\\')(.*)(\"|\\\\')$/", $var)) {
                
                if (preg_match('/^[^\\[\\.]+[\\]\\[]/', $var)
                    
                    || preg_match('/[\\[\\]][^\\]\\.]+$/', $var)
                    
                    || preg_match('/\\][^\\]\\[\\.]+\\./', $var)
                    
                    || preg_match('/\\.[^\\]\\[\\.]+\\[/', preg_replace('/^(..\\/)+/', '', preg_replace('/\\[[^\\]]+\\]/', '[XXX]', $var)))
                ) {
                    $context['error'][] = "Wrong variable naming as '$var' in $token !";
                } else {
                    $name = preg_replace('/(\\[.+?\\])/', '', $var);
                    
                    
                    if (preg_match('/[!"#%\'*+,;<=>{|}~]/', $name)) {
                        if (!$context['flags']['namev'] && preg_match('/.+=.+/', $name)) {
                            $context['error'][] = "Wrong variable naming as '$var' in $token ! If you try to use foo=bar param, you should enable LightnCandy::FLAG_NAMEDARG !";
                        } else {
                            $context['error'][] = "Wrong variable naming as '$var' in $token ! You should wrap ! \" # % & ' * + , ; < = > { | } ~ into [ ]";
                        }
                    }
                }
            }

            $var = static::getExpression($var, $context, $idx);

            if (is_string($idx)) {
                $ret[$idx] = $var;
            } else {
                $ret[$i] = $var;
                $i++;
            }
        }
        return $ret;
    }

    
    protected static function detectQuote($string)
    {
        
        if (preg_match('/^\([^\)]*$/', $string)) {
            return array(')', 1);
        }

        
        if (preg_match('/^"[^"]*$/', $string)) {
            return array('"', 0);
        }

        
        if (preg_match('/^\\\\\'[^\']*$/', $string)) {
            return array('\'', 0);
        }

        
        if (preg_match('/^[^"]*="[^"]*$/', $string)) {
            return array('"', 0);
        }

        
        if (preg_match('/^([^"\'].+)?\\[[^\\]]*$/', $string)) {
            return array(']', 0);
        }

        
        if (preg_match('/^[^\']*=\\\\\'[^\']*$/', $string)) {
            return array('\'', 0);
        }

        
        if (preg_match('/.+(\(+)[^\)]*$/', $string, $m)) {
            return array(')', strlen($m[1]));
        }
    }

    
    protected static function analyze($token, &$context)
    {
        $count = preg_match_all('/(\s*)([^\s]+)/', $token, $matchedall);
        
        if (($count > 0) && $context['flags']['advar']) {
            $vars = array();
            $prev = '';
            $expect = 0;
            $quote = 0;
            $stack = 0;

            foreach ($matchedall[2] as $index => $t) {
                $detected = static::detectQuote($t);

                if ($expect === ')') {
                    if ($detected && ($detected[0] !== ')')) {
                        $quote = $detected[0];
                    }
                    if (substr($t, -1, 1) === $quote) {
                        $quote = 0;
                    }
                }

                
                if ($expect) {
                    $prev .= "{$matchedall[1][$index]}$t";
                    if (($quote === 0) && ($stack > 0) && preg_match('/(.+=)*(\\(+)/', $t, $m)) {
                        $stack += strlen($m[2]);
                    }
                    
                    if (substr($t, -1, 1) === $expect) {
                        if ($stack > 0) {
                            preg_match('/(\\)+)$/', $t, $matchedq);
                            $stack -= isset($matchedq[0]) ? strlen($matchedq[0]) : 1;
                            if ($stack > 0) {
                                continue;
                            }
                            if ($stack < 0) {
                                $context['error'][] = "Unexcepted ')' in expression '$token' !!";
                                $expect = 0;
                                break;
                            }
                        }
                        $vars[] = $prev;
                        $prev = '';
                        $expect = 0;
                        continue;
                    } elseif (($expect == ']') && (strpos($t, $expect) !== false)) {
                        $t = $prev;
                        $detected = static::detectQuote($t);
                        $expect = 0;
                    } else {
                        continue;
                    }
                }


                if ($detected) {
                    $prev = $t;
                    $expect = $detected[0];
                    $stack = $detected[1];
                    continue;
                }

                
                if (($t === 'as') && (count($vars) > 0)) {
                    $prev = '';
                    $expect = '|';
                    $stack=1;
                    continue;
                }

                $vars[] = $t;
            }

            if ($expect) {
                $context['error'][] = "Error in '$token': expect '$expect' but the token ended!!";
            }

            return $vars;
        }
        return ($count > 0) ? $matchedall[2] : explode(' ', $token);
    }
}
