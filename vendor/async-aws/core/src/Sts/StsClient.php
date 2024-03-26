<?php

namespace AsyncAws\Core\Sts;

use AsyncAws\Core\AbstractApi;
use AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use AsyncAws\Core\AwsError\XmlAwsErrorFactory;
use AsyncAws\Core\RequestContext;
use AsyncAws\Core\Sts\Exception\ExpiredTokenException;
use AsyncAws\Core\Sts\Exception\IDPCommunicationErrorException;
use AsyncAws\Core\Sts\Exception\IDPRejectedClaimException;
use AsyncAws\Core\Sts\Exception\InvalidIdentityTokenException;
use AsyncAws\Core\Sts\Exception\MalformedPolicyDocumentException;
use AsyncAws\Core\Sts\Exception\PackedPolicyTooLargeException;
use AsyncAws\Core\Sts\Exception\RegionDisabledException;
use AsyncAws\Core\Sts\Input\AssumeRoleRequest;
use AsyncAws\Core\Sts\Input\AssumeRoleWithWebIdentityRequest;
use AsyncAws\Core\Sts\Input\GetCallerIdentityRequest;
use AsyncAws\Core\Sts\Result\AssumeRoleResponse;
use AsyncAws\Core\Sts\Result\AssumeRoleWithWebIdentityResponse;
use AsyncAws\Core\Sts\Result\GetCallerIdentityResponse;
use AsyncAws\Core\Sts\ValueObject\PolicyDescriptorType;
use AsyncAws\Core\Sts\ValueObject\ProvidedContext;
use AsyncAws\Core\Sts\ValueObject\Tag;

class StsClient extends AbstractApi
{
    
    public function assumeRole($input): AssumeRoleResponse
    {
        $input = AssumeRoleRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'AssumeRole', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'MalformedPolicyDocument' => MalformedPolicyDocumentException::class,
            'PackedPolicyTooLarge' => PackedPolicyTooLargeException::class,
            'RegionDisabledException' => RegionDisabledException::class,
            'ExpiredTokenException' => ExpiredTokenException::class,
        ]]));

        return new AssumeRoleResponse($response);
    }

    
    public function assumeRoleWithWebIdentity($input): AssumeRoleWithWebIdentityResponse
    {
        $input = AssumeRoleWithWebIdentityRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'AssumeRoleWithWebIdentity', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'MalformedPolicyDocument' => MalformedPolicyDocumentException::class,
            'PackedPolicyTooLarge' => PackedPolicyTooLargeException::class,
            'IDPRejectedClaim' => IDPRejectedClaimException::class,
            'IDPCommunicationError' => IDPCommunicationErrorException::class,
            'InvalidIdentityToken' => InvalidIdentityTokenException::class,
            'ExpiredTokenException' => ExpiredTokenException::class,
            'RegionDisabledException' => RegionDisabledException::class,
        ]]));

        return new AssumeRoleWithWebIdentityResponse($response);
    }

    
    public function getCallerIdentity($input = []): GetCallerIdentityResponse
    {
        $input = GetCallerIdentityRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetCallerIdentity', 'region' => $input->getRegion()]));

        return new GetCallerIdentityResponse($response);
    }

    protected function getAwsErrorFactory(): AwsErrorFactoryInterface
    {
        return new XmlAwsErrorFactory();
    }

    protected function getEndpointMetadata(?string $region): array
    {
        if (null === $region) {
            return [
                'endpoint' => 'https:
                'signRegion' => 'us-east-1',
                'signService' => 'sts',
                'signVersions' => ['v4'],
            ];
        }

        switch ($region) {
            case 'cn-north-1':
            case 'cn-northwest-1':
                return [
                    'endpoint' => "https:
                    'signRegion' => $region,
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-east-1-fips':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-east-1',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-east-2-fips':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-east-2',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-west-1-fips':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-west-1',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-west-2-fips':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-west-2',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-gov-east-1-fips':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-gov-east-1',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-gov-west-1-fips':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-gov-west-1',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-iso-east-1':
            case 'us-iso-west-1':
                return [
                    'endpoint' => "https:
                    'signRegion' => $region,
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
            case 'us-isob-east-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-isob-east-1',
                    'signService' => 'sts',
                    'signVersions' => ['v4'],
                ];
        }

        return [
            'endpoint' => "https:
            'signRegion' => $region,
            'signService' => 'sts',
            'signVersions' => ['v4'],
        ];
    }

    protected function getServiceCode(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);

        return 'sts';
    }

    protected function getSignatureScopeName(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);

        return 'sts';
    }

    protected function getSignatureVersion(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);

        return 'v4';
    }
}
