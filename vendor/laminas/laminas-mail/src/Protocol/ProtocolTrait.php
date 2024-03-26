<?php

namespace Laminas\Mail\Protocol;

use Laminas\Stdlib\ErrorHandler;

use function defined;
use function sprintf;
use function stream_context_create;
use function stream_set_timeout;
use function stream_socket_client;

use const STREAM_CLIENT_CONNECT;
use const STREAM_CRYPTO_METHOD_TLS_CLIENT;
use const STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
use const STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;


trait ProtocolTrait
{
    
    protected $novalidatecert;

    public function getCryptoMethod(): int
    {
        
        $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;

        
        
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
        }

        return $cryptoMethod;
    }

    
    public function setNoValidateCert(bool $novalidatecert)
    {
        $this->novalidatecert = $novalidatecert;
        return $this;
    }

    
    public function validateCert(): bool
    {
        return ! $this->novalidatecert;
    }

    
    private function prepareSocketOptions(): array
    {
        return $this->novalidatecert
            ? [
                'ssl' => [
                    'verify_peer_name' => false,
                    'verify_peer'      => false,
                ],
            ]
            : [];
    }

    
    protected function setupSocket(
        string $transport,
        string $host,
        ?int $port,
        int $timeout
    ) {
        ErrorHandler::start();
        $socket = stream_socket_client(
            sprintf('%s:
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create($this->prepareSocketOptions())
        );
        $error  = ErrorHandler::stop();

        if (! $socket) {
            throw new Exception\RuntimeException(sprintf(
                'cannot connect to host%s',
                $error ? sprintf('; error = %s (errno = %d )', $error->getMessage(), $error->getCode()) : ''
            ), 0, $error);
        }

        if (false === stream_set_timeout($socket, $timeout)) {
            throw new Exception\RuntimeException('Could not set stream timeout');
        }

        return $socket;
    }
}
