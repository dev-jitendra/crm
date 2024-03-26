<?php

namespace Laminas\Mail\Storage;

use Laminas\Mail\Exception as MailException;
use Laminas\Mail\Protocol;
use Laminas\Mail\Protocol\Exception\RuntimeException;
use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Exception\InvalidArgumentException;
use Laminas\Mail\Storage\Message;
use Laminas\Mime;

use function array_combine;
use function array_key_exists;
use function is_string;
use function range;
use function strtolower;

class Pop3 extends AbstractStorage
{
    
    protected $protocol;

    
    public function countMessages()
    {
        $count  = 0; 
        $octets = 0; 
        $this->protocol->status($count, $octets);
        return (int) $count;
    }

    
    public function getSize($id = 0)
    {
        $id = $id ?: null;
        return $this->protocol->getList($id);
    }

    
    public function getMessage($id)
    {
        $bodyLines = 0;
        $message   = $this->protocol->top($id, $bodyLines, true);

        return new $this->messageClass([
            'handler'    => $this,
            'id'         => $id,
            'headers'    => $message,
            'noToplines' => $bodyLines < 1,
        ]);
    }

    
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }

        return $this->protocol->top($id, 0, true);
    }

    
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }

        $content = $this->protocol->retrieve($id);
        
        $headers = null; 
        $body    = null; 
        Mime\Decode::splitMessage($content, $headers, $body);
        return $body;
    }

    
    public function __construct($params)
    {
        $this->has['fetchPart'] = false;
        $this->has['top']       = null;
        $this->has['uniqueid']  = null;

        if ($params instanceof Protocol\Pop3) {
            $this->protocol = $params;
            return;
        }

        $params = ParamsNormalizer::normalizeParams($params);

        if (! isset($params['user'])) {
            throw new InvalidArgumentException('need at least user in params');
        }

        $host     = $params['host'] ?? 'localhost';
        $password = $params['password'] ?? '';
        $port     = $params['port'] ?? null;
        $ssl      = $params['ssl'] ?? false;

        if (null !== $port) {
            $port = (int) $port;
        }

        if (! is_string($ssl)) {
            $ssl = (bool) $ssl;
        }

        $this->protocol = new Protocol\Pop3();

        if (array_key_exists('novalidatecert', $params)) {
            $this->protocol->setNoValidateCert((bool) $params['novalidatecert']);
        }

        $this->protocol->connect((string) $host, $port, $ssl);
        $this->protocol->login((string) $params['user'], (string) $password);
    }

    
    public function close()
    {
        $this->protocol->logout();
    }

    
    public function noop()
    {
        $this->protocol->noop();
    }

    
    public function removeMessage($id)
    {
        $this->protocol->delete($id);
    }

    
    public function getUniqueId($id = null)
    {
        if (! $this->hasUniqueid) {
            if ($id) {
                return $id;
            }
            $count = $this->countMessages();
            if ($count < 1) {
                return [];
            }
            $range = range(1, $count);
            return array_combine($range, $range);
        }

        return $this->protocol->uniqueid($id);
    }

    
    public function getNumberByUniqueId($id)
    {
        if (! $this->hasUniqueid) {
            return $id;
        }

        $ids = $this->getUniqueId();
        foreach ($ids as $k => $v) {
            if ($v == $id) {
                return $k;
            }
        }

        throw new InvalidArgumentException('unique id not found');
    }

    
    public function __get($var)
    {
        $result = parent::__get($var);
        if ($result !== null) {
            return $result;
        }

        if (strtolower($var) == 'hastop') {
            if ($this->protocol->hasTop === null) {
                
                try {
                    $this->protocol->top(1, 0, false);
                } catch (MailException\ExceptionInterface) {
                    
                }
            }
            $this->has['top'] = $this->protocol->hasTop;
            return $this->protocol->hasTop;
        }

        if (strtolower($var) == 'hasuniqueid') {
            $id = null;
            try {
                $id = $this->protocol->uniqueid(1);
            } catch (MailException\ExceptionInterface) {
                
            }
            $this->has['uniqueid'] = (bool) $id;
            return $this->has['uniqueid'];
        }

        return $result;
    }
}
