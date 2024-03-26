<?php




namespace LightnCandy;


class StringObject
{
    protected $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return strval($this->string);
    }
}


class Runtime extends Encoder
{
    const DEBUG_ERROR_LOG = 1;
    const DEBUG_ERROR_EXCEPTION = 2;
    const DEBUG_TAGS = 4;
    const DEBUG_TAGS_ANSI = 12;
    const DEBUG_TAGS_HTML = 20;

    
    public static function debug($v, $f, $cx)
    {
        
        $P = func_get_args();
        $params = array();
        for ($i=2;$i<count($P);$i++) {
            $params[] = &$P[$i];
        }
        $r = call_user_func_array((isset($cx['funcs'][$f]) ? $cx['funcs'][$f] : "{$cx['runtime']}::$f"), $params);

        if ($cx['flags']['debug'] & static::DEBUG_TAGS) {
            $ansi = $cx['flags']['debug'] & (static::DEBUG_TAGS_ANSI - static::DEBUG_TAGS);
            $html = $cx['flags']['debug'] & (static::DEBUG_TAGS_HTML - static::DEBUG_TAGS);
            $cs = ($html ? (($r !== '') ? '<!!--OK((-->' : '<!--MISSED((-->') : '')
                  . ($ansi ? (($r !== '') ? "\033[0;32m" : "\033[0;31m") : '');
            $ce = ($html ? '<!--))-->' : '')
                  . ($ansi ? "\033[0m" : '');
            switch ($f) {
                case 'sec':
                case 'wi':
                    if ($r == '') {
                        if ($ansi) {
                            $r = "\033[0;33mSKIPPED\033[0m";
                        }
                        if ($html) {
                            $r = '<!--SKIPPED-->';
                        }
                    }
                    return "$cs{{#{$v}}}$ce{$r}$cs{{/{$v}}}$ce";
                default:
                    return "$cs{{{$v}}}$ce";
            }
        } else {
            return $r;
        }
    }

    
    public static function err($cx, $err)
    {
        if ($cx['flags']['debug'] & static::DEBUG_ERROR_LOG) {
            error_log($err);
            return;
        }
        if ($cx['flags']['debug'] & static::DEBUG_ERROR_EXCEPTION) {
            throw new \Exception($err);
        }
    }

    
    public static function miss($cx, $v)
    {
        static::err($cx, "Runtime: $v does not exist");
    }

    
    public static function lo($cx, $v)
    {
        error_log(var_export($v[0], true));
        return '';
    }

    
    public static function v($cx, $in, $base, $path, $args = null)
    {
        $count = count($cx['scopes']);
        $plen = count($path);
        while ($base) {
            $v = $base;
            foreach ($path as $i => $name) {
                if (is_array($v)) {
                    if (isset($v[$name])) {
                        $v = $v[$name];
                        continue;
                    }
                    if (($i === $plen - 1) && ($name === 'length')) {
                        return count($v);
                    }
                }
                if (is_object($v)) {
                    if ($cx['flags']['prop'] && !($v instanceof \Closure) && isset($v->$name)) {
                        $v = $v->$name;
                        continue;
                    }
                    if ($cx['flags']['method'] && is_callable(array($v, $name))) {
                        try {
                            $v = $v->$name();
                            continue;
                        } catch (\BadMethodCallException $e) {}
                    }
                    if ($v instanceof \ArrayAccess) {
                        if (isset($v[$name])) {
                            $v = $v[$name];
                            continue;
                        }
                    }
                }
                if ($cx['flags']['mustlok']) {
                    unset($v);
                    break;
                }
                return null;
            }
            if (isset($v)) {
                if ($v instanceof \Closure) {
                    if ($cx['flags']['mustlam'] || $cx['flags']['lambda']) {
                        if (!$cx['flags']['knohlp'] && !is_null($args)) {
                            $A = $args ? $args[0] : array();
                            $A[] = array('hash' => is_array( $args ) ? $args[1] : null, '_this' => $in);
                        } else {
                            $A = array($in);
                        }
                        $v = call_user_func_array($v, $A);
                    }
                }
                return $v;
            }
            $count--;
            switch ($count) {
                case -1:
                    $base = $cx['sp_vars']['root'];
                    break;
                case -2:
                    return null;
                default:
                    $base = $cx['scopes'][$count];
            }
        }
        if ($args) {
            static::err($cx, 'Can not find helper or lambda: "' . implode('.', $path) . '" !');
        }
    }

    
    public static function ifvar($cx, $v, $zero)
    {
        return ($v !== null) && ($v !== false) && ($zero || ($v !== 0) && ($v !== 0.0)) && ($v !== '') && (is_array($v) ? (count($v) > 0) : true);
    }

    
    public static function isec($cx, $v)
    {
        return ($v === null) || ($v === false) || (is_array($v) && (count($v) === 0));
    }

    
    public static function enc($cx, $var)
    {
        
        if ($var instanceof \LightnCandy\SafeString) {
            return (string)$var;
        }

        return htmlspecialchars(static::raw($cx, $var), ENT_QUOTES, 'UTF-8');
    }

    
    public static function encq($cx, $var)
    {
        
        if ($var instanceof \LightnCandy\SafeString) {
            return (string)$var;
        }

        return str_replace(array('=', '`', '&#039;'), array('&#x3D;', '&#x60;', '&#x27;'), htmlspecialchars(static::raw($cx, $var), ENT_QUOTES, 'UTF-8'));
    }

    
    public static function sec($cx, $v, $bp, $in, $each, $cb, $else = null)
    {
        $push = ($in !== $v) || $each;

        $isAry = is_array($v) || ($v instanceof \ArrayObject);
        $isTrav = $v instanceof \Traversable;
        $loop = $each;
        $keys = null;
        $last = null;
        $isObj = false;

        if ($isAry && $else !== null && count($v) === 0) {
            return $else($cx, $in);
        }

        
        if (!$loop && $isAry) {
            $keys = array_keys($v);
            $loop = (count(array_diff_key($v, array_keys($keys))) == 0);
            $isObj = !$loop;
        }

        if (($loop && $isAry) || $isTrav) {
            if ($each && !$isTrav) {
                
                if ($keys == null) {
                    $keys = array_keys($v);
                    $isObj = (count(array_diff_key($v, array_keys($keys))) > 0);
                }
            }
            $ret = array();
            if ($push) {
                $cx['scopes'][] = $in;
            }
            $i = 0;
            if ($cx['flags']['spvar']) {
                $old_spvar = $cx['sp_vars'];
                $cx['sp_vars'] = array_merge(array('root' => $old_spvar['root']), $old_spvar, array('_parent' => $old_spvar));
                if (!$isTrav) {
                    $last = count($keys) - 1;
                }
            }

            $isSparceArray = $isObj && (count(array_filter(array_keys($v), 'is_string')) == 0);
            foreach ($v as $index => $raw) {
                if ($cx['flags']['spvar']) {
                    $cx['sp_vars']['first'] = ($i === 0);
                    $cx['sp_vars']['last'] = ($i == $last);
                    $cx['sp_vars']['key'] = $index;
                    $cx['sp_vars']['index'] = $isSparceArray ? $index : $i;
                    $i++;
                }
                if (isset($bp[0])) {
                    $raw = static::m($cx, $raw, array($bp[0] => $raw));
                }
                if (isset($bp[1])) {
                    $raw = static::m($cx, $raw, array($bp[1] => $index));
                }
                $ret[] = $cb($cx, $raw);
            }
            if ($cx['flags']['spvar']) {
                if ($isObj) {
                    unset($cx['sp_vars']['key']);
                } else {
                    unset($cx['sp_vars']['last']);
                }
                unset($cx['sp_vars']['index']);
                unset($cx['sp_vars']['first']);
                $cx['sp_vars'] = $old_spvar;
            }
            if ($push) {
                array_pop($cx['scopes']);
            }
            return join('', $ret);
        }
        if ($each) {
            if ($else !== null) {
                $ret = $else($cx, $v);
                return $ret;
            }
            return '';
        }
        if ($isAry) {
            if ($push) {
                $cx['scopes'][] = $in;
            }
            $ret = $cb($cx, $v);
            if ($push) {
                array_pop($cx['scopes']);
            }
            return $ret;
        }

        if ($cx['flags']['mustsec']) {
            return $v ? $cb($cx, $in) : '';
        }

        if ($v === true) {
            return $cb($cx, $in);
        }

        if (($v !== null) && ($v !== false)) {
            return $cb($cx, $v);
        }

        if ($else !== null) {
            $ret = $else($cx, $in);
            return $ret;
        }

        return '';
    }

    
    public static function wi($cx, $v, $bp, $in, $cb, $else = null)
    {
        if (isset($bp[0])) {
            $v = static::m($cx, $v, array($bp[0] => $v));
        }
        if (($v === false) || ($v === null) || (is_array($v) && (count($v) === 0))) {
            return $else ? $else($cx, $in) : '';
        }
        if ($v === $in) {
            $ret = $cb($cx, $v);
        } else {
            $cx['scopes'][] = $in;
            $ret = $cb($cx, $v);
            array_pop($cx['scopes']);
        }
        return $ret;
    }

    
    public static function m($cx, $a, $b)
    {
        if (is_array($b)) {
            if ($a === null) {
                return $b;
            } elseif (is_array($a)) {
                return array_merge($a, $b);
            } elseif ($cx['flags']['method'] || $cx['flags']['prop']) {
                if (!is_object($a)) {
                    $a = new StringObject($a);
                }
                foreach ($b as $i => $v) {
                    $a->$i = $v;
                }
            }
        }
        return $a;
    }

    
    public static function p($cx, $p, $v, $pid, $sp = '')
    {
        $pp = ($p === '@partial-block') ? "$p" . ($pid > 0 ? $pid : $cx['partialid']) : $p;

        if (!isset($cx['partials'][$pp])) {
            static::err($cx, "Can not find partial named as '$p' !!");
            return '';
        }

        $cx['partialid'] = ($p === '@partial-block') ? (($pid > 0) ? $pid : (($cx['partialid'] > 0) ? $cx['partialid'] - 1 : 0)) : $pid;

        return call_user_func($cx['partials'][$pp], $cx, static::m($cx, $v[0][0], $v[1]), $sp);
    }

    
    public static function in(&$cx, $p, $code)
    {
        $cx['partials'][$p] = $code;
    }

    
    public static function hbch(&$cx, $ch, $vars, $op, &$_this)
    {
        if (isset($cx['blparam'][0][$ch])) {
            return $cx['blparam'][0][$ch];
        }

        $options = array(
            'name' => $ch,
            'hash' => $vars[1],
            'contexts' => count($cx['scopes']) ? $cx['scopes'] : array(null),
            'fn.blockParams' => 0,
            '_this' => &$_this
        );

        if ($cx['flags']['spvar']) {
            $options['data'] = &$cx['sp_vars'];
        }

        return static::exch($cx, $ch, $vars, $options);
    }

    
    public static function hbbch(&$cx, $ch, $vars, &$_this, $inverted, $cb, $else = null)
    {
        $options = array(
            'name' => $ch,
            'hash' => $vars[1],
            'contexts' => count($cx['scopes']) ? $cx['scopes'] : array(null),
            'fn.blockParams' => 0,
            '_this' => &$_this,
        );

        if ($cx['flags']['spvar']) {
            $options['data'] = &$cx['sp_vars'];
        }

        if (isset($vars[2])) {
            $options['fn.blockParams'] = count($vars[2]);
        }

        
        if ($inverted) {
            $tmp = $else;
            $else = $cb;
            $cb = $tmp;
        }

        $options['fn'] = function ($context = '_NO_INPUT_HERE_', $data = null) use ($cx, &$_this, $cb, $options, $vars) {
            if ($cx['flags']['echo']) {
                ob_start();
            }
            if (isset($data['data'])) {
                $old_spvar = $cx['sp_vars'];
                $cx['sp_vars'] = array_merge(array('root' => $old_spvar['root']), $data['data'], array('_parent' => $old_spvar));
            }
            $ex = false;
            if (isset($data['blockParams']) && isset($vars[2])) {
                $ex = array_combine($vars[2], array_slice($data['blockParams'], 0, count($vars[2])));
                array_unshift($cx['blparam'], $ex);
            } elseif (isset($cx['blparam'][0])) {
                $ex = $cx['blparam'][0];
            }
            if (($context === '_NO_INPUT_HERE_') || ($context === $_this)) {
                $ret = $cb($cx, is_array($ex) ? static::m($cx, $_this, $ex) : $_this);
            } else {
                $cx['scopes'][] = $_this;
                $ret = $cb($cx, is_array($ex) ? static::m($cx, $context, $ex) : $context);
                array_pop($cx['scopes']);
            }
            if (isset($data['data'])) {
                $cx['sp_vars'] = $old_spvar;
            }
            return $cx['flags']['echo'] ? ob_get_clean() : $ret;
        };

        if ($else) {
            $options['inverse'] = function ($context = '_NO_INPUT_HERE_') use ($cx, $_this, $else) {
                if ($cx['flags']['echo']) {
                    ob_start();
                }
                if ($context === '_NO_INPUT_HERE_') {
                    $ret = $else($cx, $_this);
                } else {
                    $cx['scopes'][] = $_this;
                    $ret = $else($cx, $context);
                    array_pop($cx['scopes']);
                }
                return $cx['flags']['echo'] ? ob_get_clean() : $ret;
            };
        } else {
            $options['inverse'] = function () {
                return '';
            };
        }

        return static::exch($cx, $ch, $vars, $options);
    }

    
    public static function exch($cx, $ch, $vars, &$options)
    {
        $args = $vars[0];
        $args[] = &$options;
        $r = true;

        try {
            $r = call_user_func_array($cx['helpers'][$ch], $args);
        } catch (\Exception $E) {
            static::err($cx, "Runtime: call custom helper '$ch' error: " . $E->getMessage());
        }

        return $r;
    }
}
