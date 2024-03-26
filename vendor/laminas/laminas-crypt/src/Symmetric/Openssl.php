<?php

namespace Laminas\Crypt\Symmetric;

use Laminas\Stdlib\ArrayUtils;
use Psr\Container\ContainerInterface;
use Traversable;

use function array_push;
use function class_exists;
use function extension_loaded;
use function get_class;
use function gettype;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function is_subclass_of;
use function mb_strlen;
use function mb_substr;
use function openssl_cipher_iv_length;
use function openssl_decrypt;
use function openssl_encrypt;
use function openssl_error_string;
use function openssl_get_cipher_methods;
use function sprintf;
use function strtolower;

use const OPENSSL_RAW_DATA;
use const OPENSSL_ZERO_PADDING;
use const PHP_VERSION_ID;


class Openssl implements SymmetricInterface
{
    public const DEFAULT_PADDING = 'pkcs7';

    
    protected $key;

    
    protected $iv;

    
    protected $algo = 'aes';

    
    protected $mode = 'cbc';

    
    protected $padding;

    
    protected static $paddingPlugins;

    
    protected $encryptionAlgos = [
        'aes'      => 'aes-256',
        'blowfish' => 'bf',
        'des'      => 'des',
        'camellia' => 'camellia-256',
        'cast5'    => 'cast5',
        'seed'     => 'seed',
    ];

    
    protected $encryptionModes = [
        'cbc',
        'cfb',
        'ofb',
        'ecb',
        'ctr',
    ];

    
    protected $blockSizes = [
        'aes'      => 16,
        'blowfish' => 8,
        'des'      => 8,
        'camellia' => 16,
        'cast5'    => 8,
        'seed'     => 16,
    ];

    
    protected $keySizes = [
        'aes'      => 32,
        'blowfish' => 56,
        'des'      => 8,
        'camellia' => 32,
        'cast5'    => 16,
        'seed'     => 16,
    ];

    
    protected $opensslAlgos = [];

    
    protected $aad = '';

    
    protected $tag;

    
    protected $tagSize = 16;

    
    public $supportedAlgos;

    
    public function __construct($options = [])
    {
        if (! extension_loaded('openssl')) {
            throw new Exception\RuntimeException(sprintf(
                'You cannot use %s without the OpenSSL extension',
                self::class
            ));
        }
        
        if (PHP_VERSION_ID >= 70100) {
            array_push($this->encryptionModes, 'gcm', 'ccm');
        }
        $this->setOptions($options);
        $this->setDefaultOptions($options);
    }

    
    public function setOptions($options)
    {
        if (empty($options)) {
            return;
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (! is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }

        
        
        if (isset($options['algo'])) {
            $this->setAlgorithm($options['algo']);
        } elseif (isset($options['algorithm'])) {
            $this->setAlgorithm($options['algorithm']);
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
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
                case 'aad':
                    $this->setAad($value);
                    break;
                case 'tag_size':
                    $this->setTagSize($value);
                    break;
            }
        }
    }

    
    protected function setDefaultOptions($options = [])
    {
        if (isset($options['padding'])) {
            return;
        }

        $plugins       = static::getPaddingPluginManager();
        $padding       = $plugins->get(self::DEFAULT_PADDING);
        $this->padding = $padding;
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
                'Padding plugins must implements %s; received "%s"',
                ContainerInterface::class,
                is_object($plugins) ? get_class($plugins) : gettype($plugins)
            ));
        }

        static::$paddingPlugins = $plugins;
    }

    
    public function getKeySize()
    {
        return $this->keySizes[$this->algo];
    }

    
    public function setKey($key)
    {
        $keyLen = mb_strlen($key, '8bit');

        if (! $keyLen) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }

        if ($keyLen < $this->getKeySize()) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The size of the key must be at least of %d bytes',
                $this->getKeySize()
            ));
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
        if (! in_array($algo, $this->getSupportedAlgorithms())) {
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

    
    public function setAad($aad)
    {
        if (! $this->isAuthEncAvailable()) {
            throw new Exception\RuntimeException(
                'You need PHP 7.1+ and OpenSSL with CCM or GCM mode to use AAD'
            );
        }

        if (! $this->isCcmOrGcm()) {
            throw new Exception\RuntimeException(
                'You can set Additional Authentication Data (AAD) only for CCM or GCM mode'
            );
        }

        if (! is_string($aad)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The provided $aad must be a string, %s given',
                gettype($aad)
            ));
        }

        $this->aad = $aad;

        return $this;
    }

    
    public function getAad()
    {
        return $this->aad;
    }

    
    public function getTag()
    {
        return $this->tag;
    }

    
    public function setTagSize($size)
    {
        if (! is_int($size)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The provided $size must be an integer, %s given',
                gettype($size)
            ));
        }

        if (! $this->isAuthEncAvailable()) {
            throw new Exception\RuntimeException(
                'You need PHP 7.1+ and OpenSSL with CCM or GCM mode to set the Tag Size'
            );
        }

        if (! $this->isCcmOrGcm()) {
            throw new Exception\RuntimeException(
                'You can set the Tag Size only for CCM or GCM mode'
            );
        }

        if ($this->getMode() === 'gcm' && ($size < 4 || $size > 16)) {
            throw new Exception\InvalidArgumentException(
                'The Tag Size must be between 4 to 16 for GCM mode'
            );
        }

        $this->tagSize = $size;

        return $this;
    }

    
    public function getTagSize()
    {
        return $this->tagSize;
    }

    
    public function encrypt($data)
    {
        
        if (! is_string($data) || $data === '') {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }

        if (null === $this->getKey()) {
            throw new Exception\InvalidArgumentException('No key specified for the encryption');
        }

        if (null === $this->getSalt() && $this->getSaltSize() > 0) {
            throw new Exception\InvalidArgumentException('The salt (IV) cannot be empty');
        }

        if (null === $this->getPadding()) {
            throw new Exception\InvalidArgumentException('You must specify a padding method');
        }

        
        $data = $this->padding->pad($data, $this->getBlockSize());
        $iv   = $this->getSalt();

        
        if ($this->isCcmOrGcm()) {
            $result    = openssl_encrypt(
                $data,
                strtolower($this->encryptionAlgos[$this->algo] . '-' . $this->mode),
                $this->getKey(),
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv,
                $tag,
                $this->getAad(),
                $this->getTagSize()
            );
            $this->tag = $tag;
        } else {
            $result = openssl_encrypt(
                $data,
                strtolower($this->encryptionAlgos[$this->algo] . '-' . $this->mode),
                $this->getKey(),
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv
            );
        }

        if (false === $result) {
            $errMsg = '';
            while ($msg = openssl_error_string()) {
                $errMsg .= $msg;
            }
            throw new Exception\RuntimeException(sprintf(
                'OpenSSL error: %s',
                $errMsg
            ));
        }

        if ($this->isCcmOrGcm()) {
            return $tag . $iv . $result;
        }

        return $iv . $result;
    }

    
    public function decrypt($data)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }

        if (null === $this->getKey()) {
            throw new Exception\InvalidArgumentException('No decryption key specified');
        }
        if (null === $this->getPadding()) {
            throw new Exception\InvalidArgumentException('You must specify a padding method');
        }

        if ($this->isCcmOrGcm()) {
            $tag       = mb_substr($data, 0, $this->getTagSize(), '8bit');
            $data      = mb_substr($data, $this->getTagSize(), null, '8bit');
            $this->tag = $tag;
        }

        $iv         = mb_substr($data, 0, $this->getSaltSize(), '8bit');
        $ciphertext = mb_substr($data, $this->getSaltSize(), null, '8bit');
        $result     = $this->attemptOpensslDecrypt($ciphertext, $iv, $this->tag);

        if (false === $result) {
            $errMsg = '';

            while ($msg = openssl_error_string()) {
                $errMsg .= $msg;
            }

            throw new Exception\RuntimeException(sprintf(
                'OpenSSL error: %s',
                $errMsg
            ));
        }

        
        return $this->padding->strip($result);
    }

    
    public function getSaltSize()
    {
        return openssl_cipher_iv_length(
            $this->encryptionAlgos[$this->algo] . '-' . $this->mode
        );
    }

    
    public function getSupportedAlgorithms()
    {
        if (empty($this->supportedAlgos)) {
            foreach ($this->encryptionAlgos as $name => $algo) {
                
                if (in_array($algo . '-cbc', $this->getOpensslAlgos())) {
                    $this->supportedAlgos[] = $name;
                }
            }
        }
        return $this->supportedAlgos;
    }

    
    public function setSalt($salt)
    {
        if ($this->getSaltSize() <= 0) {
            throw new Exception\InvalidArgumentException(sprintf(
                'You cannot use a salt (IV) for %s in %s mode',
                $this->algo,
                $this->mode
            ));
        }

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
        if (empty($mode)) {
            return $this;
        }
        if (! in_array($mode, $this->getSupportedModes())) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The mode %s is not supported by %s',
                $mode,
                $this->algo
            ));
        }
        $this->mode = $mode;
        return $this;
    }

    
    public function getMode()
    {
        return $this->mode;
    }

    
    protected function getOpensslAlgos()
    {
        if (empty($this->opensslAlgos)) {
            $this->opensslAlgos = openssl_get_cipher_methods(true);
        }
        return $this->opensslAlgos;
    }

    
    public function getSupportedModes()
    {
        $modes = [];
        foreach ($this->encryptionModes as $mode) {
            $algo = $this->encryptionAlgos[$this->algo] . '-' . $mode;
            if (in_array($algo, $this->getOpensslAlgos())) {
                $modes[] = $mode;
            }
        }
        return $modes;
    }

    
    public function getBlockSize()
    {
        return $this->blockSizes[$this->algo];
    }

    
    public function isAuthEncAvailable()
    {
        
        $ccm = in_array('aes-256-ccm', $this->getOpensslAlgos());
        
        $gcm = in_array('aes-256-gcm', $this->getOpensslAlgos());

        return PHP_VERSION_ID >= 70100 && ($ccm || $gcm);
    }

    
    private function isCcmOrGcm()
    {
        return in_array(strtolower($this->mode), ['gcm', 'ccm'], true);
    }

    
    private function attemptOpensslDecrypt($cipherText, $iv, $tag)
    {
        if ($this->isCcmOrGcm()) {
            return openssl_decrypt(
                $cipherText,
                strtolower($this->encryptionAlgos[$this->algo] . '-' . $this->mode),
                $this->getKey(),
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv,
                $tag,
                $this->getAad()
            );
        }

        return openssl_decrypt(
            $cipherText,
            strtolower($this->encryptionAlgos[$this->algo] . '-' . $this->mode),
            $this->getKey(),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
    }
}
