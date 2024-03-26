<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class RevokedCertificate
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'userCertificate' => CertificateSerialNumber::MAP,
            'revocationDate' => Time::MAP,
            'crlEntryExtensions' => [
                'optional' => true
            ] + Extensions::MAP
        ]
    ];
}
