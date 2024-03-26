<?php


namespace Espo\Core\Authentication\Logins;

use Espo\Core\Api\Request;
use Espo\Core\Authentication\Helper\UserFinder;
use Espo\Core\Authentication\Login;
use Espo\Core\Authentication\Login\Data;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Result\FailReason;
use Espo\Core\Utils\PasswordHash;

use RuntimeException;

class Espo implements Login
{
    public const NAME = 'Espo';

    public function __construct(
        private UserFinder $userFinder,
        private PasswordHash $passwordHash
    ) {}

    public function login(Data $data, Request $request): Result
    {
        $username = $data->getUsername();
        $password = $data->getPassword();
        $authToken = $data->getAuthToken();

        if (!$username) {
            return Result::fail(FailReason::NO_USERNAME);
        }

        if (!$password) {
            return Result::fail(FailReason::NO_PASSWORD);
        }

        $hash = $authToken ?
            $authToken->getHash() :
            $this->passwordHash->hash($password);

        if (!$hash) {
            throw new RuntimeException("No hash.");
        }

        $user = $this->userFinder->find($username, $hash);

        if (!$user) {
            return Result::fail(FailReason::WRONG_CREDENTIALS);
        }

        if ($authToken && $user->getId() !== $authToken->getUserId()) {
            return Result::fail(FailReason::USER_TOKEN_MISMATCH);
        }

        return Result::success($user);
    }
}
