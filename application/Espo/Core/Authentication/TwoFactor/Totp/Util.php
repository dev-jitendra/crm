<?php


namespace Espo\Core\Authentication\TwoFactor\Totp;

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;
use RuntimeException;

class Util
{
    public function verifyCode(string $secret, string $code): bool
    {
        $impl = new TwoFactorAuth();

        return $impl->verifyCode($secret, $code);
    }

    public function createSecret(): string
    {
        $impl = new TwoFactorAuth();

        try {
            return $impl->createSecret();
        }
        catch (TwoFactorAuthException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
