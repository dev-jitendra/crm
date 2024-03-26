<?php


namespace Espo\Core\Authentication\Login;

use Espo\Core\Authentication\AuthToken\AuthToken;


class Data
{
    private ?string $username;
    private ?string $password;
    private ?AuthToken $authToken;

    public function __construct(?string $username, ?string $password, ?AuthToken $authToken = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->authToken = $authToken;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getAuthToken(): ?AuthToken
    {
        return $this->authToken;
    }

    public function hasUsername(): bool
    {
        return !is_null($this->username);
    }

    public function hasPassword(): bool
    {
        return !is_null($this->password);
    }

    public function hasAuthToken(): bool
    {
        return !is_null($this->authToken);
    }

    public static function createBuilder(): DataBuilder
    {
        return new DataBuilder();
    }
}
