<?php 

namespace Laminas\Mime;

use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;

use function array_keys;
use function base64_decode;
use function count;
use function current;
use function quoted_printable_decode;
use function sprintf;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trim;

class Message
{
    
    protected $parts = [];

    
    protected $mime;

    
    public function getParts()
    {
        return $this->parts;
    }

    
    public function setParts($parts)
    {
        $this->parts = $parts;
        return $this;
    }

    
    public function addPart(Part $part)
    {
        foreach ($this->getParts() as $row) {
            if ($part === $row) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Provided part %s already defined.',
                    $part->getId()
                ));
            }
        }

        $this->parts[] = $part;
        return $this;
    }

    
    public function isMultiPart()
    {
        return count($this->parts) > 1;
    }

    
    public function setMime(Mime $mime)
    {
        $this->mime = $mime;
        return $this;
    }

    
    public function getMime()
    {
        if ($this->mime === null) {
            $this->mime = new Mime();
        }

        return $this->mime;
    }

    
    public function generateMessage($EOL = Mime::LINEEND)
    {
        if (! $this->isMultiPart()) {
            if (empty($this->parts)) {
                return '';
            }
            $part = current($this->parts);
            $body = $part->getContent($EOL);
        } else {
            $mime = $this->getMime();

            $boundaryLine = $mime->boundaryLine($EOL);
            $body         = 'This is a message in Mime Format.  If you see this, '
                  . "your mail reader does not support this format." . $EOL;

            foreach (array_keys($this->parts) as $p) {
                $body .= $boundaryLine
                       . $this->getPartHeaders($p, $EOL)
                       . $EOL
                       . $this->getPartContent($p, $EOL);
            }

            $body .= $mime->mimeEnd($EOL);
        }

        return trim($body);
    }

    
    public function getPartHeadersArray($partnum)
    {
        return $this->parts[$partnum]->getHeadersArray();
    }

    
    public function getPartHeaders($partnum, $EOL = Mime::LINEEND)
    {
        return $this->parts[$partnum]->getHeaders($EOL);
    }

    
    public function getPartContent($partnum, $EOL = Mime::LINEEND)
    {
        return $this->parts[$partnum]->getContent($EOL);
    }

    
    protected static function _disassembleMime($body, $boundary)
    {
        $start = 0;
        $res   = [];
        
        
        
        $p = strpos($body, '--' . $boundary . "\n", $start);
        if ($p === false) {
            
            return [];
        }

        
        $start = $p + 3 + strlen($boundary);

        while (($p = strpos($body, '--' . $boundary . "\n", $start)) !== false) {
            $res[] = substr($body, $start, $p - $start);
            $start = $p + 3 + strlen($boundary);
        }

        
        $p = strpos($body, '--' . $boundary . '--', $start);
        if ($p === false) {
            throw new Exception\RuntimeException('Not a valid Mime Message: End Missing');
        }

        
        $res[] = substr($body, $start, $p - $start);
        return $res;
    }

    
    public static function createFromMessage($message, $boundary = null, $EOL = Mime::LINEEND)
    {
        if ($boundary) {
            $parts = Decode::splitMessageStruct($message, $boundary, $EOL);
        } else {
            Decode::splitMessage($message, $headers, $body, $EOL);
            $parts = [
                [
                    'header' => $headers,
                    'body'   => $body,
                ],
            ];
        }

        $res = new static();
        foreach ($parts as $part) {
            
            $properties = [];
            foreach ($part['header'] as $header) {
                
                

                $fieldName  = $header->getFieldName();
                $fieldValue = $header->getFieldValue();
                switch (strtolower($fieldName)) {
                    case 'content-type':
                        $properties['type'] = $fieldValue;
                        break;
                    case 'content-transfer-encoding':
                        $properties['encoding'] = $fieldValue;
                        break;
                    case 'content-id':
                        $properties['id'] = trim($fieldValue, '<>');
                        break;
                    case 'content-disposition':
                        $properties['disposition'] = $fieldValue;
                        break;
                    case 'content-description':
                        $properties['description'] = $fieldValue;
                        break;
                    case 'content-location':
                        $properties['location'] = $fieldValue;
                        break;
                    case 'content-language':
                        $properties['language'] = $fieldValue;
                        break;
                    default:
                        
                        break;
                }
            }

            $body = $part['body'];

            if (isset($properties['encoding'])) {
                switch ($properties['encoding']) {
                    case 'quoted-printable':
                        $body = quoted_printable_decode($body);
                        break;
                    case 'base64':
                        $body = base64_decode($body);
                        break;
                }
            }

            $newPart = new Part($body);
            foreach ($properties as $key => $value) {
                $newPart->$key = $value;
            }
            $res->addPart($newPart);
        }

        return $res;
    }
}
