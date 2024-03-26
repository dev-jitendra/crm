<?php

namespace Laminas\Validator;

use Traversable;

use function array_key_exists;
use function class_exists;
use function get_debug_type;
use function is_array;
use function is_string;
use function property_exists;
use function sprintf;
use function strtolower;
use function substr;
use function ucfirst;

class Barcode extends AbstractValidator
{
    public const INVALID        = 'barcodeInvalid';
    public const FAILED         = 'barcodeFailed';
    public const INVALID_CHARS  = 'barcodeInvalidChars';
    public const INVALID_LENGTH = 'barcodeInvalidLength';

    
    protected $messageTemplates = [
        self::FAILED         => 'The input failed checksum validation',
        self::INVALID_CHARS  => 'The input contains invalid characters',
        self::INVALID_LENGTH => 'The input should have a length of %length% characters',
        self::INVALID        => 'Invalid type given. String expected',
    ];

    
    protected $messageVariables = [
        'length' => ['options' => 'length'],
    ];

    
    protected $options = [
        'adapter'     => null, 
        'options'     => null, 
        'length'      => null,
        'useChecksum' => null,
    ];

    
    public function __construct($options = null)
    {
        if ($options === null) {
            $options = [];
        }

        if (is_array($options)) {
            if (array_key_exists('options', $options)) {
                $options['options'] = ['options' => $options['options']];
            }
        } elseif ($options instanceof Traversable) {
            if (property_exists($options, 'options')) {
                $options['options'] = ['options' => $options['options']];
            }
        } else {
            $options = ['adapter' => $options];
        }

        parent::__construct($options);
    }

    
    public function getAdapter()
    {
        if (! $this->options['adapter'] instanceof Barcode\AdapterInterface) {
            $this->setAdapter('Ean13');
        }

        return $this->options['adapter'];
    }

    
    public function setAdapter($adapter, $options = null)
    {
        if (is_string($adapter)) {
            $adapter = ucfirst(strtolower($adapter));
            $adapter = 'Laminas\\Validator\\Barcode\\' . $adapter;

            if (! class_exists($adapter)) {
                throw new Exception\InvalidArgumentException('Barcode adapter matching "' . $adapter . '" not found');
            }

            $adapter = new $adapter($options);
        }

        if (! $adapter instanceof Barcode\AdapterInterface) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Adapter %s does not implement Laminas\\Validator\\Barcode\\AdapterInterface',
                    get_debug_type($adapter)
                )
            );
        }

        $this->options['adapter'] = $adapter;

        return $this;
    }

    
    public function getChecksum()
    {
        return $this->getAdapter()->getChecksum();
    }

    
    public function useChecksum($checksum = null)
    {
        return $this->getAdapter()->useChecksum($checksum);
    }

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        $adapter                 = $this->getAdapter();
        $this->options['length'] = $adapter->getLength();
        $result                  = $adapter->hasValidLength($value);
        if (! $result) {
            if (is_array($this->options['length'])) {
                $temp                    = $this->options['length'];
                $this->options['length'] = '';
                foreach ($temp as $length) {
                    $this->options['length'] .= '/';
                    $this->options['length'] .= $length;
                }

                $this->options['length'] = substr($this->options['length'], 1);
            }

            $this->error(self::INVALID_LENGTH);
            return false;
        }

        $result = $adapter->hasValidCharacters($value);
        if (! $result) {
            $this->error(self::INVALID_CHARS);
            return false;
        }

        if ($this->useChecksum(null)) {
            $result = $adapter->hasValidChecksum($value);
            if (! $result) {
                $this->error(self::FAILED);
                return false;
            }
        }

        return true;
    }
}
