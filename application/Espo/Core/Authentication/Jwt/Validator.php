<?php


namespace Espo\Core\Authentication\Jwt;

use Espo\Core\Authentication\Jwt\Exceptions\Expired;
use Espo\Core\Authentication\Jwt\Exceptions\NotBefore;

class Validator
{
    private const DEFAULT_TIME_LEEWAY = 60 * 4;
    private int $timeLeeway;
    private ?int $now;

    public function __construct(
        ?int $timeLeeway = null,
        ?int $now = null
    ) {
        $this->timeLeeway = $timeLeeway ?? self::DEFAULT_TIME_LEEWAY;
        $this->now = $now;
    }

    
    public function validate(Token $token): void
    {
        $exp = $token->getPayload()->getExp();
        $nbf = $token->getPayload()->getNbf();

        $now = $this->now ?? time();

        if ($exp && $exp + $this->timeLeeway <= $now) {
            throw new Expired("JWT expired.");
        }

        if ($nbf && $now < $nbf - $this->timeLeeway) {
            throw new NotBefore("JWT used before allowed time.");
        }
    }
}
