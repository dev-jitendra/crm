<?php

namespace Laminas\Crypt;

use Laminas\Crypt\Key\Derivation\Pbkdf2;
use Laminas\Crypt\Symmetric\SymmetricInterface;
use Laminas\Math\Rand;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface as NotFoundException;

use function base64_decode;
use function base64_encode;
use function class_exists;
use function get_class;
use function gettype;
use function in_array;
use function is_array;
use function is_object;
use function is_string;
use function is_subclass_of;
use function mb_substr;
use function sprintf;


class BlockCipher
{
    
    protected $pbkdf2Hash = 'sha256';

    
    protected $cipher;

    
    protected static $symmetricPlugins;

    
    protected $hash = 'sha256';

    
    protected $saltSetted = false;

    
    protected $binaryOutput = false;

    
    protected $keyIteration = 5000;

    
    protected $key;

    
    public function __construct(SymmetricInterface $cipher)
    {
        $this->cipher = $cipher;
    }

    
    public static function factory($adapter, $options = [])
    {
        $plugins = static::getSymmetricPluginManager();
        try {
            $cipher = $plugins->get($adapter);
        } catch (NotFoundException $e) {
            throw new Exception\RuntimeException(sprintf(
                'The symmetric adapter %s does not exist',
                $adapter
            ));
        }
        $cipher->setOptions($options);
        return new static($cipher);
    }

    
    public static function getSymmetricPluginManager()
    {
        if (static::$symmetricPlugins === null) {
            static::setSymmetricPluginManager(new SymmetricPluginManager());
        }

        return static::$symmetricPlugins;
    }

    
    public static function setSymmetricPluginManager($plugins)
    {
        if (is_string($plugins)) {
            if (! class_exists($plugins) || ! is_subclass_of($plugins, ContainerInterface::class)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Unable to locate symmetric cipher plugins using class "%s"; '
                    . 'class does not exist or does not implement ContainerInterface',
                    $plugins
                ));
            }
            $plugins = new $plugins();
        }
        if (! $plugins instanceof ContainerInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Symmetric plugin must implements Interop\Container\ContainerInterface;; received "%s"',
                is_object($plugins) ? get_class($plugins) : gettype($plugins)
            ));
        }
        static::$symmetricPlugins = $plugins;
    }

    
    public function setCipher(SymmetricInterface $cipher)
    {
        $this->cipher = $cipher;
        return $this;
    }

    
    public function getCipher()
    {
        return $this->cipher;
    }

    
    public function setKeyIteration($num)
    {
        $this->keyIteration = (int) $num;

        return $this;
    }

    
    public function getKeyIteration()
    {
        return $this->keyIteration;
    }

    
    public function setSalt($salt)
    {
        try {
            $this->cipher->setSalt($salt);
        } catch (Symmetric\Exception\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException("The salt is not valid: " . $e->getMessage());
        }
        $this->saltSetted = true;

        return $this;
    }

    
    public function getSalt()
    {
        return $this->cipher->getSalt();
    }

    
    public function getOriginalSalt()
    {
        return $this->cipher->getOriginalSalt();
    }

    
    public function setBinaryOutput($value)
    {
        $this->binaryOutput = (bool) $value;

        return $this;
    }

    
    public function getBinaryOutput()
    {
        return $this->binaryOutput;
    }

    
    public function setKey($key)
    {
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }
        $this->key = $key;

        return $this;
    }

    
    public function getKey()
    {
        return $this->key;
    }

    
    public function setCipherAlgorithm($algo)
    {
        try {
            $this->cipher->setAlgorithm($algo);
        } catch (Symmetric\Exception\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage());
        }

        return $this;
    }

    
    public function getCipherAlgorithm()
    {
        return $this->cipher->getAlgorithm();
    }

    
    public function getCipherSupportedAlgorithms()
    {
        return $this->cipher->getSupportedAlgorithms();
    }

    
    public function setHashAlgorithm($hash)
    {
        if (! Hash::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "The specified hash algorithm '{$hash}' is not supported by Laminas\Crypt\Hash"
            );
        }
        $this->hash = $hash;

        return $this;
    }

    
    public function getHashAlgorithm()
    {
        return $this->hash;
    }

    
    public function setPbkdf2HashAlgorithm($hash)
    {
        if (! Hash::isSupported($hash)) {
            throw new Exception\InvalidArgumentException(
                "The specified hash algorithm '{$hash}' is not supported by Laminas\Crypt\Hash"
            );
        }
        $this->pbkdf2Hash = $hash;

        return $this;
    }

    
    public function getPbkdf2HashAlgorithm()
    {
        return $this->pbkdf2Hash;
    }

    
    public function encrypt($data)
    {
        
        
        if (
            (is_string($data) && $data === '')
            || is_array($data)
            || is_object($data)
        ) {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }

        
        if (! is_string($data)) {
            $data = (string) $data;
        }

        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for the encryption');
        }
        $keySize = $this->cipher->getKeySize();
        
        if (! $this->saltSetted) {
            $this->cipher->setSalt(Rand::getBytes($this->cipher->getSaltSize()));
        }

        if (in_array($this->cipher->getMode(), ['ccm', 'gcm'], true)) {
            return $this->encryptViaCcmOrGcm($data, $keySize);
        }

        
        $hash = Pbkdf2::calc(
            $this->getPbkdf2HashAlgorithm(),
            $this->getKey(),
            $this->getSalt(),
            $this->keyIteration,
            $keySize * 2
        );
        
        $this->cipher->setKey(mb_substr($hash, 0, $keySize, '8bit'));
        
        $keyHmac = mb_substr($hash, $keySize, null, '8bit');
        
        $ciphertext = $this->cipher->encrypt($data);
        
        $hmac = Hmac::compute($keyHmac, $this->hash, $this->cipher->getAlgorithm() . $ciphertext);

        return $this->binaryOutput ? $hmac . $ciphertext : $hmac . base64_encode($ciphertext);
    }

    
    public function decrypt($data)
    {
        if (! is_string($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt must be a string');
        }
        if ('' === $data) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }

        $keySize = $this->cipher->getKeySize();

        if (in_array($this->cipher->getMode(), ['ccm', 'gcm'], true)) {
            return $this->decryptViaCcmOrGcm($data, $keySize);
        }

        $hmacSize   = Hmac::getOutputSize($this->hash);
        $hmac       = mb_substr($data, 0, $hmacSize, '8bit');
        $ciphertext = mb_substr($data, $hmacSize, null, '8bit') ?: '';
        if (! $this->binaryOutput) {
            $ciphertext = base64_decode($ciphertext);
        }
        $iv = mb_substr($ciphertext, 0, $this->cipher->getSaltSize(), '8bit');
        
        $hash = Pbkdf2::calc(
            $this->getPbkdf2HashAlgorithm(),
            $this->getKey(),
            $iv,
            $this->keyIteration,
            $keySize * 2
        );
        
        $this->cipher->setKey(mb_substr($hash, 0, $keySize, '8bit'));
        
        $keyHmac = mb_substr($hash, $keySize, null, '8bit');
        $hmacNew = Hmac::compute($keyHmac, $this->hash, $this->cipher->getAlgorithm() . $ciphertext);
        if (! Utils::compareStrings($hmacNew, $hmac)) {
            return false;
        }

        return $this->cipher->decrypt($ciphertext);
    }

    
    private function encryptViaCcmOrGcm($data, $keySize)
    {
        $this->cipher->setKey(Pbkdf2::calc(
            $this->getPbkdf2HashAlgorithm(),
            $this->getKey(),
            $this->getSalt(),
            $this->keyIteration,
            $keySize
        ));

        $cipherText = $this->cipher->encrypt($data);

        return $this->binaryOutput ? $cipherText : base64_encode($cipherText);
    }

    
    private function decryptViaCcmOrGcm($data, $keySize)
    {
        $cipherText = $this->binaryOutput ? $data : base64_decode($data);
        $iv         = mb_substr($cipherText, $this->cipher->getTagSize(), $this->cipher->getSaltSize(), '8bit');

        $this->cipher->setKey(Pbkdf2::calc(
            $this->getPbkdf2HashAlgorithm(),
            $this->getKey(),
            $iv,
            $this->keyIteration,
            $keySize
        ));

        return $this->cipher->decrypt($cipherText);
    }
}
