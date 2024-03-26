<?php

namespace Laminas\Mail\Storage;

use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Message\File;
use Laminas\Mail\Storage\Message\MessageInterface;
use Laminas\Stdlib\ErrorHandler;

use function array_combine;
use function count;
use function fclose;
use function fgets;
use function filemtime;
use function fopen;
use function fseek;
use function ftell;
use function is_dir;
use function is_resource;
use function is_subclass_of;
use function range;
use function str_starts_with;
use function stream_get_contents;
use function strlen;
use function strtolower;
use function trim;

use const E_WARNING;

class Mbox extends AbstractStorage
{
    
    protected $fh;

    
    protected $filename;

    
    protected $filemtime;

    
    protected $positions;

    
    protected $messageClass = File::class;

    
    
    protected $messageEOL;

    
    public function countMessages()
    {
        return count($this->positions);
    }

    
    public function getSize($id = 0)
    {
        if ($id) {
            $pos = $this->positions[$id - 1];
            return $pos['end'] - $pos['start'];
        }

        $result = [];
        foreach ($this->positions as $num => $pos) {
            $result[$num + 1] = $pos['end'] - $pos['start'];
        }

        return $result;
    }

    
    protected function getPos($id)
    {
        if (! isset($this->positions[$id - 1])) {
            throw new Exception\InvalidArgumentException('id does not exist');
        }

        return $this->positions[$id - 1];
    }

    
    public function getMessage($id)
    {
        
        if (
            is_subclass_of($this->messageClass, File::class)
            || strtolower($this->messageClass) === strtolower(File::class)
        ) {
            
            $messagePos = $this->getPos($id);

            $messageClassParams = [
                'file'     => $this->fh,
                'startPos' => $messagePos['start'],
                'endPos'   => $messagePos['end'],
            ];

            if (isset($this->messageEOL)) {
                $messageClassParams['EOL'] = $this->messageEOL;
            }

            return new $this->messageClass($messageClassParams);
        }

        
        

        $message = $this->getRawHeader($id);

        

        return new $this->messageClass(['handler' => $this, 'id' => $id, 'headers' => $message]);
    }

    
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }
        $messagePos = $this->getPos($id);
        
        return stream_get_contents($this->fh, $messagePos['separator'] - $messagePos['start'], $messagePos['start']);
    }

    
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            
            throw new Exception\RuntimeException('not implemented');
        }
        $messagePos = $this->getPos($id);
        return stream_get_contents($this->fh, $messagePos['end'] - $messagePos['separator'], $messagePos['separator']);
    }

    
    public function __construct($params)
    {
        $params = ParamsNormalizer::normalizeParams($params);

        if (! isset($params['filename'])) {
            throw new Exception\InvalidArgumentException('no valid filename given in params');
        }

        if (isset($params['messageEOL'])) {
            $this->messageEOL = (string) $params['messageEOL'];
        }

        $this->openMboxFile((string) $params['filename']);
        $this->has['top']      = true;
        $this->has['uniqueid'] = false;
    }

    
    protected function isMboxFile($file, $fileIsString = true)
    {
        if ($fileIsString) {
            ErrorHandler::start(E_WARNING);
            $file = fopen($file, 'r');
            ErrorHandler::stop();
            if (! $file) {
                return false;
            }
        } else {
            fseek($file, 0);
        }

        $result = false;

        $line = fgets($file) ?: '';
        if (str_starts_with($line, 'From ')) {
            $result = true;
        }

        if ($fileIsString) {
            ErrorHandler::start(E_WARNING);
            fclose($file);
            ErrorHandler::stop();
        }

        return $result;
    }

    
    protected function openMboxFile($filename)
    {
        if ($this->fh) {
            $this->close();
        }

        if (is_dir($filename)) {
            throw new Exception\InvalidArgumentException('file is not a valid mbox file');
        }

        ErrorHandler::start();
        $this->fh = fopen($filename, 'r');
        $error    = ErrorHandler::stop();
        if (! $this->fh) {
            throw new Exception\RuntimeException('cannot open mbox file', 0, $error);
        }
        $this->filename  = $filename;
        $this->filemtime = filemtime($this->filename);

        if (! $this->isMboxFile($this->fh, false)) {
            ErrorHandler::start(E_WARNING);
            fclose($this->fh);
            $error = ErrorHandler::stop();
            throw new Exception\InvalidArgumentException('file is not a valid mbox format', 0, $error);
        }

        $messagePos = ['start' => ftell($this->fh), 'separator' => 0, 'end' => 0];
        while (($line = fgets($this->fh)) !== false) {
            if (str_starts_with($line, 'From ')) {
                $messagePos['end'] = ftell($this->fh) - strlen($line) - 2; 
                if (! $messagePos['separator']) {
                    $messagePos['separator'] = $messagePos['end'];
                }
                $this->positions[] = $messagePos;
                $messagePos        = ['start' => ftell($this->fh), 'separator' => 0, 'end' => 0];
            }
            if (! $messagePos['separator'] && ! trim($line)) {
                $messagePos['separator'] = ftell($this->fh);
            }
        }

        $messagePos['end'] = ftell($this->fh);
        if (! $messagePos['separator']) {
            $messagePos['separator'] = $messagePos['end'];
        }
        $this->positions[] = $messagePos;
    }

    
    public function close()
    {
        if (is_resource($this->fh)) {
            fclose($this->fh);
        }
        $this->positions = [];
    }

    
    public function noop()
    {
        return true;
    }

    
    public function removeMessage($id)
    {
        throw new Exception\RuntimeException('mbox is read-only');
    }

    
    public function getUniqueId($id = null)
    {
        if ($id) {
            
            $this->getPos($id);
            return $id;
        }

        $range = range(1, $this->countMessages());
        return array_combine($range, $range);
    }

    
    public function getNumberByUniqueId($id)
    {
        
        $this->getPos($id);
        return $id;
    }

    
    public function __sleep()
    {
        return ['filename', 'positions', 'filemtime'];
    }

    
    public function __wakeup()
    {
        ErrorHandler::start();
        $filemtime = filemtime($this->filename);
        ErrorHandler::stop();
        if ($this->filemtime != $filemtime) {
            $this->close();
            $this->openMboxFile($this->filename);
        } else {
            ErrorHandler::start();
            $this->fh = fopen($this->filename, 'r');
            $error    = ErrorHandler::stop();
            if (! $this->fh) {
                throw new Exception\RuntimeException('cannot open mbox file', 0, $error);
            }
        }
    }
}
