<?php


namespace Espo\Core\Authentication\Login;

use Espo\Core\Authentication\AuthToken\AuthToken;

class DataBuilder
{
    private ?string $username = null;
    private ?string $password = null;
    private ?AuthToken $authToken = null;

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setAuthToken(?AuthToken $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    public function build(): Data
    {
        return new Data($this->username, $this->password, $this->authToken);
    }
}
