<?php


namespace Espo\Core\Authentication\Logins;

use Espo\Core\Api\Request;
use Espo\Core\Authentication\Helper\UserFinder;
use Espo\Core\Authentication\Login;
use Espo\Core\Authentication\Login\Data;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Result\FailReason;
use Espo\Core\Utils\ApiKey;

use RuntimeException;

class Hmac implements Login
{
    public const NAME = 'Hmac';

    private UserFinder $userFinder;
    private ApiKey $apiKeyUtil;

    public function __construct(UserFinder $userFinder, ApiKey $apiKeyUtil)
    {
        $this->userFinder = $userFinder;
        $this->apiKeyUtil = $apiKeyUtil;
    }

    public function login(Data $data, Request $request): Result
    {
        $authString = base64_decode($request->getHeader('X-Hmac-Authorization') ?? '');

        list($apiKey, $hash) = explode(':', $authString, 2);

        if (!$apiKey) {
            return Result::fail(FailReason::WRONG_CREDENTIALS);
        }

        $user = $this->userFinder->findApiHmac($apiKey);

        if (!$user) {
            return Result::fail(FailReason::WRONG_CREDENTIALS);
        }

        $secretKey = $this->apiKeyUtil->getSecretKeyForUserId($user->getId());

        if (!$secretKey) {
            throw new RuntimeException("No secret key for API user '" . $user->getId() . "'.");
        }

        $string = $request->getMethod() . ' ' . $request->getResourcePath();

        if ($hash === ApiKey::hash($secretKey, $string)) {
            return Result::success($user);
        }

        return Result::fail(FailReason::HASH_NOT_MATCHED);
    }
}
