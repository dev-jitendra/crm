<?php

namespace Laminas\Mail\Protocol;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;

use function gettype;
use function is_object;
use function sprintf;


class SmtpPluginManager extends AbstractPluginManager
{
    
    protected $aliases = [
        'crammd5' => Smtp\Auth\Crammd5::class,
        'cramMd5' => Smtp\Auth\Crammd5::class,
        'CramMd5' => Smtp\Auth\Crammd5::class,
        'cramMD5' => Smtp\Auth\Crammd5::class,
        'CramMD5' => Smtp\Auth\Crammd5::class,
        'login'   => Smtp\Auth\Login::class,
        'Login'   => Smtp\Auth\Login::class,
        'plain'   => Smtp\Auth\Plain::class,
        'Plain'   => Smtp\Auth\Plain::class,
        'xoauth2' => Smtp\Auth\Xoauth2::class,
        'Xoauth2' => Smtp\Auth\Xoauth2::class,
        'smtp'    => Smtp::class,
        'Smtp'    => Smtp::class,
        'SMTP'    => Smtp::class,
        
        'Zend\Mail\Protocol\Smtp\Auth\Crammd5' => Smtp\Auth\Crammd5::class,
        'Zend\Mail\Protocol\Smtp\Auth\Login'   => Smtp\Auth\Login::class,
        'Zend\Mail\Protocol\Smtp\Auth\Plain'   => Smtp\Auth\Plain::class,
        'Zend\Mail\Protocol\Smtp'              => Smtp::class,
        
        'zendmailprotocolsmtpauthcrammd5'    => Smtp\Auth\Crammd5::class,
        'zendmailprotocolsmtpauthlogin'      => Smtp\Auth\Login::class,
        'zendmailprotocolsmtpauthplain'      => Smtp\Auth\Plain::class,
        'zendmailprotocolsmtp'               => Smtp::class,
        'laminasmailprotocolsmtpauthcrammd5' => Smtp\Auth\Crammd5::class,
        'laminasmailprotocolsmtpauthlogin'   => Smtp\Auth\Login::class,
        'laminasmailprotocolsmtpauthplain'   => Smtp\Auth\Plain::class,
        'laminasmailprotocolsmtp'            => Smtp::class,
    ];

    
    protected $factories = [
        Smtp\Auth\Crammd5::class => InvokableFactory::class,
        Smtp\Auth\Login::class   => InvokableFactory::class,
        Smtp\Auth\Plain::class   => InvokableFactory::class,
        Smtp\Auth\Xoauth2::class => InvokableFactory::class,
        Smtp::class              => InvokableFactory::class,
    ];

    
    protected $instanceOf = Smtp::class;

    
    public function validate(mixed $instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Plugin of type %s is invalid; must extend %s',
                is_object($instance) ? $instance::class : gettype($instance),
                $this->instanceOf
            ));
        }
    }

    
    public function validatePlugin(mixed $plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new Exception\InvalidArgumentException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
