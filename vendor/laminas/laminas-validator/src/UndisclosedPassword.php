<?php

namespace Laminas\Validator;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use SensitiveParameter;

use function array_filter;
use function explode;
use function is_string;
use function sha1;
use function strcmp;
use function strtoupper;
use function substr;

final class UndisclosedPassword extends AbstractValidator
{
    

    private const HIBP_API_URI                       = 'https:
    private const HIBP_API_REQUEST_TIMEOUT           = 300;
    private const HIBP_CLIENT_USER_AGENT_STRING      = 'laminas-validator';
    private const HIBP_CLIENT_ACCEPT_HEADER          = 'application/vnd.haveibeenpwned.v2+json';
    private const HIBP_K_ANONYMITY_HASH_RANGE_LENGTH = 5;
    private const HIBP_K_ANONYMITY_HASH_RANGE_BASE   = 0;
    private const SHA1_STRING_LENGTH                 = 40;

    

    private const PASSWORD_BREACHED = 'passwordBreached';
    private const NOT_A_STRING      = 'wrongInput';

    

    
    protected $messageTemplates = [
        self::PASSWORD_BREACHED => 'The provided password was found in previous breaches, please create another password',
        self::NOT_A_STRING      => 'The provided password is not a string, please provide a correct password',
    ];

    
    public function __construct(private ClientInterface $httpClient, private RequestFactoryInterface $makeHttpRequest)
    {
        parent::__construct();
    }

    
    

    
    public function isValid(
        #[SensitiveParameter]
        $value
    ): bool {
        if (! is_string($value)) {
            $this->error(self::NOT_A_STRING);
            return false;
        }

        if ($this->isPwnedPassword($value)) {
            $this->error(self::PASSWORD_BREACHED);
            return false;
        }

        return true;
    }

    

    private function isPwnedPassword(
        #[SensitiveParameter]
        string $password
    ): bool {
        $sha1Hash  = $this->hashPassword($password);
        $rangeHash = $this->getRangeHash($sha1Hash);
        $hashList  = $this->retrieveHashList($rangeHash);

        return $this->hashInResponse($sha1Hash, $hashList);
    }

    
    private function hashPassword(
        #[SensitiveParameter]
        string $password
    ): string {
        $hashedPassword = sha1($password);

        return strtoupper($hashedPassword);
    }

    
    private function getRangeHash(
        #[SensitiveParameter]
        string $passwordHash
    ): string {
        return substr($passwordHash, self::HIBP_K_ANONYMITY_HASH_RANGE_BASE, self::HIBP_K_ANONYMITY_HASH_RANGE_LENGTH);
    }

    
    private function retrieveHashList(
        #[SensitiveParameter]
        string $passwordRange
    ): string {
        $request = $this->makeHttpRequest->createRequest(
            'GET',
            self::HIBP_API_URI . '/range/' . $passwordRange
        );

        $response = $this->httpClient->sendRequest($request);
        return (string) $response->getBody();
    }

    
    private function hashInResponse(
        #[SensitiveParameter]
        string $sha1Hash,
        #[SensitiveParameter]
        string $resultStream
    ): bool {
        $data   = explode("\r\n", $resultStream);
        $hashes = array_filter($data, static function ($value) use ($sha1Hash): bool {
            [$hash] = explode(':', $value);

            return strcmp($hash, substr($sha1Hash, self::HIBP_K_ANONYMITY_HASH_RANGE_LENGTH)) === 0;
        });

        return $hashes !== [];
    }
}
