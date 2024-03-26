<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class netscape_cert_type
{
    const MAP = [
        'type' => ASN1::TYPE_BIT_STRING,
        'mapping' => [
            'SSLClient',
            'SSLServer',
            'Email',
            'ObjectSigning',
            'Reserved',
            'SSLCA',
            'EmailCA',
            'ObjectSigningCA'
        ]
    ];
}
