<?php

namespace AsyncAws\Core\Sts\Result;

use AsyncAws\Core\Response;
use AsyncAws\Core\Result;
use AsyncAws\Core\Sts\ValueObject\AssumedRoleUser;
use AsyncAws\Core\Sts\ValueObject\Credentials;


class AssumeRoleResponse extends Result
{
    
    private $credentials;

    
    private $assumedRoleUser;

    
    private $packedPolicySize;

    
    private $sourceIdentity;

    public function getAssumedRoleUser(): ?AssumedRoleUser
    {
        $this->initialize();

        return $this->assumedRoleUser;
    }

    public function getCredentials(): ?Credentials
    {
        $this->initialize();

        return $this->credentials;
    }

    public function getPackedPolicySize(): ?int
    {
        $this->initialize();

        return $this->packedPolicySize;
    }

    public function getSourceIdentity(): ?string
    {
        $this->initialize();

        return $this->sourceIdentity;
    }

    protected function populateResult(Response $response): void
    {
        $data = new \SimpleXMLElement($response->getContent());
        $data = $data->AssumeRoleResult;

        $this->credentials = !$data->Credentials ? null : new Credentials([
            'AccessKeyId' => (string) $data->Credentials->AccessKeyId,
            'SecretAccessKey' => (string) $data->Credentials->SecretAccessKey,
            'SessionToken' => (string) $data->Credentials->SessionToken,
            'Expiration' => new \DateTimeImmutable((string) $data->Credentials->Expiration),
        ]);
        $this->assumedRoleUser = !$data->AssumedRoleUser ? null : new AssumedRoleUser([
            'AssumedRoleId' => (string) $data->AssumedRoleUser->AssumedRoleId,
            'Arn' => (string) $data->AssumedRoleUser->Arn,
        ]);
        $this->packedPolicySize = ($v = $data->PackedPolicySize) ? (int) (string) $v : null;
        $this->sourceIdentity = ($v = $data->SourceIdentity) ? (string) $v : null;
    }
}
