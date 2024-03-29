<?php

namespace Laminas\Crypt\PublicKey\Rsa;

use function file_get_contents;
use function is_readable;
use function is_string;
use function openssl_error_string;
use function openssl_pkey_get_details;
use function openssl_pkey_get_private;
use function openssl_private_decrypt;
use function openssl_private_encrypt;

use const OPENSSL_PKCS1_OAEP_PADDING;
use const OPENSSL_PKCS1_PADDING;


class PrivateKey extends AbstractKey
{
    
    protected $publicKey;

    
    public static function fromFile($pemFile, $passPhrase = null)
    {
        if (! is_readable($pemFile)) {
            throw new Exception\InvalidArgumentException(
                "PEM file '{$pemFile}' is not readable"
            );
        }

        return new static(file_get_contents($pemFile), $passPhrase);
    }

    
    public function __construct($pemString, $passPhrase = null)
    {
        $result = openssl_pkey_get_private($pemString, $passPhrase);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Unable to load private key; openssl ' . openssl_error_string()
            );
        }

        $this->pemString          = $pemString;
        $this->opensslKeyResource = $result;
        $this->details            = openssl_pkey_get_details($this->opensslKeyResource);
    }

    
    public function getPublicKey()
    {
        if ($this->publicKey === null) {
            $this->publicKey = new PublicKey($this->details['key']);
        }

        return $this->publicKey;
    }

    
    public function encrypt($data, $padding = OPENSSL_PKCS1_PADDING)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }

        $encrypted = '';
        $result    = openssl_private_encrypt($data, $encrypted, $this->getOpensslKeyResource(), $padding);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not encrypt; openssl ' . openssl_error_string()
            );
        }

        return $encrypted;
    }

    
    public function decrypt($data, $padding = OPENSSL_PKCS1_OAEP_PADDING)
    {
        if (! is_string($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt must be a string');
        }
        if ('' === $data) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }

        $decrypted = '';
        $result    = openssl_private_decrypt($data, $decrypted, $this->getOpensslKeyResource(), $padding);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not decrypt; openssl ' . openssl_error_string()
            );
        }

        return $decrypted;
    }

    
    public function toString()
    {
        return $this->pemString;
    }
}
