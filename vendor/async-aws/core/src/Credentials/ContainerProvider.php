<?php

declare(strict_types=1);

namespace AsyncAws\Core\Credentials;

use AsyncAws\Core\Configuration;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


final class ContainerProvider implements CredentialProvider
{
    private const ENDPOINT = 'http:

    
    private $logger;

    
    private $httpClient;

    
    private $timeout;

    public function __construct(?HttpClientInterface $httpClient = null, ?LoggerInterface $logger = null, float $timeout = 1.0)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->timeout = $timeout;
    }

    public function getCredentials(Configuration $configuration): ?Credentials
    {
        $relativeUri = $configuration->get(Configuration::OPTION_CONTAINER_CREDENTIALS_RELATIVE_URI);
        
        if (empty($relativeUri)) {
            return null;
        }

        
        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT . $relativeUri, ['timeout' => $this->timeout]);
            $result = $response->toArray();
        } catch (DecodingExceptionInterface $e) {
            $this->logger->info('Failed to decode Credentials.', ['exception' => $e]);

            return null;
        } catch (TransportExceptionInterface|HttpExceptionInterface $e) {
            $this->logger->info('Failed to fetch Profile from Instance Metadata.', ['exception' => $e]);

            return null;
        }

        if (null !== $date = $response->getHeaders(false)['date'][0] ?? null) {
            $date = new \DateTimeImmutable($date);
        }

        return new Credentials(
            $result['AccessKeyId'],
            $result['SecretAccessKey'],
            $result['Token'],
            Credentials::adjustExpireDate(new \DateTimeImmutable($result['Expiration']), $date)
        );
    }
}
