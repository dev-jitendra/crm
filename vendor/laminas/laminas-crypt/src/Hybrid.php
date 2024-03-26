<?php

namespace Laminas\Crypt;

use Laminas\Crypt\PublicKey\Rsa\PrivateKey;
use Laminas\Crypt\PublicKey\Rsa\PublicKey as PubKey;
use Laminas\Math\Rand;

use function array_search;
use function base64_decode;
use function base64_encode;
use function explode;
use function is_array;
use function is_string;
use function sprintf;


class Hybrid
{
    
    protected $bCipher;

    
    protected $rsa;

    
    public function __construct(?BlockCipher $bCipher = null, ?PublicKey\Rsa $rsa = null)
    {
        $this->bCipher = $bCipher ?? BlockCipher::factory('openssl');
        $this->rsa     = $rsa ?? new PublicKey\Rsa();
    }

    
    public function encrypt($plaintext, $keys = null)
    {
        
        $sessionKey = Rand::getBytes($this->bCipher->getCipher()->getKeySize());

        
        $this->bCipher->setKey($sessionKey);
        $ciphertext = $this->bCipher->encrypt($plaintext);

        if (! is_array($keys)) {
            $keys = ['' => $keys];
        }

        $encKeys = '';
        
        foreach ($keys as $id => $pubkey) {
            if (! $pubkey instanceof PubKey && ! is_string($pubkey)) {
                throw new Exception\RuntimeException(sprintf(
                    "The public key must be a string in PEM format or an instance of %s",
                    PubKey::class
                ));
            }
            $pubkey   = is_string($pubkey) ? new PubKey($pubkey) : $pubkey;
            $encKeys .= sprintf(
                "%s:%s:",
                base64_encode($id),
                base64_encode($this->rsa->encrypt($sessionKey, $pubkey))
            );
        }
        return $encKeys . ';' . $ciphertext;
    }

    
    public function decrypt($msg, $privateKey = null, $passPhrase = null, $id = "")
    {
        
        [$encKeys, $ciphertext] = explode(';', $msg, 2);

        $keys = explode(':', $encKeys);
        $pos  = array_search(base64_encode($id), $keys);
        if (false === $pos) {
            throw new Exception\RuntimeException(
                "This private key cannot be used for decryption"
            );
        }

        if (! $privateKey instanceof PrivateKey && ! is_string($privateKey)) {
            throw new Exception\RuntimeException(sprintf(
                "The private key must be a string in PEM format or an instance of %s",
                PrivateKey::class
            ));
        }
        $privateKey = is_string($privateKey) ? new PrivateKey($privateKey, $passPhrase) : $privateKey;

        
        $sessionKey = $this->rsa->decrypt(base64_decode($keys[$pos + 1]), $privateKey);

        
        $this->bCipher->setKey($sessionKey);
        return $this->bCipher->decrypt($ciphertext, $sessionKey);
    }

    
    public function getBlockCipherInstance()
    {
        return $this->bCipher;
    }

    
    public function getRsaInstance()
    {
        return $this->rsa;
    }
}
