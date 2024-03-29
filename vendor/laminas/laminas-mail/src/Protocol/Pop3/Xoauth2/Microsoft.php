<?php

namespace Laminas\Mail\Protocol\Pop3\Xoauth2;

use Laminas\Mail\Protocol\Exception\RuntimeException;
use Laminas\Mail\Protocol\Pop3;
use Laminas\Mail\Protocol\Xoauth2\Xoauth2;


class Microsoft extends Pop3
{
    protected const AUTH_INITIALIZE_REQUEST      = 'AUTH XOAUTH2';
    protected const AUTH_RESPONSE_INITIALIZED_OK = '+';

    
    public function login($user, $password, $tryApop = true): void
    {
        $this->sendRequest(self::AUTH_INITIALIZE_REQUEST);

        $response = $this->readRemoteResponse();

        if ($response->status() != self::AUTH_RESPONSE_INITIALIZED_OK) {
            throw new RuntimeException($response->message());
        }

        $this->request(Xoauth2::encodeXoauth2Sasl($user, $password));
    }
}
