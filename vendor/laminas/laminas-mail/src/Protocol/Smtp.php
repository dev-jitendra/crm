<?php

namespace Laminas\Mail\Protocol;

use Generator;
use Laminas\Mail\Headers;

use function array_key_exists;
use function array_replace_recursive;
use function chunk_split;
use function fclose;
use function fgets;
use function fopen;
use function fwrite;
use function implode;
use function ini_get;
use function is_array;
use function rewind;
use function rtrim;
use function stream_socket_enable_crypto;
use function strlen;
use function strtolower;
use function substr;


class Smtp extends AbstractProtocol
{
    use ProtocolTrait;

    
    public const SMTP_LINE_LIMIT = 998;

    
    protected $transport = 'tcp';

    
    protected $secure;

    
    protected $sess = false;

    
    protected $auth = false;

    
    protected $mail = false;

    
    protected $rcpt = false;

    
    protected $data;

    
    protected $useCompleteQuit = true;

    
    public function __construct($host = '127.0.0.1', $port = null, ?array $config = null)
    {
        
        if (is_array($host)) {
            
            if (is_array($config)) {
                $config = array_replace_recursive($host, $config);
            } else {
                $config = $host;
            }

            
            if (isset($config['host'])) {
                $host = $config['host'];
            } else {
                $host = '127.0.0.1';
            }

            
            if (isset($config['port'])) {
                $port = $config['port'];
            } else {
                $port = null;
            }
        }

        
        if (null === $config) {
            $config = [];
        }

        if (isset($config['ssl'])) {
            switch (strtolower($config['ssl'])) {
                case 'tls':
                    $this->secure = 'tls';
                    break;

                case 'ssl':
                    $this->transport = 'ssl';
                    $this->secure    = 'ssl';
                    if ($port === null) {
                        $port = 465;
                    }
                    break;

                case '':
                    
                case 'none':
                    break;

                default:
                    throw new Exception\InvalidArgumentException($config['ssl'] . ' is unsupported SSL type');
            }
        }

        if (array_key_exists('use_complete_quit', $config)) {
            $this->setUseCompleteQuit($config['use_complete_quit']);
        }

        
        if ($port === null) {
            if (($port = ini_get('smtp_port')) == '') {
                $port = 25;
            }
        }

        if (array_key_exists('novalidatecert', $config)) {
            $this->setNoValidateCert($config['novalidatecert']);
        }

        parent::__construct($host, $port);
    }

    
    public function setUseCompleteQuit($useCompleteQuit)
    {
        return $this->useCompleteQuit = (bool) $useCompleteQuit;
    }

    
    private static function chunkedReader(string $data, int $chunkSize = 4096): Generator
    {
        if (($fp = fopen("php:
            throw new Exception\RuntimeException('cannot fopen');
        }
        if (fwrite($fp, $data) === false) {
            throw new Exception\RuntimeException('cannot fwrite');
        }
        rewind($fp);

        $line = null;
        while (($buffer = fgets($fp, $chunkSize)) !== false) {
            $line .= $buffer;

            
            
            
            
            
            
            
            
            
            
            
            
            
            
            $lastByte = $buffer[$chunkSize - 2] ?? null;

            
            
            if ($lastByte !== "\n" && $lastByte !== null) {
                continue;
            }

            yield $line;
            $line = null;
        }

        if ($line !== null) {
            yield $line;
        }

        fclose($fp);
    }

    
    public function useCompleteQuit()
    {
        return $this->useCompleteQuit;
    }

    
    public function connect()
    {
        $this->socket = $this->setupSocket(
            $this->transport,
            $this->host,
            $this->port,
            self::TIMEOUT_CONNECTION
        );
        return true;
    }

    
    public function helo($host = '127.0.0.1')
    {
        
        if ($this->sess === true) {
            throw new Exception\RuntimeException('Cannot issue HELO to existing session');
        }

        
        if (! $this->validHost->isValid($host)) {
            throw new Exception\RuntimeException(implode(', ', $this->validHost->getMessages()));
        }

        
        $this->_expect(220, 300); 
        $this->ehlo($host);

        
        if ($this->secure == 'tls') {
            $this->_send('STARTTLS');
            $this->_expect(220, 180);
            if (! stream_socket_enable_crypto($this->socket, true, $this->getCryptoMethod())) {
                throw new Exception\RuntimeException('Unable to connect via TLS');
            }
            $this->ehlo($host);
        }

        $this->startSession();
        $this->auth();
    }

    
    public function hasSession()
    {
        return $this->sess;
    }

    
    protected function ehlo($host)
    {
        
        try {
            $this->_send('EHLO ' . $host);
            $this->_expect(250, 300); 
        } catch (Exception\ExceptionInterface) {
            $this->_send('HELO ' . $host);
            $this->_expect(250, 300); 
        }
    }

    
    public function mail($from)
    {
        if ($this->sess !== true) {
            throw new Exception\RuntimeException('A valid session has not been started');
        }

        $this->_send('MAIL FROM:<' . $from . '>');
        $this->_expect(250, 300); 

        
        $this->mail = true;
        $this->rcpt = false;
        $this->data = false;
    }

    
    public function rcpt($to)
    {
        if ($this->mail !== true) {
            throw new Exception\RuntimeException('No sender reverse path has been supplied');
        }

        
        $this->_send('RCPT TO:<' . $to . '>');
        $this->_expect([250, 251], 300); 
        $this->rcpt = true;
    }

    
    public function data($data)
    {
        
        if ($this->rcpt !== true) { 
            throw new Exception\RuntimeException('No recipient forward path has been supplied');
        }

        $this->_send('DATA');
        $this->_expect(354, 120); 

        $reader = self::chunkedReader($data);
        foreach ($reader as $line) {
            $line = rtrim($line, "\r\n");
            if (isset($line[0]) && $line[0] === '.') {
                
                $line = '.' . $line;
            }

            if (strlen($line) > self::SMTP_LINE_LIMIT) {
                
                
                
                
                $chunks = chunk_split($line, self::SMTP_LINE_LIMIT - 1, Headers::FOLDING);
                $line   = substr($chunks, 0, -strlen(Headers::FOLDING));
            }

            $this->_send($line);
        }

        $this->_send('.');
        $this->_expect(250, 600); 
        $this->data = true;
    }

    
    public function rset()
    {
        $this->_send('RSET');
        
        $this->_expect([250, 220]);

        $this->mail = false;
        $this->rcpt = false;
        $this->data = false;
    }

    
    public function noop()
    {
        $this->_send('NOOP');
        $this->_expect(250, 300); 
    }

    
    public function vrfy($user)
    {
        $this->_send('VRFY ' . $user);
        $this->_expect([250, 251, 252], 300); 
    }

    
    public function quit()
    {
        if ($this->sess) {
            $this->auth = false;

            if ($this->useCompleteQuit()) {
                $this->_send('QUIT');
                $this->_expect(221, 300); 
            }

            $this->stopSession();
        }
    }

    
    public function auth()
    {
        if ($this->auth === true) {
            throw new Exception\RuntimeException('Already authenticated for this session');
        }
    }

    
    public function disconnect()
    {
        $this->_disconnect();
    }

    
    
    protected function _disconnect()
    {
        
        $this->quit();
        parent::_disconnect();
    }

    
    protected function startSession()
    {
        $this->sess = true;
    }

    
    protected function stopSession()
    {
        $this->sess = false;
    }
}
