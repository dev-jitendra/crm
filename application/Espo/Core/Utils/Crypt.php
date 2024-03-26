<?php


namespace Espo\Core\Utils;

use RuntimeException;

class Crypt
{
    private string $cryptKey;
    private ?string $key = null;
    private ?string $iv = null;

    public function __construct(Config $config)
    {
        $this->cryptKey = $config->get('cryptKey', '');
    }

    private function getKey(): string
    {
        if ($this->key === null) {
            $this->key = hash('sha256', $this->cryptKey, true);
        }

        if (!$this->key) {
            throw new RuntimeException("Could not hash the key.");
        }

        return $this->key;
    }

    private function getIv(): string
    {
        if ($this->iv === null) {
            if (!extension_loaded('openssl')) {
                throw new RuntimeException("openssl extension is not loaded.");
            }

            
            $iv = openssl_random_pseudo_bytes(16);

            $this->iv = $iv;
        }

        return $this->iv;
    }

    public function encrypt(string $string): string
    {
        $iv = $this->getIv();

        if (!extension_loaded('openssl')) {
            throw new RuntimeException("openssl extension is not loaded.");
        }

        return base64_encode(
            openssl_encrypt($string, 'aes-256-cbc', $this->getKey(), OPENSSL_RAW_DATA, $iv) . $iv
        );
    }

    public function decrypt(string $encryptedString): string
    {
        $encryptedStringDecoded = base64_decode($encryptedString);
        $string = substr($encryptedStringDecoded, 0, strlen($encryptedStringDecoded) - 16);
        $iv = substr($encryptedStringDecoded, -16);

        if (!extension_loaded('openssl')) {
            throw new RuntimeException("openssl extension is not loaded.");
        }

        $value = openssl_decrypt($string, 'aes-256-cbc', $this->getKey(), OPENSSL_RAW_DATA, $iv);

        if ($value === false) {
            throw new RuntimeException("OpenSSL decrypt failure.");
        }

        return trim($value);
    }
}
