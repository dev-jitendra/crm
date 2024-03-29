<?php

namespace Laminas\Mail\Header;

use Laminas\Mail\Address;
use Laminas\Mail\AddressList;
use Laminas\Mail\Headers;
use Laminas\Mail\Storage\Exception\RuntimeException;

use function array_filter;
use function array_map;
use function assert;
use function idn_to_ascii;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function sprintf;
use function str_contains;
use function str_replace;
use function strtolower;
use function trim;

use const IDNA_DEFAULT;
use const IDNA_ERROR_BIDI;
use const IDNA_ERROR_CONTEXTJ;
use const IDNA_ERROR_DISALLOWED;
use const IDNA_ERROR_DOMAIN_NAME_TOO_LONG;
use const IDNA_ERROR_EMPTY_LABEL;
use const IDNA_ERROR_HYPHEN_3_4;
use const IDNA_ERROR_INVALID_ACE_LABEL;
use const IDNA_ERROR_LABEL_HAS_DOT;
use const IDNA_ERROR_LABEL_TOO_LONG;
use const IDNA_ERROR_LEADING_COMBINING_MARK;
use const IDNA_ERROR_LEADING_HYPHEN;
use const IDNA_ERROR_PUNYCODE;
use const IDNA_ERROR_TRAILING_HYPHEN;
use const INTL_IDNA_VARIANT_UTS46;


abstract class AbstractAddressList implements HeaderInterface
{
    private const IDNA_ERROR_MAP = [
        IDNA_ERROR_EMPTY_LABEL            => 'empty label',
        IDNA_ERROR_LABEL_TOO_LONG         => 'label too long',
        IDNA_ERROR_DOMAIN_NAME_TOO_LONG   => 'domain name too long',
        IDNA_ERROR_LEADING_HYPHEN         => 'leading hyphen',
        IDNA_ERROR_TRAILING_HYPHEN        => 'trailing hyphen',
        IDNA_ERROR_HYPHEN_3_4             => 'consecutive hyphens',
        IDNA_ERROR_LEADING_COMBINING_MARK => 'leading combining mark',
        IDNA_ERROR_DISALLOWED             => 'disallowed',
        IDNA_ERROR_PUNYCODE               => 'invalid punycode encoding',
        IDNA_ERROR_LABEL_HAS_DOT          => 'has dot',
        IDNA_ERROR_INVALID_ACE_LABEL      => 'label not in ASCII encoding',
        IDNA_ERROR_BIDI                   => 'fails bidirectional criteria',
        IDNA_ERROR_CONTEXTJ               => 'one or more characters fail CONTEXTJ rule',
    ];

    
    protected $addressList;

    
    protected $fieldName;

    
    protected $encoding = 'ASCII';

    
    protected static $type;

    
    protected static $typeAliases = [];

    
    public static function fromString($headerLine)
    {
        [$fieldName, $fieldValue] = GenericHeader::splitHeaderLine($headerLine);
        if ((strtolower($fieldName) !== static::$type) && ! in_array(strtolower($fieldName), static::$typeAliases)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid header line for "%s" string',
                self::class
            ));
        }

        
        $fieldValue = str_replace(Headers::FOLDING, ' ', $fieldValue);
        $fieldValue = preg_replace('/[^:]+:([^;]*);/', '$1,', $fieldValue);
        $values     = ListParser::parse($fieldValue);

        $wasEncoded = false;
        $addresses  = array_map(
            static function ($value) use (&$wasEncoded): ?Address {
                $decodedValue = HeaderWrap::mimeDecodeValue($value);
                $wasEncoded   = $wasEncoded || ($decodedValue !== $value);
                $value        = trim($decodedValue);
                $comments     = self::getComments($value);
                $value        = self::stripComments($value);
                $value        = preg_replace(
                    [
                        '#(?<!\\\)"(.*)(?<!\\\)"#', 
                        '#\\\([\x01-\x09\x0b\x0c\x0e-\x7f])#', 
                    ],
                    [
                        '\\1',
                        '\\1',
                    ],
                    $value
                );
                return empty($value) ? null : Address::fromString($value, $comments);
            },
            $values
        );
        $addresses  = array_filter($addresses);

        $header = new static();
        if ($wasEncoded) {
            $header->setEncoding('UTF-8');
        }

        
        $addressList = $header->getAddressList();
        foreach ($addresses as $address) {
            $addressList->add($address);
        }

        return $header;
    }

    
    public function getFieldName()
    {
        return $this->fieldName;
    }

    
    protected function idnToAscii($domainName): string
    {
        
        $ascii = idn_to_ascii($domainName, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46, $conversionInfo);
        if (is_string($ascii)) {
            return $ascii;
        }

        $messages = [];
        assert(is_array($conversionInfo));
        
        $errors = (int) $conversionInfo['errors'];

        foreach (self::IDNA_ERROR_MAP as $flag => $message) {
            if (($flag & $errors) === $flag) {
                $messages[] = $message;
            }
        }

        throw new RuntimeException(sprintf(
            'Failed encoding domain due to errors: %s',
            implode(', ', $messages)
        ));
    }

    
    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        $emails   = [];
        $encoding = $this->getEncoding();

        foreach ($this->getAddressList() as $address) {
            $email = $address->getEmail();
            $name  = $address->getName();

            
            if (! empty($name) && (str_contains($name, ',') || str_contains($name, ';'))) {
                
                $name = sprintf('"%s"', $name);
            }

            if (
                $format === HeaderInterface::FORMAT_ENCODED
                && 'ASCII' !== $encoding
            ) {
                if (! empty($name)) {
                    $name = HeaderWrap::mimeEncodeValue($name, $encoding);
                }

                if (preg_match('/^(.+)@([^@]+)$/', $email, $matches)) {
                    $localPart = $matches[1];
                    $hostname  = $this->idnToAscii($matches[2]);
                    $email     = sprintf('%s@%s', $localPart, $hostname);
                }
            }

            if (empty($name)) {
                $emails[] = $email;
            } else {
                $emails[] = sprintf('%s <%s>', $name, $email);
            }
        }

        
        if ($format !== HeaderInterface::FORMAT_RAW) {
            foreach ($emails as $email) {
                HeaderValue::assertValid($email);
            }
        }

        return implode(',' . Headers::FOLDING, $emails);
    }

    
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    
    public function getEncoding()
    {
        return $this->encoding;
    }

    
    public function setAddressList(AddressList $addressList)
    {
        $this->addressList = $addressList;
    }

    
    public function getAddressList()
    {
        if (null === $this->addressList) {
            $this->setAddressList(new AddressList());
        }
        return $this->addressList;
    }

    
    public function toString()
    {
        $name  = $this->getFieldName();
        $value = $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
        return empty($value) ? '' : sprintf('%s: %s', $name, $value);
    }

    
    protected static function getComments($value)
    {
        $matches = [];
        preg_match_all(
            '/\\(
                (?P<comment>(
                    \\\\.|
                    [^\\\\)]
                )+)
            \\)/x',
            $value,
            $matches
        );
        return isset($matches['comment']) ? implode(', ', $matches['comment']) : '';
    }

    
    protected static function stripComments($value)
    {
        return preg_replace(
            '/\\(
                (
                    \\\\.|
                    [^\\\\)]
                )+
            \\)/x',
            '',
            $value
        );
    }
}
