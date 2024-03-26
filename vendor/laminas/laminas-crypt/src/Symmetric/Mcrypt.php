<?php

namespace Laminas\Crypt\Symmetric;

use Laminas\Stdlib\ArrayUtils;
use Psr\Container\ContainerInterface;
use Traversable;

use function array_key_exists;
use function array_keys;
use function class_exists;
use function extension_loaded;
use function get_class;
use function gettype;
use function implode;
use function in_array;
use function is_array;
use function is_object;
use function is_string;
use function is_subclass_of;
use function mb_strlen;
use function mb_substr;
use function mcrypt_decrypt;
use function mcrypt_encrypt;
use function mcrypt_get_block_size;
use function mcrypt_get_iv_size;
use function mcrypt_get_key_size;
use function mcrypt_module_get_supported_key_sizes;
use function sprintf;
use function strtolower;
use function trigger_error;

use const E_USER_DEPRECATED;
use const PHP_VERSION_ID;


class Mcrypt implements SymmetricInterface
{
    public const DEFAULT_PADDING = 'pkcs7';

    
    protected $key;

    
    protected $iv;

    
    protected $algo = 'aes';

    
    protected $mode = 'cbc';

    
    protected $padding;

    
    protected static $paddingPlugins;

    
    protected $supportedAlgos = [
        'aes'          => 'rijndael-128',
        'blowfish'     => 'blowfish',
        'des'          => 'des',
        '3des'         => 'tripledes',
        'tripledes'    => 'tripledes',
        'cast-128'     => 'cast-128',
        'cast-256'     => 'cast-256',
        'rijndael-128' => 'rijndael-128',
        'rijndael-192' => 'rijndael-192',
        'rijndael-256' => 'rijndael-256',
        'saferplus'    => 'saferplus',
        'serpent'      => 'serpent',
        'twofish'      => 'twofish',
    ];

    
    protected $supportedModes = [
        'cbc'  => 'cbc',
        'cfb'  => 'cfb',
        'ctr'  => 'ctr',
        'ofb'  => 'ofb',
        'nofb' => 'nofb',
        'ncfb' => 'ncfb',
    ];

    
    public function __construct($options = [])
    {
        if (PHP_VERSION_ID >= 70100) {
            trigger_error(
                'The Mcrypt extension is deprecated from PHP 7.1+. '
                . 'We suggest to use Laminas\Crypt\Symmetric\Openssl.',
                E_USER_DEPRECATED
            );
        }
        if (! extension_loaded('mcrypt')) {
            throw new Exception\RuntimeException(sprintf(
                'You cannot use %s without the Mcrypt extension',
                self::class
            ));
        }
        $this->setOptions($options);
        $this->setDefaultOptions($options);
    }

    
    public function setOptions($options)
    {
        if (! empty($options)) {
            if ($options instanceof Traversable) {
                $options = ArrayUtils::iteratorToArray($options);
            } elseif (! is_array($options)) {
                throw new Exception\InvalidArgumentException(
                    'The options parameter must be an array or a Traversable'
                );
            }
            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'algo':
                    case 'algorithm':
                        $this->setAlgorithm($value);
                        break;
                    case 'mode':
                        $this->setMode($value);
                        break;
                    case 'key':
                        $this->setKey($value);
                        break;
                    case 'iv':
                    case 'salt':
                        $this->setSalt($value);
                        break;
                    case 'padding':
                        $plugins       = static::getPaddingPluginManager();
                        $padding       = $plugins->get($value);
                        $this->padding = $padding;
                        break;
                }
            }
        }
    }

    
    protected function setDefaultOptions($options = [])
    {
        if (! isset($options['padding'])) {
            $plugins       = static::getPaddingPluginManager();
            $padding       = $plugins->get(self::DEFAULT_PADDING);
            $this->padding = $padding;
        }
    }

    
    public static function getPaddingPluginManager()
    {
        if (static::$paddingPlugins === null) {
            self::setPaddingPluginManager(new PaddingPluginManager());
        }

        return static::$paddingPlugins;
    }

    
    public static function setPaddingPluginManager($plugins)
    {
        if (is_string($plugins)) {
            if (! class_exists($plugins) || ! is_subclass_of($plugins, ContainerInterface::class)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Unable to locate padding plugin manager via class "%s"; '
                    . 'class does not exist or does not implement ContainerInterface',
                    $plugins
                ));
            }
            $plugins = new $plugins();
        }
        if (! $plugins instanceof ContainerInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Padding plugins must implements Psr\Container\ContainerInterface; received "%s"',
                is_object($plugins) ? get_class($plugins) : gettype($plugins)
            ));
        }
        static::$paddingPlugins = $plugins;
    }

    
    public function getKeySize()
    {
        return mcrypt_get_key_size($this->supportedAlgos[$this->algo], $this->supportedModes[$this->mode]);
    }

    
    public function setKey($key)
    {
        $keyLen = mb_strlen($key, '8bit');

        if (! $keyLen) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }
        $keySizes = mcrypt_module_get_supported_key_sizes($this->supportedAlgos[$this->algo]);
        $maxKey   = $this->getKeySize();

        
        if (! empty($keySizes) && $keyLen < $maxKey) {
            if (! in_array($keyLen, $keySizes)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'The size of the key must be %s bytes or longer',
                    implode(', ', $keySizes)
                ));
            }
        }
        $this->key = $key;

        return $this;
    }

    
    public function getKey()
    {
        if (empty($this->key)) {
            return;
        }
        return mb_substr($this->key, 0, $this->getKeySize(), '8bit');
    }

    
    public function setAlgorithm($algo)
    {
        if (! array_key_exists($algo, $this->supportedAlgos)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The algorithm %s is not supported by %s',
                $algo,
                self::class
            ));
        }
        $this->algo = $algo;

        return $this;
    }

    
    public function getAlgorithm()
    {
        return $this->algo;
    }

    
    public function setPadding(Padding\PaddingInterface $padding)
    {
        $this->padding = $padding;

        return $this;
    }

    
    public function getPadding()
    {
        return $this->padding;
    }

    
    public function encrypt($data)
    {
        
        if (! is_string($data) || $data === '') {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }
        if (null === $this->getKey()) {
            throw new Exception\InvalidArgumentException('No key specified for the encryption');
        }
        if (null === $this->getSalt()) {
            throw new Exception\InvalidArgumentException('The salt (IV) cannot be empty');
        }
        if (null === $this->getPadding()) {
            throw new Exception\InvalidArgumentException('You have to specify a padding method');
        }
        
        $data = $this->padding->pad($data, $this->getBlockSize());
        $iv   = $this->getSalt();
        
        $result = mcrypt_encrypt(
            $this->supportedAlgos[$this->algo],
            $this->getKey(),
            $data,
            $this->supportedModes[$this->mode],
            $iv
        );

        return $iv . $result;
    }

    
    public function decrypt($data)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }
        if (null === $this->getKey()) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }
        if (null === $this->getPadding()) {
            throw new Exception\InvalidArgumentException('You have to specify a padding method');
        }
        $iv         = mb_substr($data, 0, $this->getSaltSize(), '8bit');
        $ciphertext = mb_substr($data, $this->getSaltSize(), null, '8bit');
        $result     = mcrypt_decrypt(
            $this->supportedAlgos[$this->algo],
            $this->getKey(),
            $ciphertext,
            $this->supportedModes[$this->mode],
            $iv
        );
        
        return $this->padding->strip($result);
    }

    
    public function getSaltSize()
    {
        return mcrypt_get_iv_size($this->supportedAlgos[$this->algo], $this->supportedModes[$this->mode]);
    }

    
    public function getSupportedAlgorithms()
    {
        return array_keys($this->supportedAlgos);
    }

    
    public function setSalt($salt)
    {
        if (empty($salt)) {
            throw new Exception\InvalidArgumentException('The salt (IV) cannot be empty');
        }
        if (mb_strlen($salt, '8bit') < $this->getSaltSize()) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The size of the salt (IV) must be at least %d bytes',
                $this->getSaltSize()
            ));
        }
        $this->iv = $salt;

        return $this;
    }

    
    public function getSalt()
    {
        if (empty($this->iv)) {
            return;
        }
        if (mb_strlen($this->iv, '8bit') < $this->getSaltSize()) {
            throw new Exception\RuntimeException(sprintf(
                'The size of the salt (IV) must be at least %d bytes',
                $this->getSaltSize()
            ));
        }

        return mb_substr($this->iv, 0, $this->getSaltSize(), '8bit');
    }

    
    public function getOriginalSalt()
    {
        return $this->iv;
    }

    
    public function setMode($mode)
    {
        if (! empty($mode)) {
            $mode = strtolower($mode);
            if (! array_key_exists($mode, $this->supportedModes)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'The mode %s is not supported by %s',
                    $mode,
                    $this->algo
                ));
            }
            $this->mode = $mode;
        }

        return $this;
    }

    
    public function getMode()
    {
        return $this->mode;
    }

    
    public function getSupportedModes()
    {
        return array_keys($this->supportedModes);
    }

    
    public function getBlockSize()
    {
        return mcrypt_get_block_size($this->supportedAlgos[$this->algo], $this->supportedModes[$this->mode]);
    }
}
