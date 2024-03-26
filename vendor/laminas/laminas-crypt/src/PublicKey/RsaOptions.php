<?php

namespace Laminas\Crypt\PublicKey;

use Laminas\Crypt\PublicKey\Rsa\Exception;
use Laminas\Stdlib\AbstractOptions;

use function array_replace;
use function constant;
use function defined;
use function openssl_error_string;
use function openssl_pkey_export;
use function openssl_pkey_get_details;
use function openssl_pkey_new;
use function strtolower;
use function strtoupper;

use const OPENSSL_KEYTYPE_RSA;


class RsaOptions extends AbstractOptions
{
    
    protected $privateKey;

    
    protected $publicKey;

    
    protected $hashAlgorithm = 'sha1';

    
    protected $opensslSignatureAlgorithm;

    
    protected $passPhrase;

    
    protected $binaryOutput = true;

    
    protected $opensslPadding;

    
    public function setPrivateKey(Rsa\PrivateKey $key)
    {
        $this->privateKey = $key;
        $this->publicKey  = $this->privateKey->getPublicKey();
        return $this;
    }

    
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    
    public function setPublicKey(Rsa\PublicKey $key)
    {
        $this->publicKey = $key;
        return $this;
    }

    
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    
    public function setPassPhrase($phrase)
    {
        $this->passPhrase = (string) $phrase;
        return $this;
    }

    
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    
    public function setHashAlgorithm($hash)
    {
        $hashUpper = strtoupper($hash);
        if (! defined('OPENSSL_ALGO_' . $hashUpper)) {
            throw new Exception\InvalidArgumentException(
                "Hash algorithm '{$hash}' is not supported"
            );
        }

        $this->hashAlgorithm             = strtolower($hash);
        $this->opensslSignatureAlgorithm = constant('OPENSSL_ALGO_' . $hashUpper);
        return $this;
    }

    
    public function getHashAlgorithm()
    {
        return $this->hashAlgorithm;
    }

    
    public function getOpensslSignatureAlgorithm()
    {
        if (! isset($this->opensslSignatureAlgorithm)) {
            $this->opensslSignatureAlgorithm = constant('OPENSSL_ALGO_' . strtoupper($this->hashAlgorithm));
        }
        return $this->opensslSignatureAlgorithm;
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

    
    public function getOpensslPadding()
    {
        return $this->opensslPadding;
    }

    
    public function setOpensslPadding($opensslPadding)
    {
        $this->opensslPadding = (int) $opensslPadding;
        return $this;
    }

    
    public function generateKeys(array $opensslConfig = [])
    {
        $opensslConfig = array_replace(
            [
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
                'private_key_bits' => Rsa\PrivateKey::DEFAULT_KEY_SIZE,
                'digest_alg'       => $this->getHashAlgorithm(),
            ],
            $opensslConfig
        );

        
        $resource = openssl_pkey_new($opensslConfig);
        if (false === $resource) {
            throw new Exception\RuntimeException(
                'Can not generate keys; openssl ' . openssl_error_string()
            );
        }

        
        $passPhrase = $this->getPassPhrase();
        $result     = openssl_pkey_export($resource, $private, $passPhrase, $opensslConfig);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not export key; openssl ' . openssl_error_string()
            );
        }

        $details          = openssl_pkey_get_details($resource);
        $this->privateKey = new Rsa\PrivateKey($private, $passPhrase);
        $this->publicKey  = new Rsa\PublicKey($details['key']);

        return $this;
    }
}
