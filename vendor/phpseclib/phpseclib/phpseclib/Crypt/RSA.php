<?php



namespace phpseclib3\Crypt;

use phpseclib3\Crypt\Common\AsymmetricKey;
use phpseclib3\Crypt\RSA\Formats\Keys\PSS;
use phpseclib3\Crypt\RSA\PrivateKey;
use phpseclib3\Crypt\RSA\PublicKey;
use phpseclib3\Exception\InconsistentSetupException;
use phpseclib3\Exception\UnsupportedAlgorithmException;
use phpseclib3\Math\BigInteger;


abstract class RSA extends AsymmetricKey
{
    
    const ALGORITHM = 'RSA';

    
    const ENCRYPTION_OAEP = 1;

    
    const ENCRYPTION_PKCS1 = 2;

    
    const ENCRYPTION_NONE = 4;

    
    const SIGNATURE_PSS = 16;

    
    const SIGNATURE_RELAXED_PKCS1 = 32;

    
    const SIGNATURE_PKCS1 = 64;

    
    protected $encryptionPadding = self::ENCRYPTION_OAEP;

    
    protected $signaturePadding = self::SIGNATURE_PSS;

    
    protected $hLen;

    
    protected $sLen;

    
    protected $label = '';

    
    protected $mgfHash;

    
    protected $mgfHLen;

    
    protected $modulus;

    
    protected $k;

    
    protected $exponent;

    
    private static $defaultExponent = 65537;

    
    protected static $enableBlinding = true;

    
    protected static $configFile;

    
    private static $smallestPrime = 4096;

    
    protected $publicExponent;

    
    public static function setExponent($val)
    {
        self::$defaultExponent = $val;
    }

    
    public static function setSmallestPrime($val)
    {
        self::$smallestPrime = $val;
    }

    
    public static function setOpenSSLConfigPath($val)
    {
        self::$configFile = $val;
    }

    
    public static function createKey($bits = 2048)
    {
        self::initialize_static_variables();

        $class = new \ReflectionClass(static::class);
        if ($class->isFinal()) {
            throw new \RuntimeException('createKey() should not be called from final classes (' . static::class . ')');
        }

        $regSize = $bits >> 1; 
        if ($regSize > self::$smallestPrime) {
            $num_primes = floor($bits / self::$smallestPrime);
            $regSize = self::$smallestPrime;
        } else {
            $num_primes = 2;
        }

        if ($num_primes == 2 && $bits >= 384 && self::$defaultExponent == 65537) {
            if (!isset(self::$engines['PHP'])) {
                self::useBestEngine();
            }

            
            if (self::$engines['OpenSSL']) {
                $config = [];
                if (self::$configFile) {
                    $config['config'] = self::$configFile;
                }
                $rsa = openssl_pkey_new(['private_key_bits' => $bits] + $config);
                openssl_pkey_export($rsa, $privatekeystr, null, $config);

                
                while (openssl_error_string() !== false) {
                }

                return RSA::load($privatekeystr);
            }
        }

        static $e;
        if (!isset($e)) {
            $e = new BigInteger(self::$defaultExponent);
        }

        $n = clone self::$one;
        $exponents = $coefficients = $primes = [];
        $lcm = [
            'top' => clone self::$one,
            'bottom' => false
        ];

        do {
            for ($i = 1; $i <= $num_primes; $i++) {
                if ($i != $num_primes) {
                    $primes[$i] = BigInteger::randomPrime($regSize);
                } else {
                    extract(BigInteger::minMaxBits($bits));
                    
                    list($min) = $min->divide($n);
                    $min = $min->add(self::$one);
                    list($max) = $max->divide($n);
                    $primes[$i] = BigInteger::randomRangePrime($min, $max);
                }

                
                
                if ($i > 2) {
                    $coefficients[$i] = $n->modInverse($primes[$i]);
                }

                $n = $n->multiply($primes[$i]);

                $temp = $primes[$i]->subtract(self::$one);

                
                
                $lcm['top'] = $lcm['top']->multiply($temp);
                $lcm['bottom'] = $lcm['bottom'] === false ? $temp : $lcm['bottom']->gcd($temp);
            }

            list($temp) = $lcm['top']->divide($lcm['bottom']);
            $gcd = $temp->gcd($e);
            $i0 = 1;
        } while (!$gcd->equals(self::$one));

        $coefficients[2] = $primes[2]->modInverse($primes[1]);

        $d = $e->modInverse($temp);

        foreach ($primes as $i => $prime) {
            $temp = $prime->subtract(self::$one);
            $exponents[$i] = $e->modInverse($temp);
        }

        
        
        
        
        
        
        
        
        
        
        
        
        
        $privatekey = new PrivateKey();
        $privatekey->modulus = $n;
        $privatekey->k = $bits >> 3;
        $privatekey->publicExponent = $e;
        $privatekey->exponent = $d;
        $privatekey->primes = $primes;
        $privatekey->exponents = $exponents;
        $privatekey->coefficients = $coefficients;

        

        return $privatekey;
    }

    
    protected static function onLoad(array $components)
    {
        $key = $components['isPublicKey'] ?
            new PublicKey() :
            new PrivateKey();

        $key->modulus = $components['modulus'];
        $key->publicExponent = $components['publicExponent'];
        $key->k = $key->modulus->getLengthInBytes();

        if ($components['isPublicKey'] || !isset($components['privateExponent'])) {
            $key->exponent = $key->publicExponent;
        } else {
            $key->privateExponent = $components['privateExponent'];
            $key->exponent = $key->privateExponent;
            $key->primes = $components['primes'];
            $key->exponents = $components['exponents'];
            $key->coefficients = $components['coefficients'];
        }

        if ($components['format'] == PSS::class) {
            
            
            
            
            
            
            
            
            if (isset($components['hash'])) {
                $key = $key->withHash($components['hash']);
            }
            if (isset($components['MGFHash'])) {
                $key = $key->withMGFHash($components['MGFHash']);
            }
            if (isset($components['saltLength'])) {
                $key = $key->withSaltLength($components['saltLength']);
            }
        }

        return $key;
    }

    
    protected static function initialize_static_variables()
    {
        if (!isset(self::$configFile)) {
            self::$configFile = dirname(__FILE__) . '/../openssl.cnf';
        }

        parent::initialize_static_variables();
    }

    
    protected function __construct()
    {
        parent::__construct();

        $this->hLen = $this->hash->getLengthInBytes();
        $this->mgfHash = new Hash('sha256');
        $this->mgfHLen = $this->mgfHash->getLengthInBytes();
    }

    
    protected function i2osp($x, $xLen)
    {
        if ($x === false) {
            return false;
        }
        $x = $x->toBytes();
        if (strlen($x) > $xLen) {
            throw new \OutOfRangeException('Resultant string length out of range');
        }
        return str_pad($x, $xLen, chr(0), STR_PAD_LEFT);
    }

    
    protected function os2ip($x)
    {
        return new BigInteger($x, 256);
    }

    
    protected function emsa_pkcs1_v1_5_encode($m, $emLen)
    {
        $h = $this->hash->hash($m);

        
        switch ($this->hash->getHash()) {
            case 'md2':
                $t = "\x30\x20\x30\x0c\x06\x08\x2a\x86\x48\x86\xf7\x0d\x02\x02\x05\x00\x04\x10";
                break;
            case 'md5':
                $t = "\x30\x20\x30\x0c\x06\x08\x2a\x86\x48\x86\xf7\x0d\x02\x05\x05\x00\x04\x10";
                break;
            case 'sha1':
                $t = "\x30\x21\x30\x09\x06\x05\x2b\x0e\x03\x02\x1a\x05\x00\x04\x14";
                break;
            case 'sha256':
                $t = "\x30\x31\x30\x0d\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x01\x05\x00\x04\x20";
                break;
            case 'sha384':
                $t = "\x30\x41\x30\x0d\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x02\x05\x00\x04\x30";
                break;
            case 'sha512':
                $t = "\x30\x51\x30\x0d\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x03\x05\x00\x04\x40";
                break;
            
            case 'sha224':
                $t = "\x30\x2d\x30\x0d\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x04\x05\x00\x04\x1c";
                break;
            case 'sha512/224':
                $t = "\x30\x2d\x30\x0d\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x05\x05\x00\x04\x1c";
                break;
            case 'sha512/256':
                $t = "\x30\x31\x30\x0d\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x06\x05\x00\x04\x20";
        }
        $t .= $h;
        $tLen = strlen($t);

        if ($emLen < $tLen + 11) {
            throw new \LengthException('Intended encoded message length too short');
        }

        $ps = str_repeat(chr(0xFF), $emLen - $tLen - 3);

        $em = "\0\1$ps\0$t";

        return $em;
    }

    
    protected function emsa_pkcs1_v1_5_encode_without_null($m, $emLen)
    {
        $h = $this->hash->hash($m);

        
        switch ($this->hash->getHash()) {
            case 'sha1':
                $t = "\x30\x1f\x30\x07\x06\x05\x2b\x0e\x03\x02\x1a\x04\x14";
                break;
            case 'sha256':
                $t = "\x30\x2f\x30\x0b\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x01\x04\x20";
                break;
            case 'sha384':
                $t = "\x30\x3f\x30\x0b\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x02\x04\x30";
                break;
            case 'sha512':
                $t = "\x30\x4f\x30\x0b\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x03\x04\x40";
                break;
            
            case 'sha224':
                $t = "\x30\x2b\x30\x0b\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x04\x04\x1c";
                break;
            case 'sha512/224':
                $t = "\x30\x2b\x30\x0b\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x05\x04\x1c";
                break;
            case 'sha512/256':
                $t = "\x30\x2f\x30\x0b\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x06\x04\x20";
                break;
            default:
                throw new UnsupportedAlgorithmException('md2 and md5 require NULLs');
        }
        $t .= $h;
        $tLen = strlen($t);

        if ($emLen < $tLen + 11) {
            throw new \LengthException('Intended encoded message length too short');
        }

        $ps = str_repeat(chr(0xFF), $emLen - $tLen - 3);

        $em = "\0\1$ps\0$t";

        return $em;
    }

    
    protected function mgf1($mgfSeed, $maskLen)
    {
        

        $t = '';
        $count = ceil($maskLen / $this->mgfHLen);
        for ($i = 0; $i < $count; $i++) {
            $c = pack('N', $i);
            $t .= $this->mgfHash->hash($mgfSeed . $c);
        }

        return substr($t, 0, $maskLen);
    }

    
    public function getLength()
    {
        return !isset($this->modulus) ? 0 : $this->modulus->getLength();
    }

    
    public function withHash($hash)
    {
        $new = clone $this;

        
        switch (strtolower($hash)) {
            case 'md2':
            case 'md5':
            case 'sha1':
            case 'sha256':
            case 'sha384':
            case 'sha512':
            case 'sha224':
            case 'sha512/224':
            case 'sha512/256':
                $new->hash = new Hash($hash);
                break;
            default:
                throw new UnsupportedAlgorithmException(
                    'The only supported hash algorithms are: md2, md5, sha1, sha256, sha384, sha512, sha224, sha512/224, sha512/256'
                );
        }
        $new->hLen = $new->hash->getLengthInBytes();

        return $new;
    }

    
    public function withMGFHash($hash)
    {
        $new = clone $this;

        
        switch (strtolower($hash)) {
            case 'md2':
            case 'md5':
            case 'sha1':
            case 'sha256':
            case 'sha384':
            case 'sha512':
            case 'sha224':
            case 'sha512/224':
            case 'sha512/256':
                $new->mgfHash = new Hash($hash);
                break;
            default:
                throw new UnsupportedAlgorithmException(
                    'The only supported hash algorithms are: md2, md5, sha1, sha256, sha384, sha512, sha224, sha512/224, sha512/256'
                );
        }
        $new->mgfHLen = $new->mgfHash->getLengthInBytes();

        return $new;
    }

    
    public function getMGFHash()
    {
        return clone $this->mgfHash;
    }

    
    public function withSaltLength($sLen)
    {
        $new = clone $this;
        $new->sLen = $sLen;
        return $new;
    }

    
    public function getSaltLength()
    {
        return $this->sLen !== null ? $this->sLen : $this->hLen;
    }

    
    public function withLabel($label)
    {
        $new = clone $this;
        $new->label = $label;
        return $new;
    }

    
    public function getLabel()
    {
        return $this->label;
    }

    
    public function withPadding($padding)
    {
        $masks = [
            self::ENCRYPTION_OAEP,
            self::ENCRYPTION_PKCS1,
            self::ENCRYPTION_NONE
        ];
        $encryptedCount = 0;
        $selected = 0;
        foreach ($masks as $mask) {
            if ($padding & $mask) {
                $selected = $mask;
                $encryptedCount++;
            }
        }
        if ($encryptedCount > 1) {
            throw new InconsistentSetupException('Multiple encryption padding modes have been selected; at most only one should be selected');
        }
        $encryptionPadding = $selected;

        $masks = [
            self::SIGNATURE_PSS,
            self::SIGNATURE_RELAXED_PKCS1,
            self::SIGNATURE_PKCS1
        ];
        $signatureCount = 0;
        $selected = 0;
        foreach ($masks as $mask) {
            if ($padding & $mask) {
                $selected = $mask;
                $signatureCount++;
            }
        }
        if ($signatureCount > 1) {
            throw new InconsistentSetupException('Multiple signature padding modes have been selected; at most only one should be selected');
        }
        $signaturePadding = $selected;

        $new = clone $this;
        if ($encryptedCount) {
            $new->encryptionPadding = $encryptionPadding;
        }
        if ($signatureCount) {
            $new->signaturePadding = $signaturePadding;
        }
        return $new;
    }

    
    public function getPadding()
    {
        return $this->signaturePadding | $this->encryptionPadding;
    }

    
    public function getEngine()
    {
        if (!isset(self::$engines['PHP'])) {
            self::useBestEngine();
        }
        return self::$engines['OpenSSL'] && self::$defaultExponent == 65537 ?
            'OpenSSL' :
            'PHP';
    }

    
    public static function enableBlinding()
    {
        static::$enableBlinding = true;
    }

    
    public static function disableBlinding()
    {
        static::$enableBlinding = false;
    }
}
