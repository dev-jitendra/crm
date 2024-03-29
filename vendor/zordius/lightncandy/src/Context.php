<?php




namespace LightnCandy;


class Context extends Flags
{
    
    public static function create($options)
    {
        if (!is_array($options)) {
            $options = array();
        }

        $flags = isset($options['flags']) ? $options['flags'] : static::FLAG_BESTPERFORMANCE;

        $context = array(
            'flags' => array(
                'errorlog' => $flags & static::FLAG_ERROR_LOG,
                'exception' => $flags & static::FLAG_ERROR_EXCEPTION,
                'skippartial' => $flags & static::FLAG_ERROR_SKIPPARTIAL,
                'standalone' => $flags & static::FLAG_STANDALONEPHP,
                'noesc' => $flags & static::FLAG_NOESCAPE,
                'jstrue' => $flags & static::FLAG_JSTRUE,
                'jsobj' => $flags & static::FLAG_JSOBJECT,
                'jslen' => $flags & static::FLAG_JSLENGTH,
                'hbesc' => $flags & static::FLAG_HBESCAPE,
                'this' => $flags & static::FLAG_THIS,
                'nohbh' => $flags & static::FLAG_NOHBHELPERS,
                'parent' => $flags & static::FLAG_PARENT,
                'echo' => $flags & static::FLAG_ECHO,
                'advar' => $flags & static::FLAG_ADVARNAME,
                'namev' => $flags & static::FLAG_NAMEDARG,
                'spvar' => $flags & static::FLAG_SPVARS,
                'slash' => $flags & static::FLAG_SLASH,
                'else' => $flags & static::FLAG_ELSE,
                'exhlp' => $flags & static::FLAG_EXTHELPER,
                'lambda' => $flags & static::FLAG_HANDLEBARSLAMBDA,
                'mustlok' => $flags & static::FLAG_MUSTACHELOOKUP,
                'mustlam' => $flags & static::FLAG_MUSTACHELAMBDA,
                'mustsec' => $flags & static::FLAG_MUSTACHESECTION,
                'noind' => $flags & static::FLAG_PREVENTINDENT,
                'debug' => $flags & static::FLAG_RENDER_DEBUG,
                'prop' => $flags & static::FLAG_PROPERTY,
                'method' => $flags & static::FLAG_METHOD,
                'runpart' => $flags & static::FLAG_RUNTIMEPARTIAL,
                'rawblock' => $flags & static::FLAG_RAWBLOCK,
                'partnc' => $flags & static::FLAG_PARTIALNEWCONTEXT,
                'nostd' => $flags & static::FLAG_IGNORESTANDALONE,
                'strpar' => $flags & static::FLAG_STRINGPARAMS,
                'knohlp' => $flags & static::FLAG_KNOWNHELPERSONLY,
            ),
            'delimiters' => array(
                isset($options['delimiters'][0]) ? $options['delimiters'][0] : '{{',
                isset($options['delimiters'][1]) ? $options['delimiters'][1] : '}}',
            ),
            'level' => 0,
            'stack' => array(),
            'currentToken' => null,
            'error' => array(),
            'elselvl' => array(),
            'elsechain' => false,
            'tokens' => array(
                'standalone' => true,
                'ahead' => false,
                'current' => 0,
                'count' => 0,
                'partialind' => '',
            ),
            'usedPartial' => array(),
            'partialStack' => array(),
            'partialCode' => array(),
            'usedFeature' => array(
                'rootthis' => 0,
                'enc' => 0,
                'raw' => 0,
                'sec' => 0,
                'isec' => 0,
                'if' => 0,
                'else' => 0,
                'unless' => 0,
                'each' => 0,
                'this' => 0,
                'parent' => 0,
                'with' => 0,
                'comment' => 0,
                'partial' => 0,
                'dynpartial' => 0,
                'inlpartial' => 0,
                'helper' => 0,
                'delimiter' => 0,
                'subexp' => 0,
                'rawblock' => 0,
                'pblock' => 0,
                'lookup' => 0,
                'log' => 0,
            ),
            'usedCount' => array(
                'var' => array(),
                'helpers' => array(),
                'runtime' => array(),
            ),
            'compile' => false,
            'parsed' => array(),
            'partials' => (isset($options['partials']) && is_array($options['partials'])) ? $options['partials'] : array(),
            'partialblock' => array(),
            'inlinepartial' => array(),
            'helpers' => array(),
            'renderex' => isset($options['renderex']) ? $options['renderex'] : '',
            'prepartial' => (isset($options['prepartial']) && is_callable($options['prepartial'])) ? $options['prepartial'] : false,
            'helperresolver' => (isset($options['helperresolver']) && is_callable($options['helperresolver'])) ? $options['helperresolver'] : false,
            'partialresolver' => (isset($options['partialresolver']) && is_callable($options['partialresolver'])) ? $options['partialresolver'] : false,
            'runtime' => isset($options['runtime']) ? $options['runtime'] : '\\LightnCandy\\Runtime',
            'runtimealias' => 'LR',
            'safestring' => '\\LightnCandy\\SafeString',
            'safestringalias' => isset($options['safestring']) ? $options['safestring'] : 'LS',
            'rawblock' => false,
            'funcprefix' => uniqid('lcr'),
        );

        $context['ops'] = $context['flags']['echo'] ? array(
            'seperator' => ',',
            'f_start' => 'echo ',
            'f_end' => ';',
            'op_start' => 'ob_start();echo ',
            'op_end' => ';return ob_get_clean();',
            'cnd_start' => ';if ',
            'cnd_then' => '{echo ',
            'cnd_else' => ';}else{echo ',
            'cnd_end' => ';}echo ',
            'cnd_nend' => ';}',
        ) : array(
            'seperator' => '.',
            'f_start' => 'return ',
            'f_end' => ';',
            'op_start' => 'return ',
            'op_end' => ';',
            'cnd_start' => '.(',
            'cnd_then' => ' ? ',
            'cnd_else' => ' : ',
            'cnd_end' => ').',
            'cnd_nend' => ')',
        );

        $context['ops']['enc'] = $context['flags']['hbesc'] ? 'encq' : 'enc';
        $context['ops']['array_check'] = '$inary=is_array($in);';
        static::updateHelperTable($context, $options);

        if ($context['flags']['partnc'] && ($context['flags']['runpart'] == 0)) {
            $context['error'][] = 'The FLAG_PARTIALNEWCONTEXT requires FLAG_RUNTIMEPARTIAL! Fix your compile options please';
        }

        return $context;
    }

    
    protected static function updateHelperTable(&$context, $options, $tname = 'helpers')
    {
        if (isset($options[$tname]) && is_array($options[$tname])) {
            foreach ($options[$tname] as $name => $func) {
                $tn = is_int($name) ? $func : $name;
                if (is_callable($func)) {
                    $context[$tname][$tn] = $func;
                } else {
                    if (is_array($func)) {
                        $context['error'][] = "I found an array in $tname with key as $name, please fix it.";
                    } else {
                        if ($context['flags']['exhlp']) {
                            
                            $context[$tname][$tn] = 1;
                        } else {
                            $context['error'][] = "You provide a custom helper named as '$tn' in options['$tname'], but the function $func() is not defined!";
                        }
                    }
                }
            }
        }
        return $context;
    }

    
    public static function merge(&$context, $tmp)
    {
        $context['error'] = $tmp['error'];
        $context['helpers'] = $tmp['helpers'];
        $context['partials'] = $tmp['partials'];
        $context['partialCode'] = $tmp['partialCode'];
        $context['partialStack'] = $tmp['partialStack'];
        $context['usedCount'] = $tmp['usedCount'];
        $context['usedFeature'] = $tmp['usedFeature'];
        $context['usedPartial'] = $tmp['usedPartial'];
    }
}
