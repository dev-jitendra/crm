<?php

declare(strict_types=1);

namespace Laminas\Mail;

use ArrayIterator;
use Countable;
use Iterator;
use Laminas\Loader\PluginClassLocator;
use Laminas\Mail\Header\GenericHeader;
use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Header\HeaderLocatorInterface;
use ReturnTypeWillChange;
use Traversable;

use function array_keys;
use function array_shift;
use function assert;
use function count;
use function current;
use function explode;
use function gettype;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function key;
use function next;
use function preg_match;
use function reset;
use function sprintf;
use function str_replace;
use function strtolower;
use function trigger_error;
use function trim;

use const E_USER_DEPRECATED;


class Headers implements Countable, Iterator
{
    
    public const EOL = "\r\n";

    
    public const FOLDING = "\r\n ";

    private ?HeaderLocatorInterface $headerLocator = null;

    
    protected $pluginClassLoader;

    
    protected $headersKeys = [];

    
    protected $headers = [];

    
    protected $encoding = 'ASCII';

    
    public static function fromString($string, $eol = self::EOL)
    {
        $headers     = new static();
        $currentLine = '';
        $emptyLine   = 0;

        
        $lines = explode($eol, $string);
        $total = count($lines);
        for ($i = 0; $i < $total; $i += 1) {
            $line = $lines[$i];

            if ($line === "") {
                
                
                $emptyLine += 1;
                if ($emptyLine > 2) {
                    throw new Exception\RuntimeException('Malformed header detected');
                }
                continue;
            } elseif (preg_match('/^\s*$/', $line)) {
                
                continue;
            }

            if ($emptyLine > 1) {
                throw new Exception\RuntimeException('Malformed header detected');
            }

            
            if (preg_match('/^[\x21-\x39\x3B-\x7E]+:.*$/', $line)) {
                if ($currentLine) {
                    
                    $headers->addHeaderLine($currentLine);
                }
                $currentLine = trim($line);
                continue;
            }

            
            
            if (preg_match('/^\s+.*$/', $line)) {
                $currentLine .= ' ' . trim($line);
                continue;
            }

            
            throw new Exception\RuntimeException(sprintf(
                'Line "%s" does not match header format!',
                $line
            ));
        }
        if ($currentLine) {
            $headers->addHeaderLine($currentLine);
        }
        return $headers;
    }

    
    public function setPluginClassLoader(PluginClassLocator $pluginClassLoader)
    {
        
        @trigger_error(sprintf(
            'Since laminas/laminas-mail 2.12.0: Usage of %s is deprecated; use %s::setHeaderLocator() instead',
            __METHOD__,
            self::class
        ), E_USER_DEPRECATED);

        $this->pluginClassLoader = $pluginClassLoader;
        return $this;
    }

    
    public function getPluginClassLoader()
    {
        
        @trigger_error(sprintf(
            'Since laminas/laminas-mail 2.12.0: Usage of %s is deprecated; use %s::getHeaderLocator() instead',
            __METHOD__,
            self::class
        ), E_USER_DEPRECATED);

        if (! $this->pluginClassLoader) {
            $this->pluginClassLoader = new Header\HeaderLoader();
        }

        return $this->pluginClassLoader;
    }

    
    public function getHeaderLocator(): HeaderLocatorInterface
    {
        if (! $this->headerLocator) {
            $this->setHeaderLocator(new Header\HeaderLocator());
        }

        assert($this->headerLocator instanceof HeaderLocatorInterface);

        return $this->headerLocator;
    }

    
    public function setHeaderLocator(HeaderLocatorInterface $headerLocator)
    {
        $this->headerLocator = $headerLocator;
        return $this;
    }

    
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        foreach ($this as $header) {
            $header->setEncoding($encoding);
        }
        return $this;
    }

    
    public function getEncoding()
    {
        return $this->encoding;
    }

    
    public function addHeaders($headers)
    {
        if (! is_array($headers) && ! $headers instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable; received "%s"',
                is_object($headers) ? $headers::class : gettype($headers)
            ));
        }

        foreach ($headers as $name => $value) {
            if (is_int($name)) {
                if (is_string($value)) {
                    $this->addHeaderLine($value);
                } elseif (is_array($value) && count($value) == 1) {
                    $this->addHeaderLine(key($value), current($value));
                } elseif (is_array($value) && count($value) == 2) {
                    $this->addHeaderLine($value[0], $value[1]);
                } elseif ($value instanceof Header\HeaderInterface) {
                    $this->addHeader($value);
                }
            } elseif (is_string($name)) {
                $this->addHeaderLine($name, $value);
            }
        }

        return $this;
    }

    
    public function addHeaderLine($headerFieldNameOrLine, $fieldValue = null)
    {
        if (! is_string($headerFieldNameOrLine)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects its first argument to be a string; received "%s"',
                __METHOD__,
                is_object($headerFieldNameOrLine)
                ? $headerFieldNameOrLine::class
                : gettype($headerFieldNameOrLine)
            ));
        }

        if ($fieldValue === null) {
            $headers = $this->loadHeader($headerFieldNameOrLine);
            $headers = is_array($headers) ? $headers : [$headers];
            foreach ($headers as $header) {
                $this->addHeader($header);
            }
        } elseif (is_array($fieldValue)) {
            foreach ($fieldValue as $i) {
                $this->addHeader(Header\GenericMultiHeader::fromString($headerFieldNameOrLine . ':' . $i));
            }
        } else {
            $this->addHeader(GenericHeader::fromString($headerFieldNameOrLine . ':' . $fieldValue));
        }

        return $this;
    }

    
    public function addHeader(HeaderInterface $header)
    {
        $key                 = $this->normalizeFieldName($header->getFieldName());
        $this->headersKeys[] = $key;
        $this->headers[]     = $header;
        if ($this->getEncoding() !== 'ASCII') {
            $header->setEncoding($this->getEncoding());
        }
        return $this;
    }

    
    public function removeHeader($instanceOrFieldName)
    {
        if (! $instanceOrFieldName instanceof Header\HeaderInterface && ! is_string($instanceOrFieldName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires a string or %s instance; received %s',
                __METHOD__,
                HeaderInterface::class,
                is_object($instanceOrFieldName) ? $instanceOrFieldName::class : gettype($instanceOrFieldName)
            ));
        }

        if ($instanceOrFieldName instanceof Header\HeaderInterface) {
            $indexes = array_keys($this->headers, $instanceOrFieldName, true);
        }

        if (is_string($instanceOrFieldName)) {
            $key     = $this->normalizeFieldName($instanceOrFieldName);
            $indexes = array_keys($this->headersKeys, $key, true);
        }

        if (! empty($indexes)) {
            foreach ($indexes as $index) {
                unset($this->headersKeys[$index]);
                unset($this->headers[$index]);
            }
            return true;
        }

        return false;
    }

    
    public function clearHeaders()
    {
        $this->headers = $this->headersKeys = [];
        return $this;
    }

    
    public function get($name)
    {
        $key     = $this->normalizeFieldName($name);
        $results = [];

        foreach (array_keys($this->headersKeys, $key, true) as $index) {
            if ($this->headers[$index] instanceof Header\GenericHeader) {
                $results[] = $this->lazyLoadHeader($index);
            } else {
                $results[] = $this->headers[$index];
            }
        }

        switch (count($results)) {
            case 0:
                return false;
            case 1:
                if ($results[0] instanceof Header\MultipleHeadersInterface) {
                    return new ArrayIterator($results);
                }
                return $results[0];
            default:
                return new ArrayIterator($results);
        }
    }

    
    public function has($name)
    {
        $name = $this->normalizeFieldName($name);
        return in_array($name, $this->headersKeys, true);
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        next($this->headers);
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->headers);
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        return current($this->headers) !== false;
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->headers);
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        $current = current($this->headers);
        if ($current instanceof Header\GenericHeader) {
            $current = $this->lazyLoadHeader(key($this->headers));
        }
        return $current;
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->headers);
    }

    
    public function toString()
    {
        $headers = '';
        foreach ($this as $header) {
            if ($str = $header->toString()) {
                $headers .= $str . self::EOL;
            }
        }

        return $headers;
    }

    
    public function toArray($format = HeaderInterface::FORMAT_RAW)
    {
        $headers = [];
        foreach ($this->headers as $header) {
            if ($header instanceof Header\MultipleHeadersInterface) {
                $name = $header->getFieldName();
                if (! isset($headers[$name])) {
                    $headers[$name] = [];
                }
                $headers[$name][] = $header->getFieldValue($format);
            } else {
                $headers[$header->getFieldName()] = $header->getFieldValue($format);
            }
        }
        return $headers;
    }

    
    public function forceLoading()
    {
        
        foreach ($this as $item) {
            
        }
        return true;
    }

    
    public function loadHeader($headerLine)
    {
        [$name] = GenericHeader::splitHeaderLine($headerLine);

        $class = $this->resolveHeaderClass($name);
        assert(null !== $class);

        return $class::fromString($headerLine);
    }

    
    protected function lazyLoadHeader($index)
    {
        $current = $this->headers[$index];

        $key = $this->headersKeys[$index];

        $class = $this->resolveHeaderClass($key);
        assert(null !== $class);

        $encoding = $current->getEncoding();
        $headers  = $class::fromString($current->toString());
        if (is_array($headers)) {
            $current = array_shift($headers);
            assert($current instanceof HeaderInterface);
            $current->setEncoding($encoding);
            $this->headers[$index] = $current;
            foreach ($headers as $header) {
                assert($header instanceof HeaderInterface);
                $header->setEncoding($encoding);
                $this->headersKeys[] = $key;
                $this->headers[]     = $header;
            }
            return $current;
        }

        $current = $headers;
        $current->setEncoding($encoding);
        $this->headers[$index] = $current;
        return $current;
    }

    
    protected function normalizeFieldName($fieldName)
    {
        return str_replace(['-', '_', ' ', '.'], '', strtolower($fieldName));
    }

    
    private function resolveHeaderClass($key): ?string
    {
        if ($this->pluginClassLoader) {
            return $this->pluginClassLoader->load($key) ?: GenericHeader::class;
        }
        return $this->getHeaderLocator()->get($key, GenericHeader::class);
    }
}
