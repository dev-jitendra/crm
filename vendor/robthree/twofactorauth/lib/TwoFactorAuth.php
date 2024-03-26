<?php
namespace RobThree\Auth;

use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use RobThree\Auth\Providers\Rng\IRNGProvider;
use RobThree\Auth\Providers\Time\ITimeProvider;



class TwoFactorAuth
{
    private $algorithm;
    private $period;
    private $digits;
    private $issuer;
    private $qrcodeprovider = null;
    private $rngprovider = null;
    private $timeprovider = null;
    private static $_base32dict = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';
    private static $_base32;
    private static $_base32lookup = array();
    private static $_supportedalgos = array('sha1', 'sha256', 'sha512', 'md5');

    function __construct($issuer = null, $digits = 6, $period = 30, $algorithm = 'sha1', IQRCodeProvider $qrcodeprovider = null, IRNGProvider $rngprovider = null, ITimeProvider $timeprovider = null)
    {
        $this->issuer = $issuer;
        if (!is_int($digits) || $digits <= 0)
            throw new TwoFactorAuthException('Digits must be int > 0');
        $this->digits = $digits;

        if (!is_int($period) || $period <= 0)
            throw new TwoFactorAuthException('Period must be int > 0');
        $this->period = $period;

        $algorithm = strtolower(trim($algorithm));
        if (!in_array($algorithm, self::$_supportedalgos))
            throw new TwoFactorAuthException('Unsupported algorithm: ' . $algorithm);
        $this->algorithm = $algorithm;
        $this->qrcodeprovider = $qrcodeprovider;
        $this->rngprovider = $rngprovider;
        $this->timeprovider = $timeprovider;

        self::$_base32 = str_split(self::$_base32dict);
        self::$_base32lookup = array_flip(self::$_base32);
    }

    
    public function createSecret($bits = 80, $requirecryptosecure = true)
    {
        $secret = '';
        $bytes = ceil($bits / 5);   
        $rngprovider = $this->getRngprovider();
        if ($requirecryptosecure && !$rngprovider->isCryptographicallySecure())
            throw new TwoFactorAuthException('RNG provider is not cryptographically secure');
        $rnd = $rngprovider->getRandomBytes($bytes);
        for ($i = 0; $i < $bytes; $i++)
            $secret .= self::$_base32[ord($rnd[$i]) & 31];  
        return $secret;
    }

    
    public function getCode($secret, $time = null)
    {
        $secretkey = $this->base32Decode($secret);

        $timestamp = "\0\0\0\0" . pack('N*', $this->getTimeSlice($this->getTime($time)));  
        $hashhmac = hash_hmac($this->algorithm, $timestamp, $secretkey, true);             
        $hashpart = substr($hashhmac, ord(substr($hashhmac, -1)) & 0x0F, 4);               
        $value = unpack('N', $hashpart);                                                   
        $value = $value[1] & 0x7FFFFFFF;                                                   

        return str_pad($value % pow(10, $this->digits), $this->digits, '0', STR_PAD_LEFT);
    }

    
    public function verifyCode($secret, $code, $discrepancy = 1, $time = null, &$timeslice = 0)
    {
        $timetamp = $this->getTime($time);

        $timeslice = 0;

        
        
        
        
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $ts = $timetamp + ($i * $this->period);
            $slice = $this->getTimeSlice($ts);
            $timeslice = $this->codeEquals($this->getCode($secret, $ts), $code) ? $slice : $timeslice;
        }

        return $timeslice > 0;
    }

    
    private function codeEquals($safe, $user) {
        if (function_exists('hash_equals')) {
            return hash_equals($safe, $user);
        }
        
        
        if (strlen($safe)===strlen($user)) {
            $result = 0;
            for ($i = 0; $i < strlen($safe); $i++)
                $result |= (ord($safe[$i]) ^ ord($user[$i]));
            return $result === 0;
        }
        return false;
    }

    
    public function getQRCodeImageAsDataUri($label, $secret, $size = 200)
    {
        if (!is_int($size) || $size <= 0)
            throw new TwoFactorAuthException('Size must be int > 0');

        $qrcodeprovider = $this->getQrCodeProvider();
        return 'data:'
            . $qrcodeprovider->getMimeType()
            . ';base64,'
            . base64_encode($qrcodeprovider->getQRCodeImage($this->getQRText($label, $secret), $size));
    }

    
    public function ensureCorrectTime(array $timeproviders = null, $leniency = 5)
    {
        if ($timeproviders != null && !is_array($timeproviders))
            throw new TwoFactorAuthException('No timeproviders specified');

        if ($timeproviders == null)
            $timeproviders = array(
                new Providers\Time\NTPTimeProvider(),
                new Providers\Time\HttpTimeProvider()
            );

        
        $timeprovider = $this->getTimeProvider();

        
        foreach ($timeproviders as $t) {
            if (!($t instanceof ITimeProvider))
                throw new TwoFactorAuthException('Object does not implement ITimeProvider');

            
            if (abs($timeprovider->getTime() - $t->getTime()) > $leniency)
                throw new TwoFactorAuthException(sprintf('Time for timeprovider is off by more than %d seconds when compared to %s', $leniency, get_class($t)));
        }
    }

    private function getTime($time)
    {
        return ($time === null) ? $this->getTimeProvider()->getTime() : $time;
    }

    private function getTimeSlice($time = null, $offset = 0)
    {
        return (int)floor($time / $this->period) + ($offset * $this->period);
    }

    
    public function getQRText($label, $secret)
    {
        return 'otpauth:
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($this->issuer)
            . '&period=' . intval($this->period)
            . '&algorithm=' . rawurlencode(strtoupper($this->algorithm))
            . '&digits=' . intval($this->digits);
    }

    private function base32Decode($value)
    {
        if (strlen($value)==0) return '';

        if (preg_match('/[^'.preg_quote(self::$_base32dict).']/', $value) !== 0)
            throw new TwoFactorAuthException('Invalid base32 string');

        $buffer = '';
        foreach (str_split($value) as $char)
        {
            if ($char !== '=')
                $buffer .= str_pad(decbin(self::$_base32lookup[$char]), 5, 0, STR_PAD_LEFT);
        }
        $length = strlen($buffer);
        $blocks = trim(chunk_split(substr($buffer, 0, $length - ($length % 8)), 8, ' '));

        $output = '';
        foreach (explode(' ', $blocks) as $block)
            $output .= chr(bindec(str_pad($block, 8, 0, STR_PAD_RIGHT)));
        return $output;
    }

    
    public function getQrCodeProvider()
    {
        
        if (null === $this->qrcodeprovider) {
            return $this->qrcodeprovider = new Providers\Qr\QRServerProvider();
        }
        return $this->qrcodeprovider;
    }

    
    public function getRngprovider()
    {
        if (null !== $this->rngprovider) {
            return $this->rngprovider;
        }
        if (function_exists('random_bytes')) {
            return $this->rngprovider = new Providers\Rng\CSRNGProvider();
        }
        if (function_exists('mcrypt_create_iv')) {
            return $this->rngprovider = new Providers\Rng\MCryptRNGProvider();
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return $this->rngprovider = new Providers\Rng\OpenSSLRNGProvider();
        }
        if (function_exists('hash')) {
            return $this->rngprovider = new Providers\Rng\HashRNGProvider();
        }
        throw new TwoFactorAuthException('Unable to find a suited RNGProvider');
    }

    
    public function getTimeProvider()
    {
        
        if (null === $this->timeprovider) {
            return $this->timeprovider = new Providers\Time\LocalMachineTimeProvider();
        }
        return $this->timeprovider;
    }
}