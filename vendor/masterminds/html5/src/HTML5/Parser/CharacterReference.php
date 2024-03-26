<?php

namespace Masterminds\HTML5\Parser;

use Masterminds\HTML5\Entities;


class CharacterReference
{
    protected static $numeric_mask = array(
        0x0,
        0x2FFFF,
        0,
        0xFFFF,
    );

    
    public static function lookupName($name)
    {
        
        return isset(Entities::$byName[$name]) ? Entities::$byName[$name] : null;
    }

    
    public static function lookupDecimal($int)
    {
        $entity = '&#' . $int . ';';

        
        
        return mb_decode_numericentity($entity, static::$numeric_mask, 'utf-8');
    }

    
    public static function lookupHex($hexdec)
    {
        return static::lookupDecimal(hexdec($hexdec));
    }
}
