<?php

namespace Laminas\Mail\Protocol;

use Laminas\Mail\Protocol\Pop3\Response;
use Laminas\Stdlib\ErrorHandler;

use function explode;
use function fclose;
use function fgets;
use function fwrite;
use function is_string;
use function md5;
use function rtrim;
use function stream_socket_enable_crypto;
use function strpos;
use function strtok;
use function strtolower;
use function substr;
use function trim;

class Pop3
{
    use ProtocolTrait;

    
    public const TIMEOUT_CONNECTION = 30;

    
    public $hasTop;

    
    protected $socket;

    
    protected $timestamp;

    
    public function __construct($host = '', $port = null, $ssl = false, $novalidatecert = false)
    {
        $this->setNoValidateCert($novalidatecert);

        if ($host) {
            $this->connect($host, $port, $ssl);
        }
    }

    
    public function __destruct()
    {
        $this->logout();
    }

    
    public function connect($host, $port = null, $ssl = false)
    {
        $transport = 'tcp';
        $isTls     = false;

        if ($ssl) {
            $ssl = strtolower($ssl);
        }

        switch ($ssl) {
            case 'ssl':
                $transport = 'ssl';
                if (! $port) {
                    $port = 995;
                }
                break;
            case 'tls':
                $isTls = true;
                
            default:
                if (! $port) {
                    $port = 110;
                }
        }

        $this->socket = $this->setupSocket($transport, $host, $port, self::TIMEOUT_CONNECTION);

        $welcome = $this->readResponse();

        strtok($welcome, '<');
        $this->timestamp = strtok('>');
        if (! strpos($this->timestamp, '@')) {
            $this->timestamp = null;
        } else {
            $this->timestamp = '<' . $this->timestamp . '>';
        }

        if ($isTls) {
            $this->request('STLS');
            $result = stream_socket_enable_crypto($this->socket, true, $this->getCryptoMethod());
            if (! $result) {
                throw new Exception\RuntimeException('cannot enable TLS');
            }
        }

        return $welcome;
    }

    
    public function sendRequest($request)
    {
        ErrorHandler::start();
        $result = fwrite($this->socket, $request . "\r\n");
        $error  = ErrorHandler::stop();
        if (! $result) {
            throw new Exception\RuntimeException('send failed - connection closed?', 0, $error);
        }
    }

    
    public function readResponse($multiline = false)
    {
        $response = $this->readRemoteResponse();

        if ($response->status() != '+OK') {
            throw new Exception\RuntimeException('last request failed');
        }

        $message = $response->message();

        if ($multiline) {
            $message = '';
            $line    = fgets($this->socket);
            while ($line && rtrim($line, "\r\n") != '.') {
                if ($line[0] == '.') {
                    $line = substr($line, 1);
                }
                $message .= $line;
                $line     = fgets($this->socket);
            }
        }

        return $message;
    }

    
    protected function readRemoteResponse(): Response
    {
        ErrorHandler::start();
        $result = fgets($this->socket);
        $error  = ErrorHandler::stop();
        if (! is_string($result)) {
            throw new Exception\RuntimeException('read failed - connection closed?', 0, $error);
        }

        $result = trim($result);
        if (strpos($result, ' ')) {
            [$status, $message] = explode(' ', $result, 2);
        } else {
            $status  = $result;
            $message = '';
        }

        return new Response($status, $message);
    }

    
    public function request($request, $multiline = false)
    {
        $this->sendRequest($request);
        return $this->readResponse($multiline);
    }

    
    public function logout()
    {
        if ($this->socket) {
            try {
                $this->request('QUIT');
            } catch (Exception\ExceptionInterface) {
                
            }

            fclose($this->socket);
            $this->socket = null;
        }
    }

    
    public function capa()
    {
        $result = $this->request('CAPA', true);
        return explode("\n", $result);
    }

    
    public function login($user, $password, $tryApop = true)
    {
        if ($tryApop && $this->timestamp) {
            try {
                $this->request("APOP $user " . md5($this->timestamp . $password));
                return;
            } catch (Exception\ExceptionInterface) {
                
            }
        }

        $this->request("USER $user");
        $this->request("PASS $password");
    }

    
    public function status(&$messages, &$octets)
    {
        $messages = 0;
        $octets   = 0;
        $result   = $this->request('STAT');

        [$messages, $octets] = explode(' ', $result);
    }

    
    public function getList($msgno = null)
    {
        if ($msgno !== null) {
            $result = $this->request("LIST $msgno");

            [, $result] = explode(' ', $result);
            return (int) $result;
        }

        $result   = $this->request('LIST', true);
        $messages = [];
        $line     = strtok($result, "\n");
        while ($line) {
            [$no, $size]         = explode(' ', trim($line));
            $messages[(int) $no] = (int) $size;
            $line                = strtok("\n");
        }

        return $messages;
    }

    
    public function uniqueid($msgno = null)
    {
        if ($msgno !== null) {
            $result = $this->request("UIDL $msgno");

            [, $result] = explode(' ', $result);
            return $result;
        }

        $result = $this->request('UIDL', true);

        $result   = explode("\n", $result);
        $messages = [];
        foreach ($result as $line) {
            if (! $line) {
                continue;
            }
            [$no, $id]           = explode(' ', trim($line), 2);
            $messages[(int) $no] = $id;
        }

        return $messages;
    }

    
    public function top($msgno, $lines = 0, $fallback = false)
    {
        if ($this->hasTop === false) {
            if ($fallback) {
                return $this->retrieve($msgno);
            }

            throw new Exception\RuntimeException('top not supported and no fallback wanted');
        }
        $this->hasTop = true;

        $lines = ! $lines || $lines < 1 ? 0 : (int) $lines;

        try {
            $result = $this->request("TOP $msgno $lines", true);
        } catch (Exception\ExceptionInterface $e) {
            $this->hasTop = false;
            if ($fallback) {
                $result = $this->retrieve($msgno);
            } else {
                throw $e;
            }
        }

        return $result;
    }

    
    public function retrieve($msgno)
    {
        return $this->request("RETR $msgno", true);
    }

    
    public function noop()
    {
        $this->request('NOOP');
    }

    
    public function delete($msgno)
    {
        $this->request("DELE $msgno");
    }

    
    public function undelete()
    {
        $this->request('RSET');
    }
}
