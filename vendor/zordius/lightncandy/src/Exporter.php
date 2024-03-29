<?php




namespace LightnCandy;


class Exporter
{
    
    protected static function closure($context, $closure)
    {
        if (is_string($closure) && preg_match('/(.+)::(.+)/', $closure, $matched)) {
            $ref = new \ReflectionMethod($matched[1], $matched[2]);
        } else {
            $ref = new \ReflectionFunction($closure);
        }
        $meta = static::getMeta($ref);

        return preg_replace('/^.*?function(\s+[^\s\\(]+?)?\s*\\((.+)\\}.*?\s*$/s', 'function($2}', static::replaceSafeString($context, $meta['code']));
    }

    
    public static function helpers($context)
    {
        $ret = '';
        foreach ($context['helpers'] as $name => $func) {
            if (!isset($context['usedCount']['helpers'][$name])) {
                continue;
            }
            if ((is_object($func) && ($func instanceof \Closure)) || ($context['flags']['exhlp'] == 0)) {
                $ret .= ("            '$name' => " . static::closure($context, $func) . ",\n");
                continue;
            }
            $ret .= "            '$name' => '$func',\n";
        }

        return "array($ret)";
    }

    
    protected static function replaceSafeString($context, $str)
    {
        return $context['flags']['standalone'] ? str_replace($context['safestring'], $context['safestringalias'], $str) : $str;
    }

    
    public static function getClassMethods($context, $class)
    {
        $methods = array();

        foreach ($class->getMethods() as $method) {
            $meta = static::getMeta($method);
            $methods[$meta['name']] = static::scanDependency($context, preg_replace('/public static function (.+)\\(/', "function {$context['funcprefix']}\$1(", $meta['code']), $meta['code']);
        }

        return $methods;
    }

    
    public static function getClassStatics($class)
    {
        $ret = '';

        foreach ($class->getStaticProperties() as $name => $value) {
            $ret .= " public static \${$name} = " . var_export($value, true) . ";\n";
        }

        return $ret;
    }





    
    public static function getMeta($refobj)
    {
        $fname = $refobj->getFileName();
        $lines = file_get_contents($fname);
        $file = new \SplFileObject($fname);

        $start = $refobj->getStartLine() - 2;
        $end = $refobj->getEndLine() - 1;

        if (version_compare(\PHP_VERSION, '8.0.0') >= 0) {
            $start++;
            $end++;
        }

        $file->seek($start);
        $spos = $file->ftell();
        $file->seek($end);
        $epos = $file->ftell();
        unset($file);

        return array(
            'name' => $refobj->getName(),
            'code' => substr($lines, $spos, $epos - $spos)
        );
    }

    
    public static function safestring($context)
    {
        $class = new \ReflectionClass($context['safestring']);

        return array_reduce(static::getClassMethods($context, $class), function ($in, $cur) {
            return $in . $cur[2];
        }, "if (!class_exists(\"" . addslashes($context['safestringalias']) . "\")) {\nclass {$context['safestringalias']} {\n" . static::getClassStatics($class)) . "}\n}\n";
    }

    
    public static function stringobject($context)
    {
        if ($context['flags']['standalone'] == 0) {
            return 'use \\LightnCandy\\StringObject as StringObject;';
        }
        $class = new \ReflectionClass('\\LightnCandy\\StringObject');
        $meta = static::getMeta($class);
        return "if (!class_exists(\"StringObject\")) {\n{$meta['code']}}\n";
    }

    
    public static function runtime($context)
    {
        $class = new \ReflectionClass($context['runtime']);
        $ret = '';
        $methods = static::getClassMethods($context, $class);

        $exports = array_keys($context['usedCount']['runtime']);

        while (true) {
            if (array_sum(array_map(function ($name) use (&$exports, $methods) {
                $n = 0;
                foreach ($methods[$name][1] as $child => $count) {
                    if (!in_array($child, $exports)) {
                        $exports[] = $child;
                        $n++;
                    }
                }
                return $n;
            }, $exports)) == 0) {
                break;
            }
        }

        foreach ($exports as $export) {
            $ret .= ($methods[$export][0] . "\n");
        }

        return $ret;
    }

    
    public static function constants($context)
    {
        if ($context['flags']['standalone'] == 0) {
            return 'array()';
        }

        $class = new \ReflectionClass($context['runtime']);
        $constants = $class->getConstants();
        $ret = " array(\n";
        foreach ($constants as $name => $value) {
            $ret .= "            '$name' => ".  (is_string($value) ? "'$value'" : $value) . ",\n";
        }
        $ret .= "        )";
        return $ret;
    }

    
    protected static function scanDependency($context, $code, $ocode)
    {
        $child = array();

        $code = preg_replace_callback('/static::(\w+?)\s*\(/', function ($matches) use ($context, &$child) {
            if (!isset($child[$matches[1]])) {
                $child[$matches[1]] = 0;
            }
            $child[$matches[1]]++;

            return "{$context['funcprefix']}{$matches[1]}(";
        }, $code);

        
        $code = preg_replace('/static::([A-Z0-9_]+)/', "\$cx['constants']['$1']", $code);

        
        $code = preg_replace('/    /', ' ', $code);

        return array(static::replaceSafeString($context, $code), $child, $ocode);
    }
}
