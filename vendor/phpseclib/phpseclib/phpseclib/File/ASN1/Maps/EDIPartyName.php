<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class EDIPartyName
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'nameAssigner' => [
                'constant' => 0,
                'optional' => true,
                'implicit' => true
            ] + DirectoryString::MAP,
            
            
            'partyName' => [
                'constant' => 1,
                'optional' => true,
                'implicit' => true
            ] + DirectoryString::MAP
        ]
    ];
}
