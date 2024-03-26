<?php

namespace Laminas\Validator;

use Traversable;
use UConverter;

use function array_combine;
use function array_flip;
use function array_keys;
use function array_shift;
use function arsort;
use function checkdnsrr;
use function defined;
use function extension_loaded;
use function func_get_args;
use function function_exists;
use function gethostbynamel;
use function getmxrr;
use function idn_to_ascii;
use function idn_to_utf8;
use function is_array;
use function is_string;
use function preg_match;
use function str_contains;
use function strlen;
use function trim;

use const INTL_IDNA_VARIANT_UTS46;

class EmailAddress extends AbstractValidator
{
    public const INVALID            = 'emailAddressInvalid';
    public const INVALID_FORMAT     = 'emailAddressInvalidFormat';
    public const INVALID_HOSTNAME   = 'emailAddressInvalidHostname';
    public const INVALID_MX_RECORD  = 'emailAddressInvalidMxRecord';
    public const INVALID_SEGMENT    = 'emailAddressInvalidSegment';
    public const DOT_ATOM           = 'emailAddressDotAtom';
    public const QUOTED_STRING      = 'emailAddressQuotedString';
    public const INVALID_LOCAL_PART = 'emailAddressInvalidLocalPart';
    public const LENGTH_EXCEEDED    = 'emailAddressLengthExceeded';

    

    
    protected $messageTemplates = [
        self::INVALID            => "Invalid type given. String expected",
        self::INVALID_FORMAT     => "The input is not a valid email address. Use the basic format local-part@hostname",
        self::INVALID_HOSTNAME   => "'%hostname%' is not a valid hostname for the email address",
        self::INVALID_MX_RECORD  => "'%hostname%' does not appear to have any valid MX or A records for the email address",
        self::INVALID_SEGMENT    => "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network",
        self::DOT_ATOM           => "'%localPart%' can not be matched against dot-atom format",
        self::QUOTED_STRING      => "'%localPart%' can not be matched against quoted-string format",
        self::INVALID_LOCAL_PART => "'%localPart%' is not a valid local part for the email address",
        self::LENGTH_EXCEEDED    => "The input exceeds the allowed length",
    ];

    

    
    protected $messageVariables = [
        'hostname'  => 'hostname',
        'localPart' => 'localPart',
    ];

    
    protected $hostname;

    
    protected $localPart;

    
    protected $mxRecord = [];

    
    protected $options = [
        'useMxCheck'        => false,
        'useDeepMxCheck'    => false,
        'useDomainCheck'    => true,
        'allow'             => Hostname::ALLOW_DNS,
        'strict'            => true,
        'hostnameValidator' => null,
    ];

    
    public function __construct($options = [])
    {
        if (! is_array($options)) {
            $options       = func_get_args();
            $temp['allow'] = array_shift($options);
            if (! empty($options)) {
                $temp['useMxCheck'] = array_shift($options);
            }

            if (! empty($options)) {
                $temp['hostnameValidator'] = array_shift($options);
            }

            $options = $temp;
        }

        parent::__construct($options);
    }

    
    public function setMessage($messageString, $messageKey = null)
    {
        if ($messageKey === null) {
            $this->getHostnameValidator()->setMessage($messageString);
            parent::setMessage($messageString);
            return $this;
        }

        if (! isset($this->messageTemplates[$messageKey])) {
            $this->getHostnameValidator()->setMessage($messageString, $messageKey);
        } else {
            parent::setMessage($messageString, $messageKey);
        }

        return $this;
    }

    
    public function getHostnameValidator()
    {
        if (! isset($this->options['hostnameValidator'])) {
            $this->options['hostnameValidator'] = new Hostname($this->getAllow());
        }

        return $this->options['hostnameValidator'];
    }

    
    public function setHostnameValidator(?Hostname $hostnameValidator = null)
    {
        $this->options['hostnameValidator'] = $hostnameValidator;

        return $this;
    }

    
    public function getAllow()
    {
        return $this->options['allow'];
    }

    
    public function setAllow($allow)
    {
        $this->options['allow'] = $allow;
        if (isset($this->options['hostnameValidator'])) {
            $this->options['hostnameValidator']->setAllow($allow);
        }

        return $this;
    }

    
    public function isMxSupported()
    {
        return function_exists('getmxrr');
    }

    
    public function getMxCheck()
    {
        return $this->options['useMxCheck'];
    }

    
    public function useMxCheck($mx)
    {
        $this->options['useMxCheck'] = (bool) $mx;
        return $this;
    }

    
    public function getDeepMxCheck()
    {
        return $this->options['useDeepMxCheck'];
    }

    
    public function useDeepMxCheck($deep)
    {
        $this->options['useDeepMxCheck'] = (bool) $deep;
        return $this;
    }

    
    public function getDomainCheck()
    {
        return $this->options['useDomainCheck'];
    }

    
    public function useDomainCheck($domain = true)
    {
        $this->options['useDomainCheck'] = (bool) $domain;
        return $this;
    }

    
    protected function isReserved($host)
    {
        if (! preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $host)) {
            $host = gethostbynamel($host);
        } else {
            $host = [$host];
        }

        if (empty($host)) {
            return false;
        }

        foreach ($host as $server) {
            
            
            if (!preg_match('/^(0|10|127)(\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))){3}$/', $server) &&
                
                !preg_match('/^100\.(6[0-4]|[7-9][0-9]|1[0-1][0-9]|12[0-7])(\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))){2}$/', $server) &&
                
                !preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])(\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))){2}$/', $server) &&
                
                !preg_match('/^198\.(1[8-9])(\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))){2}$/', $server) &&
                
                !preg_match('/^(169\.254|192\.168)(\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))){2}$/', $server) &&
                
                !preg_match('/^(192\.0\.2|192\.88\.99|198\.51\.100|203\.0\.113)\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))$/', $server) &&
                
                !preg_match('/^(2(2[4-9]|[3-4][0-9]|5[0-5]))(\.([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))){3}$/', $server)
            ) {
                return false;
            }
            
        }

        return true;
    }

    
    protected function validateLocalPart()
    {
        

        
        
        
        $atext = 'a-zA-Z0-9\x21\x23\x24\x25\x26\x27\x2a\x2b\x2d\x2f\x3d\x3f\x5e\x5f\x60\x7b\x7c\x7d\x7e';
        if (preg_match('/^[' . $atext . ']+(\x2e+[' . $atext . ']+)*$/', $this->localPart)) {
            return true;
        }

        if ($this->validateInternationalizedLocalPart($this->localPart)) {
            return true;
        }

        

        
        $qtext      = '\x20-\x21\x23-\x5b\x5d-\x7e'; 
        $quotedPair = '\x20-\x7e'; 
        if (preg_match('/^"([' . $qtext . ']|\x5c[' . $quotedPair . '])*"$/', $this->localPart)) {
            return true;
        }

        $this->error(self::DOT_ATOM);
        $this->error(self::QUOTED_STRING);
        $this->error(self::INVALID_LOCAL_PART);

        return false;
    }

    
    protected function validateInternationalizedLocalPart($localPart)
    {
        if (
            extension_loaded('intl')
            && false === UConverter::transcode($localPart, 'UTF-8', 'UTF-8')
        ) {
            
            return false;
        }

        $atext = 'a-zA-Z0-9\x21\x23\x24\x25\x26\x27\x2a\x2b\x2d\x2f\x3d\x3f\x5e\x5f\x60\x7b\x7c\x7d\x7e';
        
        
        $uatext = $atext . '\x{80}-\x{FFFF}';
        return (bool) preg_match('/^[' . $uatext . ']+(\x2e+[' . $uatext . ']+)*$/u', $localPart);
    }

    
    public function getMXRecord()
    {
        return $this->mxRecord;
    }

    
    protected function validateMXRecords()
    {
        $mxHosts = [];
        $weight  = [];
        $result  = getmxrr($this->hostname, $mxHosts, $weight);
        if (! empty($mxHosts) && ! empty($weight)) {
            $this->mxRecord = array_combine($mxHosts, $weight) ?: [];
        } else {
            $this->mxRecord = [];
        }

        arsort($this->mxRecord);

        
        if (! $result) {
            $result = gethostbynamel($this->hostname);
            if (is_array($result)) {
                $this->mxRecord = array_flip($result);
            }
        }

        if (! $result) {
            $this->error(self::INVALID_MX_RECORD);
            return $result;
        }

        if (! $this->options['useDeepMxCheck']) {
            return $result;
        }

        $validAddress = false;
        $reserved     = true;
        foreach (array_keys($this->mxRecord) as $hostname) {
            $res = $this->isReserved($hostname);
            if (! $res) {
                $reserved = false;
            }

            if (! is_string($hostname) || ! trim($hostname)) {
                continue;
            }

            if (
                ! $res
                && (checkdnsrr($hostname, 'A')
                || checkdnsrr($hostname, 'AAAA')
                || checkdnsrr($hostname, 'A6'))
            ) {
                $validAddress = true;
                break;
            }
        }

        if (! $validAddress) {
            $result = false;
            $error  = $reserved ? self::INVALID_SEGMENT : self::INVALID_MX_RECORD;
            $this->error($error);
        }

        return $result;
    }

    
    protected function validateHostnamePart()
    {
        $hostname = $this->getHostnameValidator()->setTranslator($this->getTranslator())
                         ->isValid($this->hostname);
        if (! $hostname) {
            $this->error(self::INVALID_HOSTNAME);
            
            foreach ($this->getHostnameValidator()->getMessages() as $code => $message) {
                $this->abstractOptions['messages'][$code] = $message;
            }
        } elseif ($this->options['useMxCheck']) {
            
            $hostname = $this->validateMXRecords();
        }

        return $hostname;
    }

    
    protected function splitEmailParts($value)
    {
        $value = is_string($value) ? $value : '';

        
        if (
            str_contains($value, '..')
            || ! preg_match('/^(.+)@([^@]+)$/', $value, $matches)
        ) {
            return false;
        }

        $this->localPart = $matches[1];
        $this->hostname  = $this->idnToAscii($matches[2]);

        return true;
    }

    
    public function isValid($value)
    {
        if (! is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $length = true;
        $this->setValue($value);

        
        if (! $this->splitEmailParts($this->getValue())) {
            $this->error(self::INVALID_FORMAT);
            return false;
        }

        if ($this->getOption('strict') && (strlen($this->localPart) > 64) || (strlen($this->hostname) > 255)) {
            $length = false;
            $this->error(self::LENGTH_EXCEEDED);
        }

        
        $hostname = false;
        if ($this->options['useDomainCheck']) {
            $hostname = $this->validateHostnamePart();
        }

        $local = $this->validateLocalPart();

        
        return ($local && $length) && (! $this->options['useDomainCheck'] || $hostname);
    }

    
    protected function idnToAscii($email)
    {
        if (extension_loaded('intl')) {
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return idn_to_ascii($email, 0, INTL_IDNA_VARIANT_UTS46) ?: $email;
            }
            return idn_to_ascii($email) ?: $email;
        }
        return $email;
    }

    
    protected function idnToUtf8($email)
    {
        if (strlen($email) === 0) {
            return $email;
        }

        if (extension_loaded('intl')) {
            
            
            
            
            
            
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return idn_to_utf8($email, 0, INTL_IDNA_VARIANT_UTS46) ?: $email;
            }
            return idn_to_utf8($email) ?: $email;
        }
        return $email;
    }
}
