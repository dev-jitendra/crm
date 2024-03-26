<?php

namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\MimeLiteralPartFactory;


class HeaderFactory
{
    
    protected $consumerService;

    
    protected $mimeLiteralPartFactory;
    
    
    protected $types = [
        'ZBateson\MailMimeParser\Header\AddressHeader' => [
            'from',
            'to',
            'cc',
            'bcc',
            'sender',
            'replyto',
            'resentfrom',
            'resentto',
            'resentcc',
            'resentbcc',
            'resentreplyto',
            'returnpath',
            'deliveredto',
        ],
        'ZBateson\MailMimeParser\Header\DateHeader' => [
            'date',
            'resentdate',
            'deliverydate',
            'expires',
            'expirydate',
            'replyby',
        ],
        'ZBateson\MailMimeParser\Header\ParameterHeader' => [
            'contenttype',
            'contentdisposition',
        ],
        'ZBateson\MailMimeParser\Header\SubjectHeader' => [
            'subject',
        ],
        'ZBateson\MailMimeParser\Header\IdHeader' => [
            'messageid',
            'contentid',
            'inreplyto',
            'references'
        ],
        'ZBateson\MailMimeParser\Header\ReceivedHeader' => [
            'received'
        ]
    ];
    
    
    protected $genericType = 'ZBateson\MailMimeParser\Header\GenericHeader';
    
    
    public function __construct(ConsumerService $consumerService, MimeLiteralPartFactory $mimeLiteralPartFactory)
    {
        $this->consumerService = $consumerService;
        $this->mimeLiteralPartFactory = $mimeLiteralPartFactory;
    }

    
    public function getNormalizedHeaderName($header)
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($header));
    }
    
    
    private function getClassFor($name)
    {
        $test = $this->getNormalizedHeaderName($name);
        foreach ($this->types as $class => $matchers) {
            foreach ($matchers as $matcher) {
                if ($test === $matcher) {
                    return $class;
                }
            }
        }
        return $this->genericType;
    }
    
    
    public function newInstance($name, $value)
    {
        $class = $this->getClassFor($name);
        if (is_a($class, 'ZBateson\MailMimeParser\Header\MimeEncodedHeader', true)) {
            return new $class(
                $this->mimeLiteralPartFactory,
                $this->consumerService,
                $name,
                $value
            );
        }
        return new $class($this->consumerService, $name, $value);
    }

    
    public function newHeaderContainer()
    {
        return new HeaderContainer($this);
    }
}
