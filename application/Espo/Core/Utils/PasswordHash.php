<?php


namespace Espo\Core\Utils;

use RuntimeException;

class PasswordHash
{
    
    private string $saltFormat = '$6${0}$';

    public function __construct(private Config $config)
    {}

    
    public function hash(string $password, bool $useMd5 = true): string
    {
        $salt = $this->getSalt();

        if ($useMd5) {
            $password = md5($password);
        }

        $hash = crypt($password, $salt);

        return str_replace($salt, '', $hash);
    }

    
    private function getSalt(): string
    {
        $salt = $this->config->get('passwordSalt');

        if (!isset($salt)) {
            throw new RuntimeException('Option "passwordSalt" does not exist in config.php');
        }

        return $this->normalizeSalt($salt);
    }

    
    private function normalizeSalt(string $salt): string
    {
        return str_replace("{0}", $salt, $this->saltFormat);
    }

    
    public function generateSalt(): string
    {
        return substr(md5(uniqid()), 0, 16);
    }
}
