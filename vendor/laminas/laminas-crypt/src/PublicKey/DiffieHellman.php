<?php

namespace Laminas\Crypt\PublicKey;

use Laminas\Crypt\Exception;
use Laminas\Crypt\Exception\InvalidArgumentException;
use Laminas\Crypt\Exception\RuntimeException;
use Laminas\Math;
use Laminas\Math\BigInteger\Adapter\AdapterInterface;

use function function_exists;
use function mb_strlen;
use function openssl_dh_compute_key;
use function openssl_error_string;
use function openssl_pkey_get_details;
use function openssl_pkey_new;
use function preg_match;

use const OPENSSL_KEYTYPE_DH;
use const PHP_VERSION_ID;


class DiffieHellman
{
    public const DEFAULT_KEY_SIZE = 2048;

    
    public const FORMAT_BINARY = 'binary';
    public const FORMAT_NUMBER = 'number';
    public const FORMAT_BTWOC  = 'btwoc';

    
    public static $useOpenssl = true;

    
    private $prime;

    
    private $generator;

    
    private $privateKey;

    
    private $math;

    
    private $publicKey;

    
    private $secretKey;

    
    protected $opensslKeyResource;

    
    public function __construct($prime, $generator, $privateKey = null, $privateKeyFormat = self::FORMAT_NUMBER)
    {
        
        $this->math = Math\BigInteger\BigInteger::factory();

        $this->setPrime($prime);
        $this->setGenerator($generator);
        if ($privateKey !== null) {
            $this->setPrivateKey($privateKey, $privateKeyFormat);
        }
    }

    
    public static function useOpensslExtension($flag = true)
    {
        static::$useOpenssl = (bool) $flag;
    }

    
    public function generateKeys()
    {
        if (function_exists('openssl_dh_compute_key') && static::$useOpenssl !== false) {
            $details = [
                'p' => $this->convert($this->getPrime(), self::FORMAT_NUMBER, self::FORMAT_BINARY),
                'g' => $this->convert($this->getGenerator(), self::FORMAT_NUMBER, self::FORMAT_BINARY),
            ];
            
            
            if ($this->hasPrivateKey() && PHP_VERSION_ID < 70100) {
                $details['priv_key'] = $this->convert(
                    $this->privateKey,
                    self::FORMAT_NUMBER,
                    self::FORMAT_BINARY
                );
                $opensslKeyResource  = openssl_pkey_new(['dh' => $details]);
            } else {
                $opensslKeyResource = openssl_pkey_new([
                    'dh'               => $details,
                    'private_key_bits' => self::DEFAULT_KEY_SIZE,
                    'private_key_type' => OPENSSL_KEYTYPE_DH,
                ]);
            }

            if (false === $opensslKeyResource) {
                throw new Exception\RuntimeException(
                    'Can not generate new key; openssl ' . openssl_error_string()
                );
            }

            $data = openssl_pkey_get_details($opensslKeyResource);

            $this->setPrivateKey($data['dh']['priv_key'], self::FORMAT_BINARY);
            $this->setPublicKey($data['dh']['pub_key'], self::FORMAT_BINARY);

            $this->opensslKeyResource = $opensslKeyResource;
        } else {
            
            $publicKey = $this->math->powmod($this->getGenerator(), $this->getPrivateKey(), $this->getPrime());
            $this->setPublicKey($publicKey);
        }

        return $this;
    }

    
    public function setPublicKey($number, $format = self::FORMAT_NUMBER)
    {
        $number = $this->convert($number, $format, self::FORMAT_NUMBER);
        if (! preg_match('/^\d+$/', $number)) {
            throw new Exception\InvalidArgumentException('Invalid parameter; not a positive natural number');
        }
        $this->publicKey = (string) $number;

        return $this;
    }

    
    public function getPublicKey($format = self::FORMAT_NUMBER)
    {
        if ($this->publicKey === null) {
            throw new Exception\InvalidArgumentException(
                'A public key has not yet been generated using a prior call to generateKeys()'
            );
        }

        return $this->convert($this->publicKey, self::FORMAT_NUMBER, $format);
    }

    
    public function computeSecretKey(
        $publicKey,
        $publicKeyFormat = self::FORMAT_NUMBER,
        $secretKeyFormat = self::FORMAT_NUMBER
    ) {
        if (function_exists('openssl_dh_compute_key') && static::$useOpenssl !== false) {
            $publicKey = $this->convert($publicKey, $publicKeyFormat, self::FORMAT_BINARY);
            $secretKey = openssl_dh_compute_key($publicKey, $this->opensslKeyResource);
            if (false === $secretKey) {
                throw new Exception\RuntimeException(
                    'Can not compute key; openssl ' . openssl_error_string()
                );
            }
            $this->secretKey = $this->convert($secretKey, self::FORMAT_BINARY, self::FORMAT_NUMBER);
        } else {
            $publicKey = $this->convert($publicKey, $publicKeyFormat, self::FORMAT_NUMBER);
            if (! preg_match('/^\d+$/', $publicKey)) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter; not a positive natural number'
                );
            }
            $this->secretKey = $this->math->powmod($publicKey, $this->getPrivateKey(), $this->getPrime());
        }

        return $this->getSharedSecretKey($secretKeyFormat);
    }

    
    public function getSharedSecretKey($format = self::FORMAT_NUMBER)
    {
        if (! isset($this->secretKey)) {
            throw new Exception\InvalidArgumentException(
                'A secret key has not yet been computed; call computeSecretKey() first'
            );
        }

        return $this->convert($this->secretKey, self::FORMAT_NUMBER, $format);
    }

    
    public function setPrime($number)
    {
        if (! preg_match('/^\d+$/', $number) || $number < 11) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter; not a positive natural number or too small: '
                . 'should be a large natural number prime'
            );
        }
        $this->prime = (string) $number;

        return $this;
    }

    
    public function getPrime($format = self::FORMAT_NUMBER)
    {
        if (! isset($this->prime)) {
            throw new Exception\InvalidArgumentException('No prime number has been set');
        }

        return $this->convert($this->prime, self::FORMAT_NUMBER, $format);
    }

    
    public function setGenerator($number)
    {
        if (! preg_match('/^\d+$/', $number) || $number < 2) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter; not a positive natural number greater than 1'
            );
        }
        $this->generator = (string) $number;

        return $this;
    }

    
    public function getGenerator($format = self::FORMAT_NUMBER)
    {
        if (! isset($this->generator)) {
            throw new Exception\InvalidArgumentException('No generator number has been set');
        }

        return $this->convert($this->generator, self::FORMAT_NUMBER, $format);
    }

    
    public function setPrivateKey($number, $format = self::FORMAT_NUMBER)
    {
        $number = $this->convert($number, $format, self::FORMAT_NUMBER);
        if (! preg_match('/^\d+$/', $number)) {
            throw new Exception\InvalidArgumentException('Invalid parameter; not a positive natural number');
        }
        $this->privateKey = (string) $number;

        return $this;
    }

    
    public function getPrivateKey($format = self::FORMAT_NUMBER)
    {
        if (! $this->hasPrivateKey()) {
            $this->setPrivateKey($this->generatePrivateKey(), self::FORMAT_BINARY);
        }

        return $this->convert($this->privateKey, self::FORMAT_NUMBER, $format);
    }

    
    public function hasPrivateKey()
    {
        return isset($this->privateKey);
    }

    
    protected function convert($number, $inputFormat = self::FORMAT_NUMBER, $outputFormat = self::FORMAT_BINARY)
    {
        if ($inputFormat === $outputFormat) {
            return $number;
        }

        
        switch ($inputFormat) {
            case self::FORMAT_BINARY:
            case self::FORMAT_BTWOC:
                $number = $this->math->binToInt($number);
                break;
            case self::FORMAT_NUMBER:
            default:
                
                break;
        }

        
        switch ($outputFormat) {
            case self::FORMAT_BINARY:
                return $this->math->intToBin($number);
            case self::FORMAT_BTWOC:
                return $this->math->intToBin($number, true);
            case self::FORMAT_NUMBER:
            default:
                return $number;
        }
    }

    
    protected function generatePrivateKey()
    {
        return Math\Rand::getBytes(mb_strlen($this->getPrime(), '8bit'));
    }
}
