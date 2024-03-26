<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\HeaderPartFactory;
use ZBateson\MailMimeParser\Header\Part\MimeLiteralPart;
use ArrayIterator;
use Iterator;
use NoRewindIterator;


abstract class AbstractConsumer
{
    
    protected $consumerService;

    
    protected $partFactory;

    
    public function __construct(ConsumerService $consumerService, HeaderPartFactory $partFactory)
    {
        $this->consumerService = $consumerService;
        $this->partFactory = $partFactory;
    }

    
    public static function getInstance(ConsumerService $consumerService, HeaderPartFactory $partFactory)
    {
        static $instances = [];
        $class = get_called_class();
        if (!isset($instances[$class])) {
            $instances[$class] = new static($consumerService, $partFactory);
        }
        return $instances[$class];
    }

    
    public function __invoke($value)
    {
        if ($value !== '') {
            return $this->parseRawValue($value);
        }
        return [];
    }

    
    abstract protected function getSubConsumers();

    
    protected function getAllConsumers()
    {
        $found = [$this];
        do {
            $current = current($found);
            $subConsumers = $current->getSubConsumers();
            foreach ($subConsumers as $consumer) {
                if (!in_array($consumer, $found)) {
                    $found[] = $consumer;
                }
            }
        } while (next($found) !== false);
        return $found;
    }

    
    private function parseRawValue($value)
    {
        $tokens = $this->splitRawValue($value);
        return $this->parseTokensIntoParts(new NoRewindIterator(new ArrayIterator($tokens)));
    }

    
    abstract protected function getTokenSeparators();

    
    protected function getAllTokenSeparators()
    {
        $markers = $this->getTokenSeparators();
        $subConsumers = $this->getAllConsumers();
        foreach ($subConsumers as $consumer) {
            $markers = array_merge($consumer->getTokenSeparators(), $markers);
        }
        return array_unique($markers);
    }

    
    protected function getTokenSplitPattern()
    {
        $sChars = implode('|', $this->getAllTokenSeparators());
        $mimePartPattern = MimeLiteralPart::MIME_PART_PATTERN;
        return '~(' . $mimePartPattern . '|\\\\.|' . $sChars . ')~';
    }

    
    protected function splitRawValue($rawValue)
    {
        return preg_split(
            $this->getTokenSplitPattern(),
            $rawValue,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
    }

    
    abstract protected function isStartToken($token);

    
    abstract protected function isEndToken($token);

    
    protected function getPartForToken($token, $isLiteral)
    {
        if ($isLiteral) {
            return $this->partFactory->newLiteralPart($token);
        } elseif (preg_match('/^\s+$/', $token)) {
            return $this->partFactory->newToken(' ');
        }
        return $this->partFactory->newInstance($token);
    }

    
    protected function getConsumerTokenParts(Iterator $tokens)
    {
        $token = $tokens->current();
        $subConsumers = $this->getSubConsumers();
        foreach ($subConsumers as $consumer) {
            if ($consumer->isStartToken($token)) {
                $this->advanceToNextToken($tokens, true);
                return $consumer->parseTokensIntoParts($tokens);
            }
        }
        return [$this->getPartForToken($token, false)];
    }

    
    protected function getTokenParts(Iterator $tokens)
    {
        $token = $tokens->current();
        if (strlen($token) === 2 && $token[0] === '\\') {
            return [$this->getPartForToken(substr($token, 1), true)];
        }
        return $this->getConsumerTokenParts($tokens);
    }

    
    protected function advanceToNextToken(Iterator $tokens, $isStartToken)
    {
        if (($isStartToken) || ($tokens->valid() && !$this->isEndToken($tokens->current()))) {
            $tokens->next();
        }
    }

    
    protected function parseTokensIntoParts(Iterator $tokens)
    {
        $parts = [];
        while ($tokens->valid() && !$this->isEndToken($tokens->current())) {
            $parts = array_merge($parts, $this->getTokenParts($tokens));
            $this->advanceToNextToken($tokens, false);
        }
        return $this->processParts($parts);
    }

    
    protected function processParts(array $parts)
    {
        return array_values(array_filter($parts));
    }
}
