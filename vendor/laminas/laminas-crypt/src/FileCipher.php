<?php

namespace Laminas\Crypt;

use Laminas\Crypt\Key\Derivation\Pbkdf2;
use Laminas\Math\Rand;

use function fclose;
use function file_exists;
use function filesize;
use function fopen;
use function fread;
use function fseek;
use function fwrite;
use function mb_strlen;
use function mb_substr;
use function sprintf;
use function str_repeat;
use function unlink;


class FileCipher
{
    public const BUFFER_SIZE = 1048576; 

    
    protected $pbkdf2Hash = 'sha256';

    
    protected $hash = 'sha256';

    
    protected $keyIteration = 10000;

    
    protected $key;

    
    protected $cipher;

    
    public function __construct(?Symmetric\SymmetricInterface $cipher = null)
    {
        if (null === $cipher) {
            $cipher = new Symmetric\Openssl();
        }
        $this->cipher = $cipher;
    }

    
    public function setCipher(Symmetric\SymmetricInterface $cipher)
    {
        $this->cipher = $cipher;
    }

    
    public function getCipher()
    {
        return $this->cipher;
    }

    
    public function setKeyIteration($num)
    {
        $this->keyIteration = (int) $num;
    }

    
    public function getKeyIteration()
    {
        return $this->keyIteration;
    }

    
    public function setKey($key)
    {
        if (empty($key)) {
            throw new Exception\InvalidArgumentException('The key cannot be empty');
        }
        $this->key = (string) $key;
    }

    
    public function getKey()
    {
        return $this->key;
    }

    
    public function setCipherAlgorithm($algo)
    {
        $this->cipher->setAlgorithm($algo);
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
        $this->hash = (string) $hash;
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
        $this->pbkdf2Hash = (string) $hash;
    }

    
    public function getPbkdf2HashAlgorithm()
    {
        return $this->pbkdf2Hash;
    }

    
    public function encrypt($fileIn, $fileOut)
    {
        $this->checkFileInOut($fileIn, $fileOut);
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for encryption');
        }

        $read    = fopen($fileIn, "r");
        $write   = fopen($fileOut, "w");
        $iv      = Rand::getBytes($this->cipher->getSaltSize());
        $keys    = Pbkdf2::calc(
            $this->getPbkdf2HashAlgorithm(),
            $this->getKey(),
            $iv,
            $this->getKeyIteration(),
            $this->cipher->getKeySize() * 2
        );
        $hmac    = '';
        $size    = 0;
        $tot     = filesize($fileIn);
        $padding = $this->cipher->getPadding();

        $this->cipher->setKey(mb_substr($keys, 0, $this->cipher->getKeySize(), '8bit'));
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding());
        $this->cipher->setSalt($iv);
        $this->cipher->setMode('cbc');

        $hashAlgo  = $this->getHashAlgorithm();
        $saltSize  = $this->cipher->getSaltSize();
        $algorithm = $this->cipher->getAlgorithm();
        $keyHmac   = mb_substr($keys, $this->cipher->getKeySize(), null, '8bit');

        while ($data = fread($read, self::BUFFER_SIZE)) {
            $size += mb_strlen($data, '8bit');
            
            if ($size === $tot) {
                $this->cipher->setPadding($padding);
            }
            $result = $this->cipher->encrypt($data);
            if ($size <= self::BUFFER_SIZE) {
                
                fwrite($write, str_repeat(0, Hmac::getOutputSize($hashAlgo)));
            } else {
                $result = mb_substr($result, $saltSize, null, '8bit');
            }
            $hmac = Hmac::compute(
                $keyHmac,
                $hashAlgo,
                $algorithm . $hmac . $result
            );
            $this->cipher->setSalt(mb_substr($result, -1 * $saltSize, null, '8bit'));
            if (fwrite($write, $result) !== mb_strlen($result, '8bit')) {
                return false;
            }
        }
        $result = true;
        
        fseek($write, 0);
        if (fwrite($write, $hmac) !== mb_strlen($hmac, '8bit')) {
            $result = false;
        }
        fclose($write);
        fclose($read);

        return $result;
    }

    
    public function decrypt($fileIn, $fileOut)
    {
        $this->checkFileInOut($fileIn, $fileOut);
        if (empty($this->key)) {
            throw new Exception\InvalidArgumentException('No key specified for decryption');
        }

        $read     = fopen($fileIn, "r");
        $write    = fopen($fileOut, "w");
        $hmacRead = fread($read, Hmac::getOutputSize($this->getHashAlgorithm()));
        $iv       = fread($read, $this->cipher->getSaltSize());
        $tot      = filesize($fileIn);
        $hmac     = $iv;
        $size     = mb_strlen($iv, '8bit') + mb_strlen($hmacRead, '8bit');
        $keys     = Pbkdf2::calc(
            $this->getPbkdf2HashAlgorithm(),
            $this->getKey(),
            $iv,
            $this->getKeyIteration(),
            $this->cipher->getKeySize() * 2
        );
        $padding  = $this->cipher->getPadding();
        $this->cipher->setPadding(new Symmetric\Padding\NoPadding());
        $this->cipher->setKey(mb_substr($keys, 0, $this->cipher->getKeySize(), '8bit'));
        $this->cipher->setMode('cbc');

        $blockSize = $this->cipher->getBlockSize();
        $hashAlgo  = $this->getHashAlgorithm();
        $algorithm = $this->cipher->getAlgorithm();
        $saltSize  = $this->cipher->getSaltSize();
        $keyHmac   = mb_substr($keys, $this->cipher->getKeySize(), null, '8bit');

        while ($data = fread($read, self::BUFFER_SIZE)) {
            $size += mb_strlen($data, '8bit');
            
            if ($size + $blockSize >= $tot) {
                $this->cipher->setPadding($padding);
                $data .= fread($read, $blockSize);
            }
            $result = $this->cipher->decrypt($iv . $data);
            $hmac   = Hmac::compute(
                $keyHmac,
                $hashAlgo,
                $algorithm . $hmac . $data
            );
            $iv     = mb_substr($data, -1 * $saltSize, null, '8bit');
            if (fwrite($write, $result) !== mb_strlen($result, '8bit')) {
                return false;
            }
        }
        fclose($write);
        fclose($read);

        
        if (! Utils::compareStrings($hmac, $hmacRead)) {
            unlink($fileOut);
            return false;
        }

        return true;
    }

    
    protected function checkFileInOut($fileIn, $fileOut)
    {
        if (! file_exists($fileIn)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'I cannot open the %s file',
                $fileIn
            ));
        }
        if (file_exists($fileOut)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The file %s already exists',
                $fileOut
            ));
        }
    }
}
