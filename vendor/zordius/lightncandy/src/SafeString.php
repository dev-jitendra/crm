<?php




namespace LightnCandy;


class SafeString extends Encoder
{
    const EXTENDED_COMMENT_SEARCH = '/{{!--.*?--}}/s';
    const IS_SUBEXP_SEARCH = '/^\(.+\)$/s';
    const IS_BLOCKPARAM_SEARCH = '/^ +\|(.+)\|$/s';

    private $string;

    public static $jsContext = array(
        'flags' => array(
            'jstrue' => 1,
            'jsobj' => 1,
        )
    );

    
    public function __construct($str, $escape = false)
    {
        $this->string = $escape ? (($escape === 'encq') ? static::encq(static::$jsContext, $str) : static::enc(static::$jsContext, $str)) : $str;
    }

    public function __toString()
    {
        return $this->string;
    }

    
    public static function stripExtendedComments($template)
    {
        return preg_replace(static::EXTENDED_COMMENT_SEARCH, '{{! }}', $template);
    }

    
    public static function escapeTemplate($template)
    {
        return addcslashes(addcslashes($template, '\\'), "'");
    }
}
