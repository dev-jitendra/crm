<?php declare(strict_types=1);



namespace Monolog\Handler;

use Elastica\Document;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\ElasticaFormatter;
use Monolog\Level;
use Elastica\Client;
use Elastica\Exception\ExceptionInterface;
use Monolog\LogRecord;


class ElasticaHandler extends AbstractProcessingHandler
{
    protected Client $client;

    
    protected array $options;

    
    public function __construct(Client $client, array $options = [], int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->options = array_merge(
            [
                'index'          => 'monolog',      
                'type'           => 'record',       
                'ignore_error'   => false,          
            ],
            $options
        );
    }

    
    protected function write(LogRecord $record): void
    {
        $this->bulkSend([$record->formatted]);
    }

    
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        if ($formatter instanceof ElasticaFormatter) {
            return parent::setFormatter($formatter);
        }

        throw new \InvalidArgumentException('ElasticaHandler is only compatible with ElasticaFormatter');
    }

    
    public function getOptions(): array
    {
        return $this->options;
    }

    
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new ElasticaFormatter($this->options['index'], $this->options['type']);
    }

    
    public function handleBatch(array $records): void
    {
        $documents = $this->getFormatter()->formatBatch($records);
        $this->bulkSend($documents);
    }

    
    protected function bulkSend(array $documents): void
    {
        try {
            $this->client->addDocuments($documents);
        } catch (ExceptionInterface $e) {
            if (!$this->options['ignore_error']) {
                throw new \RuntimeException("Error sending messages to Elasticsearch", 0, $e);
            }
        }
    }
}
