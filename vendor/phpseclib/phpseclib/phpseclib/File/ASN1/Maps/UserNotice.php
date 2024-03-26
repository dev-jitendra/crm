<?php



namespace phpseclib3\File\ASN1\Maps;

use phpseclib3\File\ASN1;


abstract class UserNotice
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'noticeRef' => [
                'optional' => true,
                'implicit' => true
            ] + NoticeReference::MAP,
            'explicitText' => [
                'optional' => true,
                'implicit' => true
            ] + DisplayText::MAP
        ]
    ];
}
