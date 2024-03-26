<?php

namespace Laminas\Crypt\PublicKey\Rsa;

use function file_get_contents;
use function is_readable;
use function is_string;
use function openssl_error_string;
use function openssl_pkey_get_details;
use function openssl_pkey_get_public;
use function openssl_public_decrypt;
use function openssl_public_encrypt;
use function strpos;

use const OPENSSL_PKCS1_OAEP_PADDING;
use const OPENSSL_PKCS1_PADDING;


class PublicKey extends AbstractKey
{
    public const CERT_START = '-----BEGIN CERTIFICATE-----';

    
    protected $certificateString;

    
    public static function fromFile($pemOrCertificateFile)
    {
        if (! is_readable($pemOrCertificateFile)) {
            throw new Exception\InvalidArgumentException(
                "File '{$pemOrCertificateFile}' is not readable"
            );
        }

        return new static(file_get_contents($pemOrCertificateFile));
    }

    
    public function __construct($pemStringOrCertificate)
    {
        $result = openssl_pkey_get_public($pemStringOrCertificate);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Unable to load public key; openssl ' . openssl_error_string()
            );
        }

        if (strpos($pemStringOrCertificate, self::CERT_START) !== false) {
            $this->certificateString = $pemStringOrCertificate;
        } else {
            $this->pemString = $pemStringOrCertificate;
        }

        $this->opensslKeyResource = $result;
        $this->details            = openssl_pkey_get_details($this->opensslKeyResource);
    }

    
    public function encrypt($data, $padding = OPENSSL_PKCS1_OAEP_PADDING)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The data to encrypt cannot be empty');
        }

        $encrypted = '';
        $result    = openssl_public_encrypt($data, $encrypted, $this->getOpensslKeyResource(), $padding);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not encrypt; openssl ' . openssl_error_string()
            );
        }

        return $encrypted;
    }

    
    public function decrypt($data, $padding = OPENSSL_PKCS1_PADDING)
    {
        if (! is_string($data)) {
            throw new Exception\InvalidArgumentException('The data to decrypt must be a string');
        }
        if ('' === $data) {
            throw new Exception\InvalidArgumentException('The data to decrypt cannot be empty');
        }

        $decrypted = '';
        $result    = openssl_public_decrypt($data, $decrypted, $this->getOpensslKeyResource(), $padding);
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not decrypt; openssl ' . openssl_error_string()
            );
        }

        return $decrypted;
    }

    
    public function getCertificate()
    {
        return $this->certificateString;
    }

    
    public function toString()
    {
        if (! empty($this->certificateString)) {
            return $this->certificateString;
        } elseif (! empty($this->pemString)) {
            return $this->pemString;
        }
        throw new Exception\RuntimeException('No public key string representation is available');
    }
}
