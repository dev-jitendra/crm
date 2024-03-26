<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class KeyUsage
{
    const MAP = [
        'type' => ASN1::TYPE_BIT_STRING,
        'mapping' => [
            'digitalSignature',
            'nonRepudiation',
            'keyEncipherment',
            'dataEncipherment',
            'keyAgreement',
            'keyCertSign',
            'cRLSign',
            'encipherOnly',
            'decipherOnly'
        ]
    ];
}
