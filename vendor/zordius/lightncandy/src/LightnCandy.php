<?php




namespace LightnCandy;


class LightnCandy extends Flags
{
    protected static $lastContext;
    public static $lastParsed;

    
    public static function compile($template, $options = array('flags' => self::FLAG_BESTPERFORMANCE))
    {
        $context = Context::create($options);

        if (static::handleError($context)) {
            return false;
        }

        $code = Compiler::compileTemplate($context, SafeString::escapeTemplate($template));
        static::$lastParsed = Compiler::$lastParsed;

        
        if (static::handleError($context)) {
            return false;
        }

        
        return Compiler::composePHPRender($context, $code);
    }

    
    public static function compilePartial($template, $options = array('flags' => self::FLAG_BESTPERFORMANCE))
    {
        $context = Context::create($options);

        if (static::handleError($context)) {
            return false;
        }

        $code = Partial::compile($context, SafeString::escapeTemplate($template));

        static::$lastParsed = Compiler::$lastParsed;

        
        if (static::handleError($context)) {
            return false;
        }

        return $code;
    }

    
    protected static function handleError(&$context)
    {
        static::$lastContext = $context;

        if (count($context['error'])) {
            if ($context['flags']['errorlog']) {
                error_log(implode("\n", $context['error']));
            }
            if ($context['flags']['exception']) {
                throw new \Exception(implode("\n", $context['error']));
            }
            return true;
        }
        return false;
    }

    
    public static function getContext()
    {
        return static::$lastContext;
    }

    
    public static function prepare($php, $tmpDir = null, $delete = true)
    {
        $php = "<?php $php ?>";

        if (!ini_get('allow_url_include') || !ini_get('allow_url_fopen')) {
            if (!is_string($tmpDir) || !is_dir($tmpDir)) {
                $tmpDir = sys_get_temp_dir();
            }
        }

        if (is_dir($tmpDir)) {
            $fn = tempnam($tmpDir, 'lci_');
            if (!$fn) {
                error_log("Can not generate tmp file under $tmpDir!!\n");
                return false;
            }
            if (!file_put_contents($fn, $php)) {
                error_log("Can not include saved temp php code from $fn, you should add $tmpDir into open_basedir!!\n");
                return false;
            }

            $phpfunc = include($fn);

            if ($delete) {
                unlink($fn);
            }

            return $phpfunc;
        }

        return include('data:
    }
}
