<?php

namespace ZBateson\MailMimeParser\Message\Part\Factory;

use ReflectionClass;
use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message\Helper\MessageHelperService;
use ZBateson\MailMimeParser\Message\PartFilterFactory;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;
use ZBateson\MailMimeParser\Stream\StreamFactory;


abstract class MessagePartFactory
{
    
    protected $partStreamFilterManagerFactory;

    
    protected $streamFactory;

    
    private static $instances = null;

    
    public function __construct(
        StreamFactory $streamFactory,
        PartStreamFilterManagerFactory $psf
    ) {
        $this->streamFactory = $streamFactory;
        $this->partStreamFilterManagerFactory = $psf;
    }
    
    
    protected static function setCachedInstance(MessagePartFactory $instance)
    {
        if (self::$instances === null) {
            self::$instances = [];
        }
        $class = get_called_class();
        self::$instances[$class] = $instance;
    }

    
    protected static function getCachedInstance()
    {
        $class = get_called_class();
        if (self::$instances === null || !isset(self::$instances[$class])) {
            return null;
        }
        return self::$instances[$class];
    }

    
    public static function getInstance(
        StreamFactory $sdf,
        PartStreamFilterManagerFactory $psf,
        PartFilterFactory $pf = null,
        MessageHelperService $mhs = null
    ) {
        $instance = static::getCachedInstance();
        if ($instance === null) {
            $ref = new ReflectionClass(get_called_class());
            $n = $ref->getConstructor()->getNumberOfParameters();
            $args = [];
            for ($i = 0; $i < $n; ++$i) {
                $args[] = func_get_arg($i);
            }
            $instance = $ref->newInstanceArgs($args);
            static::setCachedInstance($instance);
        }
        return $instance;
    }

    
    public abstract function newInstance(PartBuilder $partBuilder, StreamInterface $messageStream = null);
}
