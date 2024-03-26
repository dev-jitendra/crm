<?php

namespace ZBateson\MailMimeParser\Message\Helper;

use ZBateson\MailMimeParser\Message\Part\Factory\PartBuilderFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartFactoryService;


class MessageHelperService
{
    
    private $partBuilderFactory;

    
    private $genericHelper;

    
    private $multipartHelper;

    
    private $privacyHelper;

    
    private $partFactoryService;

    
    public function __construct(PartBuilderFactory $partBuilderFactory)
    {
        $this->partBuilderFactory = $partBuilderFactory;
    }

    
    public function setPartFactoryService(PartFactoryService $partFactoryService)
    {
        $this->partFactoryService = $partFactoryService;
    }

    
    public function getGenericHelper()
    {
        if ($this->genericHelper === null) {
            $this->genericHelper = new GenericHelper(
                $this->partFactoryService->getMimePartFactory(),
                $this->partFactoryService->getUUEncodedPartFactory(),
                $this->partBuilderFactory
            );
        }
        return $this->genericHelper;
    }

    
    public function getMultipartHelper()
    {
        if ($this->multipartHelper === null) {
            $this->multipartHelper = new MultipartHelper(
                $this->partFactoryService->getMimePartFactory(),
                $this->partFactoryService->getUUEncodedPartFactory(),
                $this->partBuilderFactory,
                $this->getGenericHelper()
            );
        }
        return $this->multipartHelper;
    }

    
    public function getPrivacyHelper()
    {
        if ($this->privacyHelper === null) {
            $this->privacyHelper = new PrivacyHelper(
                $this->partFactoryService->getMimePartFactory(),
                $this->partFactoryService->getUUEncodedPartFactory(),
                $this->partBuilderFactory,
                $this->getGenericHelper(),
                $this->getMultipartHelper()
            );
        }
        return $this->privacyHelper;
    }
}
