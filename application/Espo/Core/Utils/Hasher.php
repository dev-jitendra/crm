<?php


namespace Espo\Core\Utils;


class Hasher
{
    private string $secretKeyParam = 'hashSecretKey';

    public function __construct(private Config $config)
    {}

    public function hash(string $string): string
    {
        $secretKey = $this->config->get($this->secretKeyParam) ?? '';

        return md5(hash_hmac('sha256', $string, $secretKey, true));
    }
}
