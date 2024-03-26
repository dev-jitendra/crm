<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class PBMAC1params
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'keyDerivationFunc' => AlgorithmIdentifier::MAP,
            'messageAuthScheme' => AlgorithmIdentifier::MAP
        ]
    ];
}
