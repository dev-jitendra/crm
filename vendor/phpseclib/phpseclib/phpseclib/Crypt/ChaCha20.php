<?php



namespace phpseclib3\Crypt;

use phpseclib3\Exception\BadDecryptionException;
use phpseclib3\Exception\InsufficientSetupException;


class ChaCha20 extends Salsa20
{
    
    protected $cipher_name_openssl = 'chacha20';

    
    protected function isValidEngineHelper($engine)
    {
        switch ($engine) {
            case self::ENGINE_LIBSODIUM:
                

                
                

                
                
                return function_exists('sodium_crypto_aead_chacha20poly1305_ietf_encrypt') &&
                       $this->key_length == 32 &&
                       (($this->usePoly1305 && !isset($this->poly1305Key) && $this->counter == 0) || $this->counter == 1) &&
                       !$this->continuousBuffer;
            case self::ENGINE_OPENSSL:
                
                

                
                
                
                
                if ($this->key_length != 32) {
                    return false;
                }
        }

        return parent::isValidEngineHelper($engine);
    }

    
    public function encrypt($plaintext)
    {
        $this->setup();

        if ($this->engine == self::ENGINE_LIBSODIUM) {
            return $this->encrypt_with_libsodium($plaintext);
        }

        return parent::encrypt($plaintext);
    }

    
    public function decrypt($ciphertext)
    {
        $this->setup();

        if ($this->engine == self::ENGINE_LIBSODIUM) {
            return $this->decrypt_with_libsodium($ciphertext);
        }

        return parent::decrypt($ciphertext);
    }

    
    private function encrypt_with_libsodium($plaintext)
    {
        $params = [$plaintext, $this->aad, $this->nonce, $this->key];
        $ciphertext = strlen($this->nonce) == 8 ?
            sodium_crypto_aead_chacha20poly1305_encrypt(...$params) :
            sodium_crypto_aead_chacha20poly1305_ietf_encrypt(...$params);
        if (!$this->usePoly1305) {
            return substr($ciphertext, 0, strlen($plaintext));
        }

        $newciphertext = substr($ciphertext, 0, strlen($plaintext));

        $this->newtag = $this->usingGeneratedPoly1305Key && strlen($this->nonce) == 12 ?
            substr($ciphertext, strlen($plaintext)) :
            $this->poly1305($newciphertext);

        return $newciphertext;
    }

    
    private function decrypt_with_libsodium($ciphertext)
    {
        $params = [$ciphertext, $this->aad, $this->nonce, $this->key];

        if (isset($this->poly1305Key)) {
            if ($this->oldtag === false) {
                throw new InsufficientSetupException('Authentication Tag has not been set');
            }
            if ($this->usingGeneratedPoly1305Key && strlen($this->nonce) == 12) {
                $plaintext = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(...$params);
                $this->oldtag = false;
                if ($plaintext === false) {
                    throw new BadDecryptionException('Derived authentication tag and supplied authentication tag do not match');
                }
                return $plaintext;
            }
            $newtag = $this->poly1305($ciphertext);
            if ($this->oldtag != substr($newtag, 0, strlen($this->oldtag))) {
                $this->oldtag = false;
                throw new BadDecryptionException('Derived authentication tag and supplied authentication tag do not match');
            }
            $this->oldtag = false;
        }

        $plaintext = strlen($this->nonce) == 8 ?
            sodium_crypto_aead_chacha20poly1305_encrypt(...$params) :
            sodium_crypto_aead_chacha20poly1305_ietf_encrypt(...$params);

        return substr($plaintext, 0, strlen($ciphertext));
    }

    
    public function setNonce($nonce)
    {
        if (!is_string($nonce)) {
            throw new \UnexpectedValueException('The nonce should be a string');
        }

        
        switch (strlen($nonce)) {
            case 8:  
            case 12: 
                break;
            default:
                throw new \LengthException('Nonce of size ' . strlen($nonce) . ' not supported by this algorithm. Only 64-bit nonces or 96-bit nonces are supported');
        }

        $this->nonce = $nonce;
        $this->changed = true;
        $this->setEngine();
    }

    
    protected function setup()
    {
        if (!$this->changed) {
            return;
        }

        $this->enbuffer = $this->debuffer = ['ciphertext' => '', 'counter' => $this->counter];

        $this->changed = $this->nonIVChanged = false;

        if ($this->nonce === false) {
            throw new InsufficientSetupException('No nonce has been defined');
        }

        if ($this->key === false) {
            throw new InsufficientSetupException('No key has been defined');
        }

        if ($this->usePoly1305 && !isset($this->poly1305Key)) {
            $this->usingGeneratedPoly1305Key = true;
            if ($this->engine == self::ENGINE_LIBSODIUM) {
                return;
            }
            $this->createPoly1305Key();
        }

        $key = $this->key;
        if (strlen($key) == 16) {
            $constant = 'expand 16-byte k';
            $key .= $key;
        } else {
            $constant = 'expand 32-byte k';
        }

        $this->p1 = $constant . $key;
        $this->p2 = $this->nonce;
        if (strlen($this->nonce) == 8) {
            $this->p2 = "\0\0\0\0" . $this->p2;
        }
    }

    
    protected static function quarterRound(&$a, &$b, &$c, &$d)
    {
        
        
        
        
        $a+= $b; $d = self::leftRotate(intval($d) ^ intval($a), 16);
        $c+= $d; $b = self::leftRotate(intval($b) ^ intval($c), 12);
        $a+= $b; $d = self::leftRotate(intval($d) ^ intval($a), 8);
        $c+= $d; $b = self::leftRotate(intval($b) ^ intval($c), 7);
        
    }

    
    protected static function doubleRound(&$x0, &$x1, &$x2, &$x3, &$x4, &$x5, &$x6, &$x7, &$x8, &$x9, &$x10, &$x11, &$x12, &$x13, &$x14, &$x15)
    {
        
        static::quarterRound($x0, $x4, $x8, $x12);
        static::quarterRound($x1, $x5, $x9, $x13);
        static::quarterRound($x2, $x6, $x10, $x14);
        static::quarterRound($x3, $x7, $x11, $x15);
        
        static::quarterRound($x0, $x5, $x10, $x15);
        static::quarterRound($x1, $x6, $x11, $x12);
        static::quarterRound($x2, $x7, $x8, $x13);
        static::quarterRound($x3, $x4, $x9, $x14);
    }

    
    protected static function salsa20($x)
    {
        list(, $x0, $x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $x10, $x11, $x12, $x13, $x14, $x15) = unpack('V*', $x);
        $z0 = $x0;
        $z1 = $x1;
        $z2 = $x2;
        $z3 = $x3;
        $z4 = $x4;
        $z5 = $x5;
        $z6 = $x6;
        $z7 = $x7;
        $z8 = $x8;
        $z9 = $x9;
        $z10 = $x10;
        $z11 = $x11;
        $z12 = $x12;
        $z13 = $x13;
        $z14 = $x14;
        $z15 = $x15;

        
        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);

        
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 16);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 12);
        $x0+= $x4; $x12 = self::leftRotate(intval($x12) ^ intval($x0), 8);
        $x8+= $x12; $x4 = self::leftRotate(intval($x4) ^ intval($x8), 7);

        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 16);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 12);
        $x1+= $x5; $x13 = self::leftRotate(intval($x13) ^ intval($x1), 8);
        $x9+= $x13; $x5 = self::leftRotate(intval($x5) ^ intval($x9), 7);

        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 16);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 12);
        $x2+= $x6; $x14 = self::leftRotate(intval($x14) ^ intval($x2), 8);
        $x10+= $x14; $x6 = self::leftRotate(intval($x6) ^ intval($x10), 7);

        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 16);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 12);
        $x3+= $x7; $x15 = self::leftRotate(intval($x15) ^ intval($x3), 8);
        $x11+= $x15; $x7 = self::leftRotate(intval($x7) ^ intval($x11), 7);

        
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 16);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 12);
        $x0+= $x5; $x15 = self::leftRotate(intval($x15) ^ intval($x0), 8);
        $x10+= $x15; $x5 = self::leftRotate(intval($x5) ^ intval($x10), 7);

        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 16);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 12);
        $x1+= $x6; $x12 = self::leftRotate(intval($x12) ^ intval($x1), 8);
        $x11+= $x12; $x6 = self::leftRotate(intval($x6) ^ intval($x11), 7);

        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 16);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 12);
        $x2+= $x7; $x13 = self::leftRotate(intval($x13) ^ intval($x2), 8);
        $x8+= $x13; $x7 = self::leftRotate(intval($x7) ^ intval($x8), 7);

        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 16);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 12);
        $x3+= $x4; $x14 = self::leftRotate(intval($x14) ^ intval($x3), 8);
        $x9+= $x14; $x4 = self::leftRotate(intval($x4) ^ intval($x9), 7);
        

        $x0 += $z0;
        $x1 += $z1;
        $x2 += $z2;
        $x3 += $z3;
        $x4 += $z4;
        $x5 += $z5;
        $x6 += $z6;
        $x7 += $z7;
        $x8 += $z8;
        $x9 += $z9;
        $x10 += $z10;
        $x11 += $z11;
        $x12 += $z12;
        $x13 += $z13;
        $x14 += $z14;
        $x15 += $z15;

        return pack('V*', $x0, $x1, $x2, $x3, $x4, $x5, $x6, $x7, $x8, $x9, $x10, $x11, $x12, $x13, $x14, $x15);
    }
}
