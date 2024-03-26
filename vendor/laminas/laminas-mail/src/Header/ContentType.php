<?php

namespace Laminas\Mail\Header;

use Laminas\Mail\Headers;
use Laminas\Mime\Mime;

use function count;
use function explode;
use function implode;
use function in_array;
use function preg_match;
use function sprintf;
use function str_replace;
use function strtolower;
use function trim;

class ContentType implements UnstructuredInterface
{
    
    protected $type;

    
    protected $encoding = 'ASCII';

    
    protected $parameters = [];

    
    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        
        if (! in_array(strtolower($name), ['contenttype', 'content_type', 'content-type'])) {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Type string');
        }

        $value = str_replace(Headers::FOLDING, ' ', $value);
        $parts = explode(';', $value, 2);

        $header = new static();
        $header->setType($parts[0]);

        if (isset($parts[1])) {
            $values = ListParser::parse(trim($parts[1]), [';', '=']);
            $length = count($values);

            for ($i = 0; $i < $length; $i += 2) {
                $value = $values[$i + 1];
                $value = trim($value, "'\" \t\n\r\0\x0B");
                $header->addParameter($values[$i], $value);
            }
        }

        return $header;
    }

    
    public function getFieldName()
    {
        return 'Content-Type';
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        $prepared = $this->type;
        if (empty($this->parameters)) {
            return $prepared;
        }

        $values = [$prepared];
        foreach ($this->parameters as $attribute => $value) {
            if (HeaderInterface::FORMAT_ENCODED === $format && ! Mime::isPrintable($value)) {
                $this->encoding = 'UTF-8';
                $value          = HeaderWrap::wrap($value, $this);
                $this->encoding = 'ASCII';
            }

            $values[] = sprintf('%s="%s"', $attribute, $value);
        }

        return implode(';' . Headers::FOLDING, $values);
    }

    
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    
    public function getEncoding()
    {
        return $this->encoding;
    }

    
    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
    }

    
    public function setType($type)
    {
        if (! preg_match('/^[a-z-]+\/[a-z0-9.+-]+$/i', $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a value in the format "type/subtype"; received "%s"',
                __METHOD__,
                (string) $type
            ));
        }
        $this->type = $type;
        return $this;
    }

    
    public function getType()
    {
        return $this->type;
    }

    
    public function addParameter($name, $value)
    {
        $name  = trim(strtolower($name));
        $value = (string) $value;

        if (! HeaderValue::isValid($name)) {
            throw new Exception\InvalidArgumentException('Invalid content-type parameter name detected');
        }
        if (! HeaderWrap::canBeEncoded($value)) {
            throw new Exception\InvalidArgumentException(
                'Parameter value must be composed of printable US-ASCII or UTF-8 characters.'
            );
        }

        $this->parameters[$name] = $value;
        return $this;
    }

    
    public function getParameters()
    {
        return $this->parameters;
    }

    
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        return null;
    }

    
    public function removeParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }
}
