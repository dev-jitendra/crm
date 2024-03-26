<?php




namespace LightnCandy;


class Validator
{
    
    public static function verify(&$context, $template)
    {
        $template = SafeString::stripExtendedComments($template);
        $context['level'] = 0;
        Parser::setDelimiter($context);

        while (preg_match($context['tokens']['search'], $template, $matches)) {
            
            if ($context['flags']['slash'] && ($matches[Token::POS_LSPACE] === '') && preg_match('/^(.*?)(\\\\+)$/s', $matches[Token::POS_LOTHER], $escmatch)) {
                if (strlen($escmatch[2]) % 4) {
                    static::pushToken($context, substr($matches[Token::POS_LOTHER], 0, -2) . $context['tokens']['startchar']);
                    $matches[Token::POS_BEGINTAG] = substr($matches[Token::POS_BEGINTAG], 1);
                    $template = implode('', array_slice($matches, Token::POS_BEGINTAG));
                    continue;
                } else {
                    $matches[Token::POS_LOTHER] = $escmatch[1] . str_repeat('\\', strlen($escmatch[2]) / 2);
                }
            }
            $context['tokens']['count']++;
            $V = static::token($matches, $context);
            static::pushLeft($context);
            if ($V) {
                if (is_array($V)) {
                    array_push($V, $matches, $context['tokens']['partialind']);
                }
                static::pushToken($context, $V);
            }
            $template = "{$matches[Token::POS_RSPACE]}{$matches[Token::POS_ROTHER]}";
        }
        static::pushToken($context, $template);

        if ($context['level'] > 0) {
            array_pop($context['stack']);
            array_pop($context['stack']);
            $token = array_pop($context['stack']);
            $context['error'][] = 'Unclosed token ' . ($context['rawblock'] ? "{{{{{$token}}}}}" : ($context['partialblock'] ? "{{#>{$token}}}" : "{{#{$token}}}")) . ' !!';
        }
    }

    
    protected static function pushLeft(&$context)
    {
        $L = $context['currentToken'][Token::POS_LOTHER] . $context['currentToken'][Token::POS_LSPACE];
        static::pushToken($context, $L);
        $context['currentToken'][Token::POS_LOTHER] = $context['currentToken'][Token::POS_LSPACE] = '';
    }

    
    protected static function pushPartial(&$context, $append)
    {
        $appender = function (&$p) use ($append) {
            $p .= $append;
        };
        array_walk($context['inlinepartial'], $appender);
        array_walk($context['partialblock'], $appender);
    }

    
    protected static function pushToken(&$context, $token)
    {
        if ($token === '') {
            return;
        }
        if (is_string($token)) {
            static::pushPartial($context, $token);
            if (is_string(end($context['parsed'][0]))) {
                $context['parsed'][0][key($context['parsed'][0])] .= $token;
                return;
            }
        } else {
            static::pushPartial($context, Token::toString($context['currentToken']));
            switch ($context['currentToken'][Token::POS_OP]) {
            case '#*':
                array_unshift($context['inlinepartial'], '');
                break;
            case '#>':
                array_unshift($context['partialblock'], '');
                break;
            }
        }
        $context['parsed'][0][] = $token;
    }

    
    protected static function pushStack(&$context, $operation, $vars)
    {
        list($levels, $spvar, $var) = Expression::analyze($context, $vars[0]);
        $context['stack'][] = $context['currentToken'][Token::POS_INNERTAG];
        $context['stack'][] = Expression::toString($levels, $spvar, $var);
        $context['stack'][] = $operation;
        $context['level']++;
    }

    
    protected static function delimiter($token, &$context)
    {
        
        if (strlen($token[Token::POS_BEGINRAW]) !== strlen($token[Token::POS_ENDRAW])) {
            $context['error'][] = 'Bad token ' . Token::toString($token) . ' ! Do you mean ' . Token::toString($token, array(Token::POS_BEGINRAW => '', Token::POS_ENDRAW => '')) . ' or ' . Token::toString($token, array(Token::POS_BEGINRAW => '{', Token::POS_ENDRAW => '}')) . '?';
            return true;
        }
        
        if ((strlen($token[Token::POS_BEGINRAW]) == 1) && $token[Token::POS_OP] && ($token[Token::POS_OP] !== '&')) {
            $context['error'][] = 'Bad token ' . Token::toString($token) . ' ! Do you mean ' . Token::toString($token, array(Token::POS_BEGINRAW => '', Token::POS_ENDRAW => '')) . ' ?';
            return true;
        }
    }

    
    protected static function operator($operator, &$context, &$vars)
    {
        switch ($operator) {
            case '#*':
                if (!$context['compile']) {
                    $context['stack'][] = count($context['parsed'][0]) + ($context['currentToken'][Token::POS_LOTHER] . $context['currentToken'][Token::POS_LSPACE] === '' ? 0 : 1);
                    static::pushStack($context, '#*', $vars);
                }
                return static::inline($context, $vars);

            case '#>':
                if (!$context['compile']) {
                    $context['stack'][] = count($context['parsed'][0]) + ($context['currentToken'][Token::POS_LOTHER] . $context['currentToken'][Token::POS_LSPACE] === '' ? 0 : 1);
                    $vars[Parser::PARTIALBLOCK] = ++$context['usedFeature']['pblock'];
                    static::pushStack($context, '#>', $vars);
                }
                
            case '>':
                return static::partial($context, $vars);

            case '^':
                if (!isset($vars[0][0])) {
                    if (!$context['flags']['else']) {
                        $context['error'][] = 'Do not support {{^}}, you should do compile with LightnCandy::FLAG_ELSE flag';
                        return;
                    } else {
                        return static::doElse($context, $vars);
                    }
                }

                static::doElseChain($context);

                if (static::isBlockHelper($context, $vars)) {
                    static::pushStack($context, '#', $vars);
                    return static::blockCustomHelper($context, $vars, true);
                }

                static::pushStack($context, '^', $vars);
                return static::invertedSection($context, $vars);

            case '/':
                $r = static::blockEnd($context, $vars);
                if ($r !== Token::POS_BACKFILL) {
                    array_pop($context['stack']);
                    array_pop($context['stack']);
                    array_pop($context['stack']);
                }
                return $r;

            case '#':
                static::doElseChain($context);
                static::pushStack($context, '#', $vars);

                if (static::isBlockHelper($context, $vars)) {
                    return static::blockCustomHelper($context, $vars);
                }

                return static::blockBegin($context, $vars);
        }
    }

    
    protected static function inlinePartial(&$context, $vars)
    {
        $ended = false;
        if ($context['currentToken'][Token::POS_OP] === '/') {
            if (static::blockEnd($context, $vars, '#*') !== null) {
                $context['usedFeature']['inlpartial']++;
                $tmpl = array_shift($context['inlinepartial']) . $context['currentToken'][Token::POS_LOTHER] . $context['currentToken'][Token::POS_LSPACE];
                $c = $context['stack'][count($context['stack']) - 4];
                $context['parsed'][0] = array_slice($context['parsed'][0], 0, $c + 1);
                $P = &$context['parsed'][0][$c];
                if (isset($P[1][1][0])) {
                    $context['usedPartial'][$P[1][1][0]] = $tmpl;
                    $P[1][0][0] = Partial::compileDynamic($context, $P[1][1][0]);
                }
                $ended = true;
            }
        }
        return $ended;
    }

    
    protected static function partialBlock(&$context, $vars)
    {
        $ended = false;
        if ($context['currentToken'][Token::POS_OP] === '/') {
            if (static::blockEnd($context, $vars, '#>') !== null) {
                $c = $context['stack'][count($context['stack']) - 4];
                $context['parsed'][0] = array_slice($context['parsed'][0], 0, $c + 1);
                $found = Partial::resolve($context, $vars[0][0]) !== null;
                $v = $found ? "@partial-block{$context['parsed'][0][$c][1][Parser::PARTIALBLOCK]}" : "{$vars[0][0]}";
                if (count($context['partialblock']) == 1) {
                    $tmpl = $context['partialblock'][0] . $context['currentToken'][Token::POS_LOTHER] . $context['currentToken'][Token::POS_LSPACE];
                    if ($found) {
                        $context['partials'][$v] = $tmpl;
                    }
                    $context['usedPartial'][$v] = $tmpl;
                    Partial::compileDynamic($context, $v);
                    if ($found) {
                        Partial::read($context, $vars[0][0]);
                    }
                }
                array_shift($context['partialblock']);
                $ended = true;
            }
        }
        return $ended;
    }

    
    protected static function doElseChain(&$context)
    {
        if ($context['elsechain']) {
            $context['elsechain'] = false;
        } else {
            array_unshift($context['elselvl'], array());
        }
    }

    
    protected static function blockBegin(&$context, $vars)
    {
        switch ((isset($vars[0][0]) && is_string($vars[0][0])) ? $vars[0][0] : null) {
            case 'with':
                return static::with($context, $vars);
            case 'each':
                return static::section($context, $vars, true);
            case 'unless':
                return static::unless($context, $vars);
            case 'if':
                return static::doIf($context, $vars);
            default:
                return static::section($context, $vars);
        }
    }

    
    protected static function builtin(&$context, $vars)
    {
        if ($context['flags']['nohbh']) {
            if (isset($vars[1][0])) {
                $context['error'][] = "Do not support {{#{$vars[0][0]} var}} because you compile with LightnCandy::FLAG_NOHBHELPERS flag";
            }
        } else {
            if (count($vars) < 2) {
                $context['error'][] = "No argument after {{#{$vars[0][0]}}} !";
            }
        }
        $context['usedFeature'][$vars[0][0]]++;
    }

    
    protected static function section(&$context, $vars, $isEach = false)
    {
        if ($isEach) {
            static::builtin($context, $vars);
        } else {
            if ((count($vars) > 1) && !$context['flags']['lambda']) {
                $context['error'][] = "Custom helper not found: {$vars[0][0]} in " . Token::toString($context['currentToken']) . ' !';
            }
            $context['usedFeature']['sec']++;
        }
        return true;
    }

    
    protected static function with(&$context, $vars)
    {
        static::builtin($context, $vars);
        return true;
    }

    
    protected static function unless(&$context, $vars)
    {
        static::builtin($context, $vars);
        return true;
    }

    
    protected static function doIf(&$context, $vars)
    {
        static::builtin($context, $vars);
        return true;
    }

    
    protected static function blockCustomHelper(&$context, $vars, $inverted = false)
    {
        if (is_string($vars[0][0])) {
            if (static::resolveHelper($context, $vars)) {
                return ++$context['usedFeature']['helper'];
            }
        }
    }

    
    protected static function invertedSection(&$context, $vars)
    {
        return ++$context['usedFeature']['isec'];
    }

    
    protected static function blockEnd(&$context, &$vars, $match = null)
    {
        $c = count($context['stack']) - 2;
        $pop = ($c >= 0) ? $context['stack'][$c + 1] : '';
        if (($match !== null) && ($match !== $pop)) {
            return;
        }
        
        $context['level']--;
        $pop2 = ($c >= 0) ? $context['stack'][$c]: '';
        switch ($context['currentToken'][Token::POS_INNERTAG]) {
            case 'with':
                if (!$context['flags']['nohbh']) {
                    if ($pop2 !== '[with]') {
                        $context['error'][] = 'Unexpect token: {{/with}} !';
                        return;
                    }
                }
                return true;
        }

        switch ($pop) {
            case '#':
            case '^':
                $elsechain = array_shift($context['elselvl']);
                if (isset($elsechain[0])) {
                    
                    $context['level']++;
                    $context['currentToken'][Token::POS_RSPACE] = $context['currentToken'][Token::POS_BACKFILL] = '{{/' . implode('}}{{/', $elsechain) . '}}' . Token::toString($context['currentToken']) . $context['currentToken'][Token::POS_RSPACE];
                    return Token::POS_BACKFILL;
                }
                
            case '#>':
            case '#*':
                list($levels, $spvar, $var) = Expression::analyze($context, $vars[0]);
                $v = Expression::toString($levels, $spvar, $var);
                if ($pop2 !== $v) {
                    $context['error'][] = 'Unexpect token ' . Token::toString($context['currentToken']) . " ! Previous token {{{$pop}$pop2}} is not closed";
                    return;
                }
                return true;
            default:
                $context['error'][] = 'Unexpect token: ' . Token::toString($context['currentToken']) . ' !';
                return;
        }
    }

    
    protected static function isDelimiter(&$context)
    {
        if (preg_match('/^=\s*([^ ]+)\s+([^ ]+)\s*=$/', $context['currentToken'][Token::POS_INNERTAG], $matched)) {
            $context['usedFeature']['delimiter']++;
            Parser::setDelimiter($context, $matched[1], $matched[2]);
            return true;
        }
    }

    
    protected static function rawblock(&$token, &$context)
    {
        $inner = $token[Token::POS_INNERTAG];
        trim($inner);

        
        if ($context['rawblock'] && !(($token[Token::POS_BEGINRAW] === '{{') && ($token[Token::POS_OP] === '/') && ($context['rawblock'] === $inner))) {
            return true;
        }

        $token[Token::POS_INNERTAG] = $inner;

        
        if ($token[Token::POS_BEGINRAW] === '{{') {
            if ($token[Token::POS_ENDRAW] !== '}}') {
                $context['error'][] = 'Bad token ' . Token::toString($token) . ' ! Do you mean ' . Token::toString($token, array(Token::POS_ENDRAW => '}}')) . ' ?';
            }
            if ($context['rawblock']) {
                Parser::setDelimiter($context);
                $context['rawblock'] = false;
            } else {
                if ($token[Token::POS_OP]) {
                    $context['error'][] = "Wrong raw block begin with " . Token::toString($token) . ' ! Remove "' . $token[Token::POS_OP] . '" to fix this issue.';
                }
                $context['rawblock'] = $token[Token::POS_INNERTAG];
                Parser::setDelimiter($context);
                $token[Token::POS_OP] = '#';
            }
            $token[Token::POS_ENDRAW] = '}}';
        }
    }

    
    protected static function comment(&$token, &$context)
    {
        if ($token[Token::POS_OP] === '!') {
            $context['usedFeature']['comment']++;
            return true;
        }
    }

    
    protected static function token(&$token, &$context)
    {
        $context['currentToken'] = &$token;

        if (static::rawblock($token, $context)) {
            return Token::toString($token);
        }

        if (static::delimiter($token, $context)) {
            return;
        }

        if (static::isDelimiter($context)) {
            static::spacing($token, $context);
            return;
        }

        if (static::comment($token, $context)) {
            static::spacing($token, $context);
            return;
        }

        list($raw, $vars) = Parser::parse($token, $context);

        
        static::spacing($token, $context, (($token[Token::POS_OP] === '') || ($token[Token::POS_OP] === '&')) && (!$context['flags']['else'] || !isset($vars[0][0]) || ($vars[0][0] !== 'else')) || ($context['flags']['nostd'] > 0));

        $inlinepartial = static::inlinePartial($context, $vars);
        $partialblock = static::partialBlock($context, $vars);

        if ($partialblock || $inlinepartial) {
            $context['stack'] = array_slice($context['stack'], 0, -4);
            static::pushPartial($context, $context['currentToken'][Token::POS_LOTHER] . $context['currentToken'][Token::POS_LSPACE] . Token::toString($context['currentToken']));
            $context['currentToken'][Token::POS_LOTHER] = '';
            $context['currentToken'][Token::POS_LSPACE] = '';
            return;
        }

        if (static::operator($token[Token::POS_OP], $context, $vars)) {
            return isset($token[Token::POS_BACKFILL]) ? null : array($raw, $vars);
        }

        if (count($vars) == 0) {
            return $context['error'][] = 'Wrong variable naming in ' . Token::toString($token);
        }

        if (!isset($vars[0])) {
            return $context['error'][] = 'Do not support name=value in ' . Token::toString($token) . ', you should use it after a custom helper.';
        }

        $context['usedFeature'][$raw ? 'raw' : 'enc']++;

        foreach ($vars as $var) {
            if (!isset($var[0]) || ($var[0] === 0)) {
                if ($context['level'] == 0) {
                    $context['usedFeature']['rootthis']++;
                }
                $context['usedFeature']['this']++;
            }
        }

        if (!isset($vars[0][0])) {
            return array($raw, $vars);
        }

        if (($vars[0][0] === 'else') && $context['flags']['else']) {
            static::doElse($context, $vars);
            return array($raw, $vars);
        }

        if (!static::helper($context, $vars)) {
            static::lookup($context, $vars);
            static::log($context, $vars);
        }

        return array($raw, $vars);
    }

    
    protected static function doElse(&$context, $vars)
    {
        if ($context['level'] == 0) {
            $context['error'][] = '{{else}} only valid in if, unless, each, and #section context';
        }

        if (isset($vars[1][0])) {
            $token = $context['currentToken'];
            $context['currentToken'][Token::POS_INNERTAG] = 'else';
            $context['currentToken'][Token::POS_RSPACE] = "{{#{$vars[1][0]} " . preg_replace('/^\\s*else\\s+' . $vars[1][0] . '\\s*/', '', $token[Token::POS_INNERTAG]) . '}}' . $context['currentToken'][Token::POS_RSPACE];
            array_unshift($context['elselvl'][0], $vars[1][0]);
            $context['elsechain'] = true;
        }

        return ++$context['usedFeature']['else'];
    }

    
    public static function log(&$context, $vars)
    {
        if (isset($vars[0][0]) && ($vars[0][0] === 'log')) {
            if (!$context['flags']['nohbh']) {
                if (count($vars) < 2) {
                    $context['error'][] = "No argument after {{log}} !";
                }
                $context['usedFeature']['log']++;
                return true;
            }
        }
    }

    
    public static function lookup(&$context, $vars)
    {
        if (isset($vars[0][0]) && ($vars[0][0] === 'lookup')) {
            if (!$context['flags']['nohbh']) {
                if (count($vars) < 2) {
                    $context['error'][] = "No argument after {{lookup}} !";
                } elseif (count($vars) < 3) {
                    $context['error'][] = "{{lookup}} requires 2 arguments !";
                }
                $context['usedFeature']['lookup']++;
                return true;
            }
        }
    }

    
    public static function helper(&$context, $vars, $checkSubexp = false)
    {
        if (static::resolveHelper($context, $vars)) {
            $context['usedFeature']['helper']++;
            return true;
        }

        if ($checkSubexp) {
            switch ($vars[0][0]) {
                case 'if':
                case 'unless':
                case 'with':
                case 'each':
                case 'lookup':
                    return $context['flags']['nohbh'] ? false : true;
            }
        }

        return false;
    }

    
    public static function resolveHelper(&$context, &$vars)
    {
        if (count($vars[0]) !== 1) {
            return false;
        }
        if (isset($context['helpers'][$vars[0][0]])) {
            return true;
        }

        if ($context['helperresolver']) {
            $helper = $context['helperresolver']($context, $vars[0][0]);
            if ($helper) {
                $context['helpers'][$vars[0][0]] = $helper;
                return true;
            }
        }

        return false;
    }

    
    protected static function isBlockHelper($context, $vars)
    {
        if (!isset($vars[0][0])) {
            return;
        }

        if (!static::resolveHelper($context, $vars)) {
            return;
        }

        return true;
    }

    
    protected static function inline(&$context, $vars)
    {
        if (!$context['flags']['runpart']) {
            $context['error'][] = "Do not support {{#*{$context['currentToken'][Token::POS_INNERTAG]}}}, you should do compile with LightnCandy::FLAG_RUNTIMEPARTIAL flag";
        }
        if (!isset($vars[0][0]) || ($vars[0][0] !== 'inline')) {
            $context['error'][] = "Do not support {{#*{$context['currentToken'][Token::POS_INNERTAG]}}}, now we only support {{#*inline \"partialName\"}}template...{{/inline}}";
        }
        if (!isset($vars[1][0])) {
            $context['error'][] = "Error in {{#*{$context['currentToken'][Token::POS_INNERTAG]}}}: inline require 1 argument for partial name!";
        }
        return true;
    }

    
    protected static function partial(&$context, $vars)
    {
        if (Parser::isSubExp($vars[0])) {
            if ($context['flags']['runpart']) {
                return $context['usedFeature']['dynpartial']++;
            } else {
                $context['error'][] = "You use dynamic partial name as '{$vars[0][2]}', this only works with option FLAG_RUNTIMEPARTIAL enabled";
                return true;
            }
        } else {
            if ($context['currentToken'][Token::POS_OP] !== '#>') {
                Partial::read($context, $vars[0][0]);
            }
        }
        if (!$context['flags']['runpart']) {
            $named = count(array_diff_key($vars, array_keys(array_keys($vars)))) > 0;
            if ($named || (count($vars) > 1)) {
                $context['error'][] = "Do not support {{>{$context['currentToken'][Token::POS_INNERTAG]}}}, you should do compile with LightnCandy::FLAG_RUNTIMEPARTIAL flag";
            }
        }

        return true;
    }

    
    protected static function spacing(&$token, &$context, $nost = false)
    {
        
        $lsp = preg_match('/^(.*)(\\r?\\n)([ \\t]*?)$/s', $token[Token::POS_LSPACE], $lmatch);
        $ind = $lsp ? $lmatch[3] : $token[Token::POS_LSPACE];
        
        $rsp = preg_match('/^([ \\t]*?)(\\r?\\n)(.*)$/s', $token[Token::POS_RSPACE], $rmatch);
        $st = true;
        
        $ahead = $context['tokens']['ahead'];
        $context['tokens']['ahead'] = preg_match('/^[^\n]*{{/s', $token[Token::POS_RSPACE] . $token[Token::POS_ROTHER]);
        
        $context['tokens']['partialind'] = '';
        
        if (!$lsp && $ahead) {
            $st = false;
        }
        if ($nost) {
            $st = false;
        }
        
        if ($token[Token::POS_LOTHER] && !$token[Token::POS_LSPACE]) {
            $st = false;
        }
        
        if ($token[Token::POS_ROTHER] && !$token[Token::POS_RSPACE]) {
            $st = false;
        }
        if ($st && (
            ($lsp && $rsp) 
                || ($rsp && !$token[Token::POS_LOTHER]) 
                || ($lsp && !$token[Token::POS_ROTHER]) 
            )) {
            
            if ($token[Token::POS_OP] === '>') {
                if (!$context['flags']['noind']) {
                    $context['tokens']['partialind'] = $token[Token::POS_LSPACECTL] ? '' : $ind;
                    $token[Token::POS_LSPACE] = (isset($lmatch[2]) ? ($lmatch[1] . $lmatch[2]) : '');
                }
            } else {
                $token[Token::POS_LSPACE] = (isset($lmatch[2]) ? ($lmatch[1] . $lmatch[2]) : '');
            }
            $token[Token::POS_RSPACE] = isset($rmatch[3]) ? $rmatch[3] : '';
        }

        
        if ($token[Token::POS_LSPACECTL]) {
            $token[Token::POS_LSPACE] = '';
        }
        if ($token[Token::POS_RSPACECTL]) {
            $token[Token::POS_RSPACE] = '';
        }
    }
}
