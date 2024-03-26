<?php


namespace Espo\Core\Authentication\Logins;

use Espo\Core\Api\Request;
use Espo\Core\Authentication\Helper\UserFinder;
use Espo\Core\Authentication\Login;
use Espo\Core\Authentication\Login\Data;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Result\FailReason;

class ApiKey implements Login
{
    public const NAME = 'ApiKey';

    private UserFinder $userFinder;

    public function __construct(UserFinder $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    public function login(Data $data, Request $request): Result
    {
        $apiKey = $request->getHeader('X-Api-Key');

        if (!$apiKey) {
            return Result::fail(FailReason::WRONG_CREDENTIALS);
        }

        $user = $this->userFinder->findApiApiKey($apiKey);

        if (!$user) {
            return Result::fail(FailReason::WRONG_CREDENTIALS);
        }

        return Result::success($user);
    }
}
