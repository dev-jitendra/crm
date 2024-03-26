<?php


namespace Espo\Core\Authentication\Oidc;

use Espo\Core\Authentication\Jwt\SignatureVerifier;
use Espo\Core\Authentication\Jwt\SignatureVerifierFactory;
use Espo\Core\Authentication\Jwt\SignatureVerifiers\Hmac;
use Espo\Core\Authentication\Jwt\SignatureVerifiers\Rsa;
use RuntimeException;

class DefaultSignatureVerifierFactory implements SignatureVerifierFactory
{
    private const RS256 = 'RS256';
    private const RS384 = 'RS384';
    private const RS512 = 'RS512';
    private const HS256 = 'HS256';
    private const HS384 = 'HS384';
    private const HS512 = 'HS512';

    private const ALGORITHM_VERIFIER_CLASS_NAME_MAP = [
        self::RS256 => Rsa::class,
        self::RS384 => Rsa::class,
        self::RS512 => Rsa::class,
        self::HS256 => Hmac::class,
        self::HS384 => Hmac::class,
        self::HS512 => Hmac::class,
    ];

    public function __construct(
        private KeysProvider $keysProvider,
        private ConfigDataProvider $configDataProvider
    ) {}

    public function create(string $algorithm): SignatureVerifier
    {
        
        $className = self::ALGORITHM_VERIFIER_CLASS_NAME_MAP[$algorithm] ?? null;

        if (!$className) {
            throw new RuntimeException("Not supported algorithm {$algorithm}.");
        }

        if ($className === Rsa::class) {
            $keys = $this->keysProvider->get();

            return new Rsa($algorithm, $keys);
        }

        if ($className === Hmac::class) {
            $key = $this->configDataProvider->getClientSecret();

            if (!$key) {
                throw new RuntimeException("No client secret.");
            }

            return new Hmac($algorithm, $key);
        }

        throw new RuntimeException();
    }
}
