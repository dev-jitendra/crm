<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class NameConstraints
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'permittedSubtrees' => [
                'constant' => 0,
                'optional' => true,
                'implicit' => true
            ] + GeneralSubtrees::MAP,
            'excludedSubtrees' => [
                'constant' => 1,
                'optional' => true,
                'implicit' => true
            ] + GeneralSubtrees::MAP
        ]
    ];
}
