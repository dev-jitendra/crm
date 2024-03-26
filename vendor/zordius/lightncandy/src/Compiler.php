<?php




namespace LightnCandy;


class Compiler extends Validator
{
    public static $lastParsed;

    
    public static function compileTemplate(&$context, $template)
    {
        array_unshift($context['parsed'], array());
        Validator::verify($context, $template);
        static::$lastParsed = $context['parsed'];

        if (count($context['error'])) {
            return;
        }

        Parser::setDelimiter($context);

        $context['compile'] = true;

        
        Partial::handleDynamic($context);

        
        $code = '';
        foreach ($context['parsed'][0] as $info) {
            if (is_array($info)) {
                $context['tokens']['current']++;
                $code .= "'" . static::compileToken($context, $info) . "'";
            } else {
                $code .= $info;
            }
        }

        array_shift($context['parsed']);

        return $code;
    }

    
    public static function composePHPRender($context, $code)
    {
        $flagJStrue = Expression::boolString($context['flags']['jstrue']);
        $flagJSObj = Expression::boolString($context['flags']['jsobj']);
        $flagJSLen = Expression::boolString($context['flags']['jslen']);
        $flagSPVar = Expression::boolString($context['flags']['spvar']);
        $flagProp = Expression::boolString($context['flags']['prop']);
        $flagMethod = Expression::boolString($context['flags']['method']);
        $flagLambda = Expression::boolString($context['flags']['lambda']);
        $flagMustlok = Expression::boolString($context['flags']['mustlok']);
        $flagMustlam = Expression::boolString($context['flags']['mustlam']);
        $flagMustsec = Expression::boolString($context['flags']['mustsec']);
        $flagEcho = Expression::boolString($context['flags']['echo']);
        $flagPartNC = Expression::boolString($context['flags']['partnc']);
        $flagKnownHlp = Expression::boolString($context['flags']['knohlp']);

        $constants = Exporter::constants($context);
        $helpers = Exporter::helpers($context);
        $partials = implode(",\n", $context['partialCode']);
        $debug = Runtime::DEBUG_ERROR_LOG;
        $use = $context['flags']['standalone'] ? Exporter::runtime($context) : "use {$context['runtime']} as {$context['runtimealias']};";
        $stringObject = $context['flags']['method'] || $context['flags']['prop'] ? Exporter::stringobject($context) : '';
        $safeString = (($context['usedFeature']['enc'] > 0) && ($context['flags']['standalone'] === 0)) ? "use {$context['safestring']} as SafeString;" : '';
        $exportSafeString = (($context['usedFeature']['enc'] > 0) && ($context['flags']['standalone'] >0)) ? Exporter::safestring($context) : '';
        
        return <<<VAREND
$stringObject{$safeString}{$use}{$exportSafeString}return function (\$in = null, \$options = null) {
    \$helpers = $helpers;
    \$partials = array($partials);
    \$cx = array(
        'flags' => array(
            'jstrue' => $flagJStrue,
            'jsobj' => $flagJSObj,
            'jslen' => $flagJSLen,
            'spvar' => $flagSPVar,
            'prop' => $flagProp,
            'method' => $flagMethod,
            'lambda' => $flagLambda,
            'mustlok' => $flagMustlok,
            'mustlam' => $flagMustlam,
            'mustsec' => $flagMustsec,
            'echo' => $flagEcho,
            'partnc' => $flagPartNC,
            'knohlp' => $flagKnownHlp,
            'debug' => isset(\$options['debug']) ? \$options['debug'] : $debug,
        ),
        'constants' => $constants,
        'helpers' => isset(\$options['helpers']) ? array_merge(\$helpers, \$options['helpers']) : \$helpers,
        'partials' => isset(\$options['partials']) ? array_merge(\$partials, \$options['partials']) : \$partials,
        'scopes' => array(),
        'sp_vars' => isset(\$options['data']) ? array_merge(array('root' => \$in), \$options['data']) : array('root' => \$in),
        'blparam' => array(),
        'partialid' => 0,
        'runtime' => '{$context['runtime']}',
    );
    {$context['renderex']}
    {$context['ops']['array_check']}
    {$context['ops']['op_start']}'$code'{$context['ops']['op_end']}
};
VAREND
        ;
    }

    
    protected static function getFuncName(&$context, $name, $tag)
    {
        static::addUsageCount($context, 'runtime', $name);

        if ($context['flags']['debug'] && ($name != 'miss')) {
            $dbg = "'$tag', '$name', ";
            $name = 'debug';
            static::addUsageCount($context, 'runtime', 'debug');
        } else {
            $dbg = '';
        }

        return $context['flags']['standalone'] ? "{$context['funcprefix']}$name($dbg" : "{$context['runtimealias']}::$name($dbg";
    }

    
    protected static function getVariableNames(&$context, $vn, $blockParams = null)
    {
        $vars = array(array(), array());
        $exps = array();
        foreach ($vn as $i => $v) {
            $V = static::getVariableNameOrSubExpression($context, $v);
            if (is_string($i)) {
                $vars[1][] = "'$i'=>{$V[0]}";
            } else {
                $vars[0][] = $V[0];
            }
            $exps[] = $V[1];
        }
        $bp = $blockParams ? (',array(' . Expression::listString($blockParams) . ')') : '';
        return array('array(array(' . implode(',', $vars[0]) . '),array(' . implode(',', $vars[1]) . ")$bp)", $exps);
    }

    
    public static function compileSubExpression(&$context, $vars)
    {
        $ret = static::customHelper($context, $vars, true, true, true);

        if (($ret === null) && $context['flags']['lambda']) {
            $ret = static::compileVariable($context, $vars, true, true);
        }

        return array($ret ? $ret : '', 'FIXME: $subExpression');
    }

    
    protected static function getVariableNameOrSubExpression(&$context, $var)
    {
        return Parser::isSubExp($var) ? static::compileSubExpression($context, $var[1]) : static::getVariableName($context, $var);
    }

    
    protected static function getVariableName(&$context, $var, $lookup = null, $args = null)
    {
        if (isset($var[0]) && ($var[0] === Parser::LITERAL)) {
            if ($var[1] === "undefined") {
                $var[1] = "null";
            }
            return array($var[1], preg_replace('/\'(.*)\'/', '$1', $var[1]));
        }

        list($levels, $spvar, $var) = Expression::analyze($context, $var);
        $exp = Expression::toString($levels, $spvar, $var);
        $base = $spvar ? "\$cx['sp_vars']" : '$in';

        
        if ($levels > 0) {
            if ($spvar) {
                $base .= str_repeat("['_parent']", $levels);
            } else {
                $base = "\$cx['scopes'][count(\$cx['scopes'])-$levels]";
            }
        }

        if ((empty($var) || (count($var) == 0) || (($var[0] === null) && (count($var) == 1))) && ($lookup === null)) {
            return array($base, $exp);
        }

        if ((count($var) > 0) && ($var[0] === null)) {
            array_shift($var);
        }

        
        
        if ($context['flags']['prop'] || $context['flags']['method'] || $context['flags']['mustlok'] || $context['flags']['mustlam'] || $context['flags']['lambda']) {
            $L = Expression::listString($var);
            $L = ($L === '') ? array() : array($L);
            if ($lookup) {
                $L[] = $lookup[0];
            }
            $A = $args ? ",$args[0]" : '';
            $E = $args ? ' ' . implode(' ', $args[1]) : '';
            return array(static::getFuncName($context, 'v', $exp) . "\$cx, \$in, isset($base) ? $base : null, array(" . implode(',', $L) . ")$A)", $lookup ? "lookup $exp $lookup[1]" : "$exp$E");
        }

        $n = Expression::arrayString($var);
        $k = array_pop($var);
        $L = $lookup ? "[{$lookup[0]}]" : '';
        $p = $lookup ? $n : (count($var) ? Expression::arrayString($var) : '');

        $checks = array();
        if ($levels > 0) {
            $checks[] = "isset($base)";
        }
        if (!$spvar) {
            if (($levels === 0) && $p) {
                $checks[] = "isset($base$p)";
            }
            $checks[] = ("$base$p" == '$in') ? '$inary' : "is_array($base$p)";
        }
        $checks[] = "isset($base$n$L)";
        $check = ((count($checks) > 1) ? '(' : '') . implode(' && ', $checks) . ((count($checks) > 1) ? ')' : '');

        $lenStart = '';
        $lenEnd = '';

        if ($context['flags']['jslen']) {
            if (($lookup === null) && ($k === 'length')) {
                array_pop($checks);
                $lenStart = '(' . ((count($checks) > 1) ? '(' : '') . implode(' && ', $checks) . ((count($checks) > 1) ? ')' : '') . " ? count($base" . Expression::arrayString($var) . ') : ';
                $lenEnd = ')';
            }
        }

        return array("($check ? $base$n$L : $lenStart" . ($context['flags']['debug'] ? (static::getFuncName($context, 'miss', '') . "\$cx, '$exp')") : 'null') . ")$lenEnd", $lookup ? "lookup $exp $lookup[1]" : $exp);
    }

    
    protected static function compileToken(&$context, $info)
    {
        list($raw, $vars, $token, $indent) = $info;

        $context['tokens']['partialind'] = $indent;
        $context['currentToken'] = $token;

        if ($ret = static::operator($token[Token::POS_OP], $context, $vars)) {
            return $ret;
        }

        if (isset($vars[0][0])) {
            if ($ret = static::customHelper($context, $vars, $raw, true)) {
                return static::compileOutput($context, $ret, 'FIXME: helper', $raw, false);
            }
            if ($context['flags']['else'] && ($vars[0][0] === 'else')) {
                return static::doElse($context, $vars);
            }
            if ($vars[0][0] === 'lookup') {
                return static::compileLookup($context, $vars, $raw);
            }
            if ($vars[0][0] === 'log') {
                return static::compileLog($context, $vars, $raw);
            }
        }

        return static::compileVariable($context, $vars, $raw, false);
    }

    
    public static function partial(&$context, $vars)
    {
        Parser::getBlockParams($vars);
        $pid = Parser::getPartialBlock($vars);
        $p = array_shift($vars);
        if ($context['flags']['runpart']) {
            if (!isset($vars[0])) {
                $vars[0] = $context['flags']['partnc'] ? array(0, 'null') : array();
            }
            $v = static::getVariableNames($context, $vars);
            $tag = ">$p[0] " .implode(' ', $v[1]);
            if (Parser::isSubExp($p)) {
                list($p) = static::compileSubExpression($context, $p[1]);
            } else {
                $p = "'$p[0]'";
            }
            $sp = $context['tokens']['partialind'] ? ", '{$context['tokens']['partialind']}'" : '';
            return $context['ops']['seperator'] . static::getFuncName($context, 'p', $tag) . "\$cx, $p, $v[0],$pid$sp){$context['ops']['seperator']}";
        }
        return isset($context['usedPartial'][$p[0]]) ? "{$context['ops']['seperator']}'" . Partial::compileStatic($context, $p[0]) . "'{$context['ops']['seperator']}" : $context['ops']['seperator'];
    }

    
    public static function inline(&$context, $vars)
    {
        Parser::getBlockParams($vars);
        list($code) = array_shift($vars);
        $p = array_shift($vars);
        if (!isset($vars[0])) {
            $vars[0] = $context['flags']['partnc'] ? array(0, 'null') : array();
        }
        $v = static::getVariableNames($context, $vars);
        $tag = ">*inline $p[0]" .implode(' ', $v[1]);
        return $context['ops']['seperator'] . static::getFuncName($context, 'in', $tag) . "\$cx, '{$p[0]}', $code){$context['ops']['seperator']}";
    }

    
    protected static function invertedSection(&$context, $vars)
    {
        $v = static::getVariableName($context, $vars[0]);
        return "{$context['ops']['cnd_start']}(" . static::getFuncName($context, 'isec', '^' . $v[1]) . "\$cx, {$v[0]})){$context['ops']['cnd_then']}";
    }

    
    protected static function blockCustomHelper(&$context, $vars, $inverted = false)
    {
        $bp = Parser::getBlockParams($vars);
        $ch = array_shift($vars);
        $inverted = $inverted ? 'true' : 'false';
        static::addUsageCount($context, 'helpers', $ch[0]);
        $v = static::getVariableNames($context, $vars, $bp);

        return $context['ops']['seperator'] . static::getFuncName($context, 'hbbch', ($inverted ? '^' : '#') . implode(' ', $v[1])) . "\$cx, '$ch[0]', {$v[0]}, \$in, $inverted, function(\$cx, \$in) {{$context['ops']['array_check']}{$context['ops']['f_start']}";
    }

    
    protected static function blockEnd(&$context, &$vars, $matchop = null)
    {
        $pop = $context['stack'][count($context['stack']) - 1];

        switch (isset($context['helpers'][$context['currentToken'][Token::POS_INNERTAG]]) ? 'skip' : $context['currentToken'][Token::POS_INNERTAG]) {
            case 'if':
            case 'unless':
                if ($pop === ':') {
                    array_pop($context['stack']);
                    return "{$context['ops']['cnd_end']}";
                }
                if (!$context['flags']['nohbh']) {
                    return "{$context['ops']['cnd_else']}''{$context['ops']['cnd_end']}";
                }
                break;
            case 'with':
                if (!$context['flags']['nohbh']) {
                    return "{$context['ops']['f_end']}}){$context['ops']['seperator']}";
                }
        }

        if ($pop === ':') {
            array_pop($context['stack']);
            return "{$context['ops']['f_end']}}){$context['ops']['seperator']}";
        }

        switch ($pop) {
            case '#':
                return "{$context['ops']['f_end']}}){$context['ops']['seperator']}";
            case '^':
                return "{$context['ops']['cnd_else']}''{$context['ops']['cnd_end']}";
        }
    }

    
    protected static function blockBegin(&$context, $vars)
    {
        $v = isset($vars[1]) ? static::getVariableNameOrSubExpression($context, $vars[1]) : array(null, array());
        if (!$context['flags']['nohbh']) {
            switch (isset($vars[0][0]) ? $vars[0][0] : null) {
                case 'if':
                    $includeZero = (isset($vars['includeZero'][1]) && $vars['includeZero'][1]) ? 'true' : 'false';
                    return "{$context['ops']['cnd_start']}(" . static::getFuncName($context, 'ifvar', $v[1]) . "\$cx, {$v[0]}, {$includeZero})){$context['ops']['cnd_then']}";
                case 'unless':
                    return "{$context['ops']['cnd_start']}(!" . static::getFuncName($context, 'ifvar', $v[1]) . "\$cx, {$v[0]}, false)){$context['ops']['cnd_then']}";
                case 'each':
                    return static::section($context, $vars, true);
                case 'with':
                    if ($r = static::with($context, $vars)) {
                        return $r;
                    }
            }
        }

        return static::section($context, $vars);
    }

    
    protected static function section(&$context, $vars, $isEach = false)
    {
        $bs = 'null';
        $be = '';
        if ($isEach) {
            $bp = Parser::getBlockParams($vars);
            $bs = $bp ? ('array(' . Expression::listString($bp) . ')') : 'null';
            $be = $bp ? (' as |' . implode(' ', $bp) . '|') : '';
            array_shift($vars);
        }
        if ($context['flags']['lambda'] && !$isEach) {
            $V = array_shift($vars);
            $v = static::getVariableName($context, $V, null, count($vars) ? static::getVariableNames($context, $vars) : array('0',array('')));
        } else {
            $v = static::getVariableNameOrSubExpression($context, $vars[0]);
        }
        $each = $isEach ? 'true' : 'false';
        return $context['ops']['seperator'] . static::getFuncName($context, 'sec', ($isEach ? 'each ' : '') . $v[1] . $be) . "\$cx, {$v[0]}, $bs, \$in, $each, function(\$cx, \$in) {{$context['ops']['array_check']}{$context['ops']['f_start']}";
    }

    
    protected static function with(&$context, $vars)
    {
        $v = isset($vars[1]) ? static::getVariableNameOrSubExpression($context, $vars[1]) : array(null, array());
        $bp = Parser::getBlockParams($vars);
        $bs = $bp ? ('array(' . Expression::listString($bp) . ')') : 'null';
        $be = $bp ? " as |$bp[0]|" : '';
        return $context['ops']['seperator'] . static::getFuncName($context, 'wi', 'with ' . $v[1] . $be) . "\$cx, {$v[0]}, $bs, \$in, function(\$cx, \$in) {{$context['ops']['array_check']}{$context['ops']['f_start']}";
    }

    
    protected static function customHelper(&$context, $vars, $raw, $nosep, $subExp = false)
    {
        if (count($vars[0]) > 1) {
            return;
        }

        if (!isset($context['helpers'][$vars[0][0]])) {
            if ($subExp) {
                if ($vars[0][0] == 'lookup') {
                    return static::compileLookup($context, $vars, $raw, true);
                }
            }
            return;
        }

        $fn = $raw ? 'raw' : $context['ops']['enc'];
        $ch = array_shift($vars);
        $v = static::getVariableNames($context, $vars);
        static::addUsageCount($context, 'helpers', $ch[0]);
        $sep = $nosep ? '' : $context['ops']['seperator'];

        return $sep . static::getFuncName($context, 'hbch', "$ch[0] " . implode(' ', $v[1])) . "\$cx, '$ch[0]', {$v[0]}, '$fn', \$in)$sep";
    }

    
    protected static function doElse(&$context, $vars)
    {
        $v = $context['stack'][count($context['stack']) - 2];

        if ((($v === '[if]') && !isset($context['helpers']['if'])) ||
           (($v === '[unless]') && !isset($context['helpers']['unless']))) {
            $context['stack'][] = ':';
            return "{$context['ops']['cnd_else']}";
        }

        return "{$context['ops']['f_end']}}, function(\$cx, \$in) {{$context['ops']['array_check']}{$context['ops']['f_start']}";
    }

    
    protected static function compileLog(&$context, &$vars, $raw)
    {
        array_shift($vars);
        $v = static::getVariableNames($context, $vars);
        return $context['ops']['seperator'] . static::getFuncName($context, 'lo', $v[1]) . "\$cx, {$v[0]}){$context['ops']['seperator']}";
    }

    
    protected static function compileLookup(&$context, &$vars, $raw, $nosep = false)
    {
        $v2 = static::getVariableName($context, $vars[2]);
        $v = static::getVariableName($context, $vars[1], $v2);
        $sep = $nosep ? '' : $context['ops']['seperator'];
        $ex = $nosep ? ', 1' : '';

        if ($context['flags']['hbesc'] || $context['flags']['jsobj'] || $context['flags']['jstrue'] || $context['flags']['debug']) {
            return $sep . static::getFuncName($context, $raw ? 'raw' : $context['ops']['enc'], $v[1]) . "\$cx, {$v[0]}$ex){$sep}";
        } else {
            return $raw ? "{$sep}$v[0]{$sep}" : "{$sep}htmlspecialchars((string){$v[0]}, ENT_QUOTES, 'UTF-8'){$sep}";
        }
    }

    
    protected static function compileOutput(&$context, $variable, $expression, $raw, $nosep)
    {
        $sep = $nosep ? '' : $context['ops']['seperator'];
        if ($context['flags']['hbesc'] || $context['flags']['jsobj'] || $context['flags']['jstrue'] || $context['flags']['debug'] || $nosep) {
            return $sep . static::getFuncName($context, $raw ? 'raw' : $context['ops']['enc'], $expression) . "\$cx, $variable)$sep";
        } else {
            return $raw ? "$sep$variable{$context['ops']['seperator']}" : "{$context['ops']['seperator']}htmlspecialchars((string)$variable, ENT_QUOTES, 'UTF-8')$sep";
        }
    }

    
    protected static function compileVariable(&$context, &$vars, $raw, $nosep)
    {
        if ($context['flags']['lambda']) {
            $V = array_shift($vars);
            $v = static::getVariableName($context, $V, null, count($vars) ? static::getVariableNames($context, $vars) : array('0',array('')));
        } else {
            $v = static::getVariableName($context, $vars[0]);
        }
        return static::compileOutput($context, $v[0], $v[1], $raw, $nosep);
    }

    
    protected static function addUsageCount(&$context, $category, $name, $count = 1)
    {
        if (!isset($context['usedCount'][$category][$name])) {
            $context['usedCount'][$category][$name] = 0;
        }
        return ($context['usedCount'][$category][$name] += $count);
    }
}
