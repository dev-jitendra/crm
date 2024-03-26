<?php

namespace Laminas\Mail\Protocol\Smtp\Auth;

use Laminas\Mail\Protocol\Smtp;

use function array_replace_recursive;
use function base64_encode;
use function is_array;


class Plain extends Smtp
{
    
    protected $username;

    
    protected $password;

    
    public function __construct($host = '127.0.0.1', $port = null, $config = null)
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
                $this->setUsername($config['username']);
            }
            if (isset($config['password'])) {
                $this->setPassword($config['password']);
            }
        }

        
        parent::__construct($host, $port, $origConfig);
    }

    
    public function auth()
    {
        
        parent::auth();

        $this->_send('AUTH PLAIN');
        $this->_expect(334);
        $this->_send(base64_encode("\0" . $this->getUsername() . "\0" . $this->getPassword()));
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
}
