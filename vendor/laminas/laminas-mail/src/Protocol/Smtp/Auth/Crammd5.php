<?php

namespace Laminas\Mail\Protocol\Smtp\Auth;

use Laminas\Mail\Exception\InvalidArgumentException;
use Laminas\Mail\Protocol\Smtp;

use function array_replace_recursive;
use function base64_decode;
use function base64_encode;
use function hash_hmac;
use function is_array;
use function is_string;


class Crammd5 extends Smtp
{
    
    protected $username;

    
    protected $password;

    
    public function __construct($host = '127.0.0.1', $port = null, $config = null)
    {
        
        $config     = $config ?? [];
        $origConfig = $config;
        if (is_array($host)) {
            
            $config = array_replace_recursive($host, $config);
        }

        if (isset($config['username'])) {
            $this->setUsername($config['username']);
        }
        if (isset($config['password'])) {
            $this->setPassword($config['password']);
        }

        
        parent::__construct($host, $port, $origConfig);
    }

    
    public function auth()
    {
        
        parent::auth();

        $this->_send('AUTH CRAM-MD5');
        $challenge = $this->_expect(334);
        $challenge = base64_decode($challenge);
        $digest    = $this->hmacMd5($this->getPassword(), $challenge);
        $this->_send(base64_encode($this->getUsername() . ' ' . $digest));
        $this->_expect(235);
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

    
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    
    public function getPassword()
    {
        return $this->password;
    }

    
    protected function hmacMd5($key, $data,  $block = 64)
    {
        if (! is_string($key) || $key === '') {
            throw new InvalidArgumentException('CramMD5 authentication requires a non-empty password');
        }

        if (! is_string($data) || $data === '') {
            throw new InvalidArgumentException('CramMD5 authentication requires a non-empty challenge');
        }

        return hash_hmac('md5', $data, $key, false);
    }
}
