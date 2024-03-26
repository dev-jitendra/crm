<?php


namespace Espo\Core\Authentication\Oidc;

use Espo\Core\Authentication\Jwt\Exceptions\Invalid;
use Espo\Core\Authentication\Jwt\Exceptions\SignatureNotVerified;
use Espo\Core\Authentication\Jwt\SignatureVerifierFactory;
use Espo\Core\Authentication\Jwt\Token;
use RuntimeException;

class TokenValidator
{
    public function __construct(
        private ConfigDataProvider $configDataProvider,
        private SignatureVerifierFactory $signatureVerifierFactory
    ) {}

    
    public function validateSignature(Token $token): void
    {
        $algorithm = $token->getHeader()->getAlg();

        $allowedAlgorithmList = $this->configDataProvider->getJwtSignatureAlgorithmList();

        if (!in_array($algorithm, $allowedAlgorithmList)) {
            throw new Invalid("JWT signing algorithm `{$algorithm}` not allowed.");
        }

        $verifier = $this->signatureVerifierFactory->create($algorithm);

        if (!$verifier->verify($token)) {
            throw new SignatureNotVerified("JWT signature not verified.");
        }
    }

    
    public function validateFields(Token $token): void
    {
        $oidcClientId = $this->configDataProvider->getClientId();

        if (!$oidcClientId) {
            throw new RuntimeException("OIDC: No client ID.");
        }

        if (!in_array($oidcClientId, $token->getPayload()->getAud())) {
            throw new Invalid("JWT the `aud` field does not contain matching client ID.");
        }

        if (!$token->getPayload()->getSub()) {
            throw new Invalid("JWT does not contain the `sub` value.");
        }

        if (!$token->getPayload()->getIss()) {
            throw new Invalid("JWT does not contain the `iss` value.");
        }
    }
}
