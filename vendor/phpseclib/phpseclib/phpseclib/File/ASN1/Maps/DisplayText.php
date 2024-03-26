<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class DisplayText
{
    const MAP = [
        'type' => ASN1::TYPE_CHOICE,
        'children' => [
            'ia5String' => ['type' => ASN1::TYPE_IA5_STRING],
            'visibleString' => ['type' => ASN1::TYPE_VISIBLE_STRING],
            'bmpString' => ['type' => ASN1::TYPE_BMP_STRING],
            'utf8String' => ['type' => ASN1::TYPE_UTF8_STRING]
        ]
    ];
}
