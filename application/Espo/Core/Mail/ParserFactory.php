<?php


namespace Espo\Core\Mail;

use Espo\Core\InjectableFactory;
use Espo\Core\Mail\Parsers\MailMimeParser;

class ParserFactory
{
    protected const DEFAULT_PARSER_CLASS_NAME = MailMimeParser::class;

    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): Parser
    {
        return $this->injectableFactory->create(self::DEFAULT_PARSER_CLASS_NAME);
    }
}
