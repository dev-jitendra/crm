<?php

namespace Laminas\Crypt\PublicKey;

use Laminas\Crypt\PublicKey\Rsa\Exception;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function base64_decode;
use function base64_encode;
use function extension_loaded;
use function is_array;
use function is_file;
use function is_string;
use function openssl_error_string;
use function openssl_sign;
use function openssl_verify;
use function trim;


class Rsa
{
    public const MODE_AUTO   = 1;
    public const MODE_BASE64 = 2;
    public const MODE_RAW    = 3;

    
    protected $options;

    
    public static function factory($options)
    {
        if (! extension_loaded('openssl')) {
            throw new Exception\RuntimeException(
                'Can not create Laminas\Crypt\PublicKey\Rsa; openssl extension needs to be loaded'
            );
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (! is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }

        $privateKey = null;
        $passPhrase = $options['pass_phrase'] ?? null;
        if (isset($options['private_key'])) {
            if (is_file($options['private_key'])) {
                $privateKey = Rsa\PrivateKey::fromFile($options['private_key'], $passPhrase);
            } elseif (is_string($options['private_key'])) {
                $privateKey = new Rsa\PrivateKey($options['private_key'], $passPhrase);
            } else {
                throw new Exception\InvalidArgumentException(
                    'Parameter "private_key" must be PEM formatted string or path to key file'
                );
            }
            unset($options['private_key']);
        }

        $publicKey = null;
        if (isset($options['public_key'])) {
            if (is_file($options['public_key'])) {
                $publicKey = Rsa\PublicKey::fromFile($options['public_key']);
            } elseif (is_string($options['public_key'])) {
                $publicKey = new Rsa\PublicKey($options['public_key']);
            } else {
                throw new Exception\InvalidArgumentException(
                    'Parameter "public_key" must be PEM/certificate string or path to key/certificate file'
                );
            }
            unset($options['public_key']);
        }

        $options = new RsaOptions($options);
        if ($privateKey instanceof Rsa\PrivateKey) {
            $options->setPrivateKey($privateKey);
        }
        if ($publicKey instanceof Rsa\PublicKey) {
            $options->setPublicKey($publicKey);
        }

        return new Rsa($options);
    }

    
    public function __construct(?RsaOptions $options = null)
    {
        if (! extension_loaded('openssl')) {
            throw new Exception\RuntimeException(
                'Laminas\Crypt\PublicKey\Rsa requires openssl extension to be loaded'
            );
        }

        if ($options === null) {
            $this->options = new RsaOptions();
        } else {
            $this->options = $options;
        }
    }

    
    public function setOptions(RsaOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    
    public function getOptions()
    {
        return $this->options;
    }

    
    public function getOpensslErrorString()
    {
        $message = '';
        while (false !== ($error = openssl_error_string())) {
            $message .= $error . "\n";
        }
        return trim($message);
    }

    
    public function sign($data, ?Rsa\PrivateKey $privateKey = null)
    {
        $signature = '';
        if (null === $privateKey) {
            $privateKey = $this->options->getPrivateKey();
        }

        $result = openssl_sign(
            $data,
            $signature,
            $privateKey->getOpensslKeyResource(),
            $this->options->getOpensslSignatureAlgorithm()
        );
        if (false === $result) {
            throw new Exception\RuntimeException(
                'Can not generate signature; openssl ' . $this->getOpensslErrorString()
            );
        }

        if ($this->options->getBinaryOutput()) {
            return $signature;
        }

        return base64_encode($signature);
    }

    
    public function verify(
        $data,
        $signature,
        ?Rsa\PublicKey $publicKey = null,
        $mode = self::MODE_AUTO
    ) {
        if (null === $publicKey) {
            $publicKey = $this->options->getPublicKey();
        }

        switch ($mode) {
            case self::MODE_AUTO:
                
                $output = base64_decode($signature, true);
                if ((false !== $output) && ($signature === base64_encode($output))) {
                    $signature = $output;
                }
                break;
            case self::MODE_BASE64:
                $signature = base64_decode($signature);
                break;
            case self::MODE_RAW:
            default:
                break;
        }

        $result = openssl_verify(
            $data,
            $signature,
            $publicKey->getOpensslKeyResource(),
            $this->options->getOpensslSignatureAlgorithm()
        );
        if (-1 === $result) {
            throw new Exception\RuntimeException(
                'Can not verify signature; openssl ' . $this->getOpensslErrorString()
            );
        }

        return $result === 1;
    }

    
    public function encrypt($data, ?Rsa\AbstractKey $key = null)
    {
        if (null === $key) {
            $key = $this->options->getPublicKey();
        }

        if (null === $key) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }

        $padding = $this->getOptions()->getOpensslPadding();
        if (null === $padding) {
            $encrypted = $key->encrypt($data);
        } else {
            $encrypted = $key->encrypt($data, $padding);
        }

        if ($this->options->getBinaryOutput()) {
            return $encrypted;
        }

        return base64_encode($encrypted);
    }

    
    public function decrypt(
        $data,
        ?Rsa\AbstractKey $key = null,
        $mode = self::MODE_AUTO
    ) {
        if (null === $key) {
            $key = $this->options->getPrivateKey();
        }

        if (null === $key) {
            throw new Exception\InvalidArgumentException('No key specified for the decryption');
        }

        switch ($mode) {
            case self::MODE_AUTO:
                
                $output = base64_decode($data, true);
                if ((false !== $output) && ($data === base64_encode($output))) {
                    $data = $output;
                }
                break;
            case self::MODE_BASE64:
                $data = base64_decode($data);
                break;
            case self::MODE_RAW:
            default:
                break;
        }

        $padding = $this->getOptions()->getOpensslPadding();
        if (null === $padding) {
            return $key->decrypt($data);
        } else {
            return $key->decrypt($data, $padding);
        }
    }

    
    public function generateKeys(array $opensslConfig = [])
    {
        $this->options->generateKeys($opensslConfig);
        return $this;
    }
}
