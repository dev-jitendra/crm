<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class Time
{
    const MAP = [
        'type' => ASN1::TYPE_CHOICE,
        'children' => [
            'utcTime' => ['type' => ASN1::TYPE_UTC_TIME],
            'generalTime' => ['type' => ASN1::TYPE_GENERALIZED_TIME]
        ]
    ];
}
