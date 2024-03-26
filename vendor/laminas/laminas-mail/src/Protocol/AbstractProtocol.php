<?php

namespace Laminas\Mail\Protocol;

use Laminas\Validator;
use Laminas\Validator\ValidatorChain;

use function array_shift;
use function count;
use function fclose;
use function fgets;
use function fwrite;
use function implode;
use function in_array;
use function is_array;
use function is_resource;
use function preg_split;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_starts_with;
use function stream_get_meta_data;
use function stream_set_timeout;
use function stream_socket_client;

use const E_WARNING;
use const PREG_SPLIT_DELIM_CAPTURE;


abstract class AbstractProtocol
{
    
    public const EOL = "\r\n";

    
    public const TIMEOUT_CONNECTION = 30;

    
    protected $maximumLog = 64;

    
    protected $host;

    
    protected $validHost;

    
    protected $socket;

    
    protected $request;

    
    protected $response;

    
    private array $log = [];

    
    public function __construct($host = '127.0.0.1', protected $port = null)
    {
        $this->validHost = new Validator\ValidatorChain();
        $this->validHost->attach(new Validator\Hostname(Validator\Hostname::ALLOW_ALL));

        if (! $this->validHost->isValid($host)) {
            throw new Exception\RuntimeException(implode(', ', $this->validHost->getMessages()));
        }

        $this->host = $host;
    }

    
    public function __destruct()
    {
        $this->_disconnect();
    }

    
    public function setMaximumLog($maximumLog)
    {
        $this->maximumLog = (int) $maximumLog;
    }

    
    public function getMaximumLog()
    {
        return $this->maximumLog;
    }

    
    abstract public function connect();

    
    public function getRequest()
    {
        return $this->request;
    }

    
    public function getResponse()
    {
        return $this->response;
    }

    
    public function getLog()
    {
        return implode('', $this->log);
    }

    
    public function resetLog()
    {
        $this->log = [];
    }

    
    
    protected function _addLog($value)
    {
        if ($this->maximumLog >= 0 && count($this->log) >= $this->maximumLog) {
            array_shift($this->log);
        }

        $this->log[] = $value;
    }

    
    
    protected function _connect($remote)
    {
        $errorNum = 0;
        $errorStr = '';

        
        set_error_handler(
            static function ($error, $message = '') {
                throw new Exception\RuntimeException(sprintf('Could not open socket: %s', $message), $error);
            },
            E_WARNING
        );
        $this->socket = stream_socket_client($remote, $errorNum, $errorStr, self::TIMEOUT_CONNECTION);
        restore_error_handler();

        if ($this->socket === false) {
            if ($errorNum == 0) {
                $errorStr = 'Could not open socket';
            }
            throw new Exception\RuntimeException($errorStr);
        }

        if (($result = stream_set_timeout($this->socket, self::TIMEOUT_CONNECTION)) === false) {
            throw new Exception\RuntimeException('Could not set stream timeout');
        }

        return $result;
    }

    
    
    protected function _disconnect()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
    }

    
    
    protected function _send($request)
    {
        if (! is_resource($this->socket)) {
            throw new Exception\RuntimeException('No connection has been established to ' . $this->host);
        }

        $this->request = $request;

        $result = fwrite($this->socket, $request . self::EOL);

        
        $this->_addLog($request . self::EOL);

        if ($result === false) {
            throw new Exception\RuntimeException('Could not send request to ' . $this->host);
        }

        return $result;
    }

    
    
    protected function _receive($timeout = null)
    {
        if (! is_resource($this->socket)) {
            throw new Exception\RuntimeException('No connection has been established to ' . $this->host);
        }

        
        if ($timeout !== null) {
            stream_set_timeout($this->socket, $timeout);
        }

        
        $response = fgets($this->socket, 1024);

        
        $this->_addLog($response);

        
        $info = stream_get_meta_data($this->socket);

        if ($info['timed_out']) {
            throw new Exception\RuntimeException($this->host . ' has timed out');
        }

        if ($response === false) {
            throw new Exception\RuntimeException('Could not read from ' . $this->host);
        }

        return $response;
    }

    
    
    protected function _expect($code, $timeout = null)
    {
        $this->response = [];
        $errMsg         = '';

        if (! is_array($code)) {
            $code = [$code];
        }

        do {
            $this->response[]   = $result = $this->_receive($timeout);
            [$cmd, $more, $msg] = preg_split('/([\s-]+)/', $result, 2, PREG_SPLIT_DELIM_CAPTURE);

            if ($errMsg !== '') {
                $errMsg .= ' ' . $msg;
            } elseif ($cmd === null || ! in_array($cmd, $code)) {
                $errMsg = $msg;
            }

        
        } while (str_starts_with($more, '-'));

        if ($errMsg !== '') {
            throw new Exception\RuntimeException($errMsg, (int) $cmd);
        }

        return $msg;
    }
}
