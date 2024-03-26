<?php


namespace Espo\Core\Authentication\TwoFactor\Totp;

use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Entities\UserData;
use Espo\Repositories\UserData as UserDataRepository;
use Espo\Core\Authentication\TwoFactor\Login;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Result\Data as ResultData;
use Espo\Core\Authentication\Result\FailReason;
use Espo\Core\Api\Request;

use RuntimeException;


class TotpLogin implements Login
{
    public const NAME = 'Totp';

    public function __construct(
        private EntityManager $entityManager,
        private Util $totp
    ) {}

    public function login(Result $result, Request $request): Result
    {
        $code = $request->getHeader('Espo-Authorization-Code');

        $user = $result->getUser();

        if (!$user) {
            throw new RuntimeException("No user.");
        }

        if (!$code) {
            return Result::secondStepRequired($user, $this->getResultData());
        }

        if ($this->verifyCode($user, $code)) {
            return $result;
        }

        return Result::fail(FailReason::CODE_NOT_VERIFIED);
    }

    private function getResultData(): ResultData
    {
        return ResultData::createWithMessage('enterTotpCode');
    }

    private function verifyCode(User $user, string $code): bool
    {
        $userData = $this->getUserDataRepository()->getByUserId($user->getId());

        if (!$userData) {
            return false;
        }

        if (!$userData->get('auth2FA')) {
            return false;
        }

        if ($userData->get('auth2FAMethod') !== self::NAME) {
            return false;
        }

        $secret = $userData->get('auth2FATotpSecret');

        if (!$secret) {
            return false;
        }

        return $this->totp->verifyCode($secret, $code);
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        $repository = $this->entityManager->getRepository(UserData::ENTITY_TYPE);

        return $repository;
    }
}
