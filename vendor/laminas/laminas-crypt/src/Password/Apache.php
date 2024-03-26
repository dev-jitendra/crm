<?php

namespace Laminas\Crypt\Password;

use Laminas\Crypt\Utils;
use Laminas\Math\Rand;
use Traversable;

use function addcslashes;
use function base64_encode;
use function chr;
use function crypt;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function mb_strlen;
use function mb_substr;
use function md5;
use function min;
use function pack;
use function preg_match;
use function sha1;
use function sprintf;
use function strpos;
use function strrev;
use function strtolower;
use function strtr;


class Apache implements PasswordInterface
{
    public const BASE64  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    public const ALPHA64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    
    protected $supportedFormat = [
        'crypt',
        'sha1',
        'md5',
        'digest',
    ];

    
    protected $format;

    
    protected $authName;

    
    protected $userName;

    
    public function __construct($options = [])
    {
        if (empty($options)) {
            return;
        }
        if (! is_array($options) && ! $options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }
        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'format':
                    $this->setFormat($value);
                    break;
                case 'authname':
                    $this->setAuthName($value);
                    break;
                case 'username':
                    $this->setUserName($value);
                    break;
            }
        }
    }

    
    public function create($password)
    {
        if (empty($this->format)) {
            throw new Exception\RuntimeException(
                'You must specify a password format'
            );
        }
        switch ($this->format) {
            case 'crypt':
                $hash = crypt($password, Rand::getString(2, self::ALPHA64));
                break;
            case 'sha1':
                $hash = '{SHA}' . base64_encode(sha1($password, true));
                break;
            case 'md5':
                $hash = $this->apr1Md5($password);
                break;
            case 'digest':
                if (empty($this->userName) || empty($this->authName)) {
                    throw new Exception\RuntimeException(
                        'You must specify UserName and AuthName (realm) to generate the digest'
                    );
                }
                $hash = md5($this->userName . ':' . $this->authName . ':' . $password);
                break;
        }

        return $hash;
    }

    
    public function verify($password, $hash)
    {
        if (mb_substr($hash, 0, 5, '8bit') === '{SHA}') {
            $hash2 = '{SHA}' . base64_encode(sha1($password, true));
            return Utils::compareStrings($hash, $hash2);
        }

        if (mb_substr($hash, 0, 6, '8bit') === '$apr1$') {
            $token = explode('$', $hash);
            if (empty($token[2])) {
                throw new Exception\InvalidArgumentException(
                    'The APR1 password format is not valid'
                );
            }
            $hash2 = $this->apr1Md5($password, $token[2]);
            return Utils::compareStrings($hash, $hash2);
        }

        $bcryptPattern = '/\$2[ay]?\$[0-9]{2}\$[' . addcslashes(static::BASE64, '+/') . '\.]{53}/';

        if (mb_strlen($hash, '8bit') > 13 && ! preg_match($bcryptPattern, $hash)) { 
            if (empty($this->userName) || empty($this->authName)) {
                throw new Exception\RuntimeException(
                    'You must specify UserName and AuthName (realm) to verify the digest'
                );
            }
            $hash2 = md5($this->userName . ':' . $this->authName . ':' . $password);
            return Utils::compareStrings($hash, $hash2);
        }

        return Utils::compareStrings($hash, crypt($password, $hash));
    }

    
    public function setFormat($format)
    {
        $format = strtolower($format);
        if (! in_array($format, $this->supportedFormat)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The format %s specified is not valid. The supported formats are: %s',
                $format,
                implode(',', $this->supportedFormat)
            ));
        }
        $this->format = $format;

        return $this;
    }

    
    public function getFormat()
    {
        return $this->format;
    }

    
    public function setAuthName($name)
    {
        $this->authName = $name;

        return $this;
    }

    
    public function getAuthName()
    {
        return $this->authName;
    }

    
    public function setUserName($name)
    {
        $this->userName = $name;

        return $this;
    }

    
    public function getUserName()
    {
        return $this->userName;
    }

    
    protected function toAlphabet64($value)
    {
        return strtr(strrev(mb_substr(base64_encode($value), 2, null, '8bit')), self::BASE64, self::ALPHA64);
    }

    
    protected function apr1Md5($password, $salt = null)
    {
        if (null === $salt) {
            $salt = Rand::getString(8, self::ALPHA64);
        } else {
            if (mb_strlen($salt, '8bit') !== 8) {
                throw new Exception\InvalidArgumentException(
                    'The salt value for APR1 algorithm must be 8 characters long'
                );
            }
            for ($i = 0; $i < 8; $i++) {
                if (strpos(self::ALPHA64, $salt[$i]) === false) {
                    throw new Exception\InvalidArgumentException(
                        'The salt value must be a string in the alphabet "./0-9A-Za-z"'
                    );
                }
            }
        }
        $len  = mb_strlen($password, '8bit');
        $text = $password . '$apr1$' . $salt;
        $bin  = pack("H32", md5($password . $salt . $password));
        for ($i = $len; $i > 0; $i -= 16) {
            $text .= mb_substr($bin, 0, min(16, $i), '8bit');
        }
        for ($i = $len; $i > 0; $i >>= 1) {
            $text .= $i & 1 ? chr(0) : $password[0];
        }
        $bin = pack("H32", md5($text));
        for ($i = 0; $i < 1000; $i++) {
            $new = $i & 1 ? $password : $bin;
            if ($i % 3) {
                $new .= $salt;
            }
            if ($i % 7) {
                $new .= $password;
            }
            $new .= $i & 1 ? $bin : $password;
            $bin  = pack("H32", md5($new));
        }
        $tmp = '';
        for ($i = 0; $i < 5; $i++) {
            $k = $i + 6;
            $j = $i + 12;
            if ($j === 16) {
                $j = 5;
            }
            $tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
        }
        $tmp = chr(0) . chr(0) . $bin[11] . $tmp;

        return '$apr1$' . $salt . '$' . $this->toAlphabet64($tmp);
    }
}
