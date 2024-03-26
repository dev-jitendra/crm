<?php

namespace Laminas\Mail\Protocol\Smtp\Auth;

use Laminas\Mail\Protocol\Smtp;
use Laminas\Mail\Protocol\Xoauth2\Xoauth2 as Xoauth2AuthEncoder;

use function array_replace_recursive;
use function is_array;


final class Xoauth2 extends Smtp
{
    
    protected $username;

    
    protected $accessToken;

    
    public function __construct($host = '127.0.0.1', $port = null, ?array $config = null)
    {
        
        $origConfig = $config;
        if (is_array($host)) {
            
            if (is_array($config)) {
                $config = array_replace_recursive($host, $config);
            } else {
                $config = $host;
            }
        }

        if (is_array($config)) {
            if (isset($config['username'])) {
                $this->setUsername((string) $config['username']);
            }
            if (isset($config['access_token'])) {
                $this->setAccessToken((string) $config['access_token']);
            }
        }

        
        parent::__construct($host, $port, $origConfig);
    }

    
    public function auth()
    {
        
        parent::auth();

        $this->_send('AUTH XOAUTH2');
        $this->_expect('334');
        $this->_send(Xoauth2AuthEncoder::encodeXoauth2Sasl($this->getUsername(), $this->getAccessToken()));
        $this->_expect('235');
        $this->auth = true;
    }

    
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    
    public function getUsername()
    {
        return $this->username;
    }

    
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
        return $this;
    }

    
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
