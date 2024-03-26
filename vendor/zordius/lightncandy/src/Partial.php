<?php




namespace LightnCandy;


class Partial
{
    public static $TMP_JS_FUNCTION_STR = "!!\aFuNcTiOn\a!!";

    
    public static function handleDynamic(&$context)
    {
        if ($context['usedFeature']['dynpartial'] == 0) {
            return;
        }

        foreach ($context['partials'] as $name => $code) {
            static::read($context, $name);
        }
    }

    
    public static function read(&$context, $name)
    {
        $isPB = ($name === '@partial-block');
        $context['usedFeature']['partial']++;

        if (isset($context['usedPartial'][$name])) {
            return;
        }

        $cnt = static::resolve($context, $name);

        if ($cnt !== null) {
            $context['usedPartial'][$name] = SafeString::escapeTemplate($cnt);
            return static::compileDynamic($context, $name);
        }

        if (!$context['flags']['skippartial'] && !$isPB) {
            $context['error'][] = "Can not find partial for '$name', you should provide partials or partialresolver in options";
        }
    }

    
    protected static function prePartial(&$context, $tmpl, &$name)
    {
        return $context['prepartial'] ? $context['prepartial']($context, $tmpl, $name) : $tmpl;
    }

    
    public static function resolve(&$context, &$name)
    {
        if ($name === '@partial-block') {
            $name = "@partial-block{$context['usedFeature']['pblock']}";
        }
        if (isset($context['partials'][$name])) {
            return static::prePartial($context, $context['partials'][$name], $name);
        }

        return static::resolver($context, $name);
    }

    
    public static function resolver(&$context, &$name)
    {
        if ($context['partialresolver']) {
            $cnt = $context['partialresolver']($context, $name);
            return static::prePartial($context, $cnt, $name);
        }
    }

    
    public static function compileStatic(&$context, $name)
    {
        
        if (!$context['flags']['runpart']) {
            $context['partialStack'][] = $name;
            $diff = count($context['partialStack']) - count(array_unique($context['partialStack']));
            if ($diff) {
                $context['error'][] = 'I found recursive partial includes as the path: ' . implode(' -> ', $context['partialStack']) . '! You should fix your template or compile with LightnCandy::FLAG_RUNTIMEPARTIAL flag.';
            }
        }

        $code = Compiler::compileTemplate($context, preg_replace('/^/m', $context['tokens']['partialind'], $context['usedPartial'][$name]));

        if (!$context['flags']['runpart']) {
            array_pop($context['partialStack']);
        }

        return $code;
    }

    
    public static function compileDynamic(&$context, $name)
    {
        if (!$context['flags']['runpart']) {
            return;
        }

        $func = static::compile($context, $context['usedPartial'][$name], $name);

        if (!isset($context['partialCode'][$name]) && $func) {
            $context['partialCode'][$name] = "'$name' => $func";
        }

        return $func;
    }

    
    public static function compile(&$context, $template, $name = 0)
    {
        if ((end($context['partialStack']) === $name) && (substr($name, 0, 14) === '@partial-block')) {
            return;
        }

        $tmpContext = $context;
        $tmpContext['inlinepartial'] = array();
        $tmpContext['partialblock'] = array();

        if ($name !== 0) {
            $tmpContext['partialStack'][] = $name;
        }

        $code = Compiler::compileTemplate($tmpContext, str_replace('function', static::$TMP_JS_FUNCTION_STR, $template));
        Context::merge($context, $tmpContext);
        if (!$context['flags']['noind']) {
            $sp = ', $sp';
            $code = preg_replace('/^/m', "'{$context['ops']['seperator']}\$sp{$context['ops']['seperator']}'", $code);
            
            $code = preg_replace('/\bfunction\s*\(([^\(]*?)\)\s*{/', 'function(\\1)use($sp){', $code);
            $code = preg_replace('/function\(\$cx, \$in, \$sp\)use\(\$sp\){/', 'function($cx, $in)use($sp){', $code);
        } else {
            $sp = '';
        }
        $code = str_replace(static::$TMP_JS_FUNCTION_STR, 'function', $code);
        return "function (\$cx, \$in{$sp}) {{$context['ops']['array_check']}{$context['ops']['op_start']}'$code'{$context['ops']['op_end']}}";
    }
}
