<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class CertificationRequest
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'certificationRequestInfo' => CertificationRequestInfo::MAP,
            'signatureAlgorithm' => AlgorithmIdentifier::MAP,
            'signature' => ['type' => ASN1::TYPE_BIT_STRING]
        ]
    ];
}
