<?php

namespace Laminas\Mail\Transport;

use Laminas\Mail\Address;
use Laminas\Mail\Headers;
use Laminas\Mail\Message;
use Laminas\Mail\Protocol;
use Laminas\Mail\Protocol\Exception as ProtocolException;
use Laminas\ServiceManager\ServiceManager;

use function array_unique;
use function count;
use function sprintf;
use function time;


class Smtp implements TransportInterface
{
    
    protected $options;

    
    protected $envelope;

    
    protected $connection;

    
    protected $autoDisconnect = true;

    
    protected $plugins;

    
    protected $connectedTime;

    
    public function __construct(?SmtpOptions $options = null)
    {
        if (! $options instanceof SmtpOptions) {
            $options = new SmtpOptions();
        }
        $this->setOptions($options);
    }

    
    public function setOptions(SmtpOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    
    public function getOptions()
    {
        return $this->options;
    }

    
    public function setEnvelope(Envelope $envelope)
    {
        $this->envelope = $envelope;
    }

    
    public function getEnvelope()
    {
        return $this->envelope;
    }

    
    public function setPluginManager(Protocol\SmtpPluginManager $plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }

    
    public function getPluginManager()
    {
        if (null === $this->plugins) {
            $this->setPluginManager(new Protocol\SmtpPluginManager(new ServiceManager()));
        }
        return $this->plugins;
    }

    
    public function setAutoDisconnect($flag)
    {
        $this->autoDisconnect = (bool) $flag;
        return $this;
    }

    
    public function getAutoDisconnect()
    {
        return $this->autoDisconnect;
    }

    
    public function plugin($name, ?array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
    }

    
    public function __destruct()
    {
        $connection = $this->getConnection();
        if (! $connection instanceof Protocol\Smtp) {
            return;
        }

        try {
            $connection->quit();
        } catch (ProtocolException\ExceptionInterface) {
            
        }

        if ($this->autoDisconnect) {
            $connection->disconnect();
        }
    }

    
    public function setConnection(Protocol\AbstractProtocol $connection)
    {
        $this->connection = $connection;
        if (
            $connection instanceof Protocol\Smtp
            && ($this->getOptions()->getConnectionTimeLimit() !== null)
        ) {
            $connection->setUseCompleteQuit(false);
        }
    }

    
    public function getConnection()
    {
        $timeLimit = $this->getOptions()->getConnectionTimeLimit();
        if (
            $timeLimit !== null
            && $this->connectedTime !== null
            && ((time() - $this->connectedTime) > $timeLimit)
        ) {
            $this->connection = null;
        }
        return $this->connection;
    }

    
    public function disconnect()
    {
        $connection = $this->getConnection();
        if ($connection instanceof Protocol\Smtp) {
            $connection->disconnect();
            $this->connectedTime = null;
        }
    }

    
    public function send(Message $message)
    {
        
        $connection = $this->getConnection();

        if (! $connection instanceof Protocol\Smtp || ! $connection->hasSession()) {
            $connection = $this->connect();
        } else {
            
            $connection->rset();
        }

        
        $from       = $this->prepareFromAddress($message);
        $recipients = $this->prepareRecipients($message);
        $headers    = $this->prepareHeaders($message);
        $body       = $this->prepareBody($message);

        if ((count($recipients) == 0) && (! empty($headers) || ! empty($body))) {
            
            throw new Exception\RuntimeException(
                sprintf(
                    '%s transport expects at least one recipient if the message has at least one header or body',
                    self::class
                )
            );
        }

        
        $connection->mail($from);

        
        foreach ($recipients as $recipient) {
            $connection->rcpt($recipient);
        }

        
        $connection->data($headers . Headers::EOL . $body);
    }

    
    protected function prepareFromAddress(Message $message)
    {
        if ($this->getEnvelope() && $this->getEnvelope()->getFrom()) {
            return $this->getEnvelope()->getFrom();
        }

        $sender = $message->getSender();
        if ($sender instanceof Address\AddressInterface) {
            return $sender->getEmail();
        }

        $from = $message->getFrom();
        if (! count($from)) {
            
            throw new Exception\RuntimeException(sprintf(
                '%s transport expects either a Sender or at least one From address in the Message; none provided',
                self::class
            ));
        }

        $from->rewind();
        $sender = $from->current();
        return $sender->getEmail();
    }

    
    protected function prepareRecipients(Message $message)
    {
        if ($this->getEnvelope() && $this->getEnvelope()->getTo()) {
            return (array) $this->getEnvelope()->getTo();
        }

        $recipients = [];
        foreach ($message->getTo() as $address) {
            $recipients[] = $address->getEmail();
        }
        foreach ($message->getCc() as $address) {
            $recipients[] = $address->getEmail();
        }
        foreach ($message->getBcc() as $address) {
            $recipients[] = $address->getEmail();
        }

        $recipients = array_unique($recipients);
        return $recipients;
    }

    
    protected function prepareHeaders(Message $message)
    {
        $headers = clone $message->getHeaders();
        $headers->removeHeader('Bcc');
        return $headers->toString();
    }

    
    protected function prepareBody(Message $message)
    {
        return $message->getBodyText();
    }

    
    protected function lazyLoadConnection()
    {
        
        $options        = $this->getOptions();
        $config         = $options->getConnectionConfig();
        $config['host'] = $options->getHost();
        $config['port'] = $options->getPort();

        $this->setConnection($this->plugin($options->getConnectionClass(), $config));

        return $this->connect();
    }

    
    protected function connect()
    {
        if (! $this->connection instanceof Protocol\Smtp) {
            return $this->lazyLoadConnection();
        }

        $this->connection->connect();

        $this->connectedTime = time();

        $this->connection->helo($this->getOptions()->getName());

        return $this->connection;
    }
}
