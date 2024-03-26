<?php

namespace ZBateson\MailMimeParser;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\HeaderFactory;
use ZBateson\MailMimeParser\Header\Part\HeaderPartFactory;
use ZBateson\MailMimeParser\Header\Part\MimeLiteralPartFactory;
use ZBateson\MailMimeParser\Message\Helper\MessageHelperService;
use ZBateson\MailMimeParser\Message\MessageParser;
use ZBateson\MailMimeParser\Message\Part\Factory\PartBuilderFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartFactoryService;
use ZBateson\MailMimeParser\Message\Part\Factory\PartStreamFilterManagerFactory;
use ZBateson\MbWrapper\MbWrapper;


class Container
{
    
    protected $partBuilderFactory;
    
    
    protected $partFactoryService;
    
    
    protected $partFilterFactory;
    
    
    protected $partStreamFilterManagerFactory;
    
    
    protected $headerFactory;
    
    
    protected $headerPartFactory;
    
    
    protected $mimeLiteralPartFactory;
    
    
    protected $consumerService;
    
    
    protected $messageHelperService;

    
    protected $streamFactory;
    
    
    public function __construct()
    {
    }

    
    protected function getInstance($var, $class)
    {
        if ($this->$var === null) {
            $this->$var = new $class();
        }
        return $this->$var;
    }
    
    
    public function newMessageParser()
    {
        return new MessageParser(
            $this->getPartFactoryService(),
            $this->getPartBuilderFactory()
        );
    }
    
    
    public function getMessageHelperService()
    {
        if ($this->messageHelperService === null) {
            $this->messageHelperService = new MessageHelperService(
                $this->getPartBuilderFactory()
            );
            $this->messageHelperService->setPartFactoryService(
                $this->getPartFactoryService()
            );
        }
        return $this->messageHelperService;
    }

    
    public function getPartFilterFactory()
    {
        return $this->getInstance(
            'partFilterFactory',
            __NAMESPACE__ . '\Message\PartFilterFactory'
        );
    }
    
    
    public function getPartFactoryService()
    {
        if ($this->partFactoryService === null) {
            $this->partFactoryService = new PartFactoryService(
                $this->getPartFilterFactory(),
                $this->getStreamFactory(),
                $this->getPartStreamFilterManagerFactory(),
                $this->getMessageHelperService()
            );
        }
        return $this->partFactoryService;
    }

    
    public function getPartBuilderFactory()
    {
        if ($this->partBuilderFactory === null) {
            $this->partBuilderFactory = new PartBuilderFactory(
                $this->getHeaderFactory()
            );
        }
        return $this->partBuilderFactory;
    }
    
    
    public function getHeaderFactory()
    {
        if ($this->headerFactory === null) {
            $this->headerFactory = new HeaderFactory(
                $this->getConsumerService(),
                $this->getMimeLiteralPartFactory()
            );
        }
        return $this->headerFactory;
    }

    
    public function getStreamFactory()
    {
        return $this->getInstance(
            'streamFactory',
            __NAMESPACE__ . '\Stream\StreamFactory'
        );
    }

    
    public function getPartStreamFilterManagerFactory()
    {
        if ($this->partStreamFilterManagerFactory === null) {
            $this->partStreamFilterManagerFactory = new PartStreamFilterManagerFactory(
                $this->getStreamFactory()
            );
        }
        return $this->getInstance(
            'partStreamFilterManagerFactory',
            __NAMESPACE__ . '\Message\Part\PartStreamFilterManagerFactory'
        );
    }

    
    public function getCharsetConverter()
    {
        return new MbWrapper();
    }
    
    
    public function getHeaderPartFactory()
    {
        if ($this->headerPartFactory === null) {
            $this->headerPartFactory = new HeaderPartFactory($this->getCharsetConverter());
        }
        return $this->headerPartFactory;
    }
    
    
    public function getMimeLiteralPartFactory()
    {
        if ($this->mimeLiteralPartFactory === null) {
            $this->mimeLiteralPartFactory = new MimeLiteralPartFactory($this->getCharsetConverter());
        }
        return $this->mimeLiteralPartFactory;
    }
    
    
    public function getConsumerService()
    {
        if ($this->consumerService === null) {
            $this->consumerService = new ConsumerService(
                $this->getHeaderPartFactory(),
                $this->getMimeLiteralPartFactory()
            );
        }
        return $this->consumerService;
    }
    
}
