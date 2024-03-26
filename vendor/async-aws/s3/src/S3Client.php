<?php

namespace AsyncAws\S3;

use AsyncAws\Core\AbstractApi;
use AsyncAws\Core\AwsError\AwsErrorFactoryInterface;
use AsyncAws\Core\AwsError\XmlAwsErrorFactory;
use AsyncAws\Core\Configuration;
use AsyncAws\Core\RequestContext;
use AsyncAws\Core\Result;
use AsyncAws\S3\Enum\BucketCannedACL;
use AsyncAws\S3\Enum\EncodingType;
use AsyncAws\S3\Enum\MetadataDirective;
use AsyncAws\S3\Enum\ObjectCannedACL;
use AsyncAws\S3\Enum\ObjectLockLegalHoldStatus;
use AsyncAws\S3\Enum\ObjectLockMode;
use AsyncAws\S3\Enum\RequestPayer;
use AsyncAws\S3\Enum\ServerSideEncryption;
use AsyncAws\S3\Enum\StorageClass;
use AsyncAws\S3\Enum\TaggingDirective;
use AsyncAws\S3\Exception\BucketAlreadyExistsException;
use AsyncAws\S3\Exception\BucketAlreadyOwnedByYouException;
use AsyncAws\S3\Exception\InvalidObjectStateException;
use AsyncAws\S3\Exception\NoSuchBucketException;
use AsyncAws\S3\Exception\NoSuchKeyException;
use AsyncAws\S3\Exception\NoSuchUploadException;
use AsyncAws\S3\Exception\ObjectNotInActiveTierErrorException;
use AsyncAws\S3\Input\AbortMultipartUploadRequest;
use AsyncAws\S3\Input\CompleteMultipartUploadRequest;
use AsyncAws\S3\Input\CopyObjectRequest;
use AsyncAws\S3\Input\CreateBucketRequest;
use AsyncAws\S3\Input\CreateMultipartUploadRequest;
use AsyncAws\S3\Input\DeleteBucketCorsRequest;
use AsyncAws\S3\Input\DeleteBucketRequest;
use AsyncAws\S3\Input\DeleteObjectRequest;
use AsyncAws\S3\Input\DeleteObjectsRequest;
use AsyncAws\S3\Input\GetBucketCorsRequest;
use AsyncAws\S3\Input\GetObjectAclRequest;
use AsyncAws\S3\Input\GetObjectRequest;
use AsyncAws\S3\Input\HeadBucketRequest;
use AsyncAws\S3\Input\HeadObjectRequest;
use AsyncAws\S3\Input\ListMultipartUploadsRequest;
use AsyncAws\S3\Input\ListObjectsV2Request;
use AsyncAws\S3\Input\ListPartsRequest;
use AsyncAws\S3\Input\PutBucketCorsRequest;
use AsyncAws\S3\Input\PutBucketNotificationConfigurationRequest;
use AsyncAws\S3\Input\PutObjectAclRequest;
use AsyncAws\S3\Input\PutObjectRequest;
use AsyncAws\S3\Input\UploadPartRequest;
use AsyncAws\S3\Result\AbortMultipartUploadOutput;
use AsyncAws\S3\Result\BucketExistsWaiter;
use AsyncAws\S3\Result\BucketNotExistsWaiter;
use AsyncAws\S3\Result\CompleteMultipartUploadOutput;
use AsyncAws\S3\Result\CopyObjectOutput;
use AsyncAws\S3\Result\CreateBucketOutput;
use AsyncAws\S3\Result\CreateMultipartUploadOutput;
use AsyncAws\S3\Result\DeleteObjectOutput;
use AsyncAws\S3\Result\DeleteObjectsOutput;
use AsyncAws\S3\Result\GetBucketCorsOutput;
use AsyncAws\S3\Result\GetObjectAclOutput;
use AsyncAws\S3\Result\GetObjectOutput;
use AsyncAws\S3\Result\HeadObjectOutput;
use AsyncAws\S3\Result\ListMultipartUploadsOutput;
use AsyncAws\S3\Result\ListObjectsV2Output;
use AsyncAws\S3\Result\ListPartsOutput;
use AsyncAws\S3\Result\ObjectExistsWaiter;
use AsyncAws\S3\Result\ObjectNotExistsWaiter;
use AsyncAws\S3\Result\PutObjectAclOutput;
use AsyncAws\S3\Result\PutObjectOutput;
use AsyncAws\S3\Result\UploadPartOutput;
use AsyncAws\S3\Signer\SignerV4ForS3;
use AsyncAws\S3\ValueObject\AccessControlPolicy;
use AsyncAws\S3\ValueObject\CompletedMultipartUpload;
use AsyncAws\S3\ValueObject\CORSConfiguration;
use AsyncAws\S3\ValueObject\CreateBucketConfiguration;
use AsyncAws\S3\ValueObject\Delete;
use AsyncAws\S3\ValueObject\MultipartUpload;
use AsyncAws\S3\ValueObject\NotificationConfiguration;
use AsyncAws\S3\ValueObject\Part;

class S3Client extends AbstractApi
{
    
    public function abortMultipartUpload($input): AbortMultipartUploadOutput
    {
        $input = AbortMultipartUploadRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'AbortMultipartUpload', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchUpload' => NoSuchUploadException::class,
        ]]));

        return new AbortMultipartUploadOutput($response);
    }

    
    public function bucketExists($input): BucketExistsWaiter
    {
        $input = HeadBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadBucket', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchBucket' => NoSuchBucketException::class,
        ]]));

        return new BucketExistsWaiter($response, $this, $input);
    }

    
    public function bucketNotExists($input): BucketNotExistsWaiter
    {
        $input = HeadBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadBucket', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchBucket' => NoSuchBucketException::class,
        ]]));

        return new BucketNotExistsWaiter($response, $this, $input);
    }

    
    public function completeMultipartUpload($input): CompleteMultipartUploadOutput
    {
        $input = CompleteMultipartUploadRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CompleteMultipartUpload', 'region' => $input->getRegion()]));

        return new CompleteMultipartUploadOutput($response);
    }

    
    public function copyObject($input): CopyObjectOutput
    {
        $input = CopyObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CopyObject', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'ObjectNotInActiveTierError' => ObjectNotInActiveTierErrorException::class,
        ]]));

        return new CopyObjectOutput($response);
    }

    
    public function createBucket($input): CreateBucketOutput
    {
        $input = CreateBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CreateBucket', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'BucketAlreadyExists' => BucketAlreadyExistsException::class,
            'BucketAlreadyOwnedByYou' => BucketAlreadyOwnedByYouException::class,
        ]]));

        return new CreateBucketOutput($response);
    }

    
    public function createMultipartUpload($input): CreateMultipartUploadOutput
    {
        $input = CreateMultipartUploadRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'CreateMultipartUpload', 'region' => $input->getRegion()]));

        return new CreateMultipartUploadOutput($response);
    }

    
    public function deleteBucket($input): Result
    {
        $input = DeleteBucketRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteBucket', 'region' => $input->getRegion()]));

        return new Result($response);
    }

    
    public function deleteBucketCors($input): Result
    {
        $input = DeleteBucketCorsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteBucketCors', 'region' => $input->getRegion()]));

        return new Result($response);
    }

    
    public function deleteObject($input): DeleteObjectOutput
    {
        $input = DeleteObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteObject', 'region' => $input->getRegion()]));

        return new DeleteObjectOutput($response);
    }

    
    public function deleteObjects($input): DeleteObjectsOutput
    {
        $input = DeleteObjectsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'DeleteObjects', 'region' => $input->getRegion()]));

        return new DeleteObjectsOutput($response);
    }

    
    public function getBucketCors($input): GetBucketCorsOutput
    {
        $input = GetBucketCorsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetBucketCors', 'region' => $input->getRegion()]));

        return new GetBucketCorsOutput($response);
    }

    
    public function getObject($input): GetObjectOutput
    {
        $input = GetObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetObject', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchKey' => NoSuchKeyException::class,
            'InvalidObjectState' => InvalidObjectStateException::class,
        ]]));

        return new GetObjectOutput($response);
    }

    
    public function getObjectAcl($input): GetObjectAclOutput
    {
        $input = GetObjectAclRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'GetObjectAcl', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchKey' => NoSuchKeyException::class,
        ]]));

        return new GetObjectAclOutput($response);
    }

    
    public function headObject($input): HeadObjectOutput
    {
        $input = HeadObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadObject', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchKey' => NoSuchKeyException::class,
        ]]));

        return new HeadObjectOutput($response);
    }

    
    public function listMultipartUploads($input): ListMultipartUploadsOutput
    {
        $input = ListMultipartUploadsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListMultipartUploads', 'region' => $input->getRegion()]));

        return new ListMultipartUploadsOutput($response, $this, $input);
    }

    
    public function listObjectsV2($input): ListObjectsV2Output
    {
        $input = ListObjectsV2Request::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListObjectsV2', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchBucket' => NoSuchBucketException::class,
        ]]));

        return new ListObjectsV2Output($response, $this, $input);
    }

    
    public function listParts($input): ListPartsOutput
    {
        $input = ListPartsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'ListParts', 'region' => $input->getRegion()]));

        return new ListPartsOutput($response, $this, $input);
    }

    
    public function objectExists($input): ObjectExistsWaiter
    {
        $input = HeadObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadObject', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchKey' => NoSuchKeyException::class,
        ]]));

        return new ObjectExistsWaiter($response, $this, $input);
    }

    
    public function objectNotExists($input): ObjectNotExistsWaiter
    {
        $input = HeadObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'HeadObject', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchKey' => NoSuchKeyException::class,
        ]]));

        return new ObjectNotExistsWaiter($response, $this, $input);
    }

    
    public function putBucketCors($input): Result
    {
        $input = PutBucketCorsRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutBucketCors', 'region' => $input->getRegion()]));

        return new Result($response);
    }

    
    public function putBucketNotificationConfiguration($input): Result
    {
        $input = PutBucketNotificationConfigurationRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutBucketNotificationConfiguration', 'region' => $input->getRegion()]));

        return new Result($response);
    }

    
    public function putObject($input): PutObjectOutput
    {
        $input = PutObjectRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutObject', 'region' => $input->getRegion()]));

        return new PutObjectOutput($response);
    }

    
    public function putObjectAcl($input): PutObjectAclOutput
    {
        $input = PutObjectAclRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'PutObjectAcl', 'region' => $input->getRegion(), 'exceptionMapping' => [
            'NoSuchKey' => NoSuchKeyException::class,
        ]]));

        return new PutObjectAclOutput($response);
    }

    
    public function uploadPart($input): UploadPartOutput
    {
        $input = UploadPartRequest::create($input);
        $response = $this->getResponse($input->request(), new RequestContext(['operation' => 'UploadPart', 'region' => $input->getRegion()]));

        return new UploadPartOutput($response);
    }

    protected function getAwsErrorFactory(): AwsErrorFactoryInterface
    {
        return new XmlAwsErrorFactory();
    }

    protected function getEndpoint(string $uri, array $query, ?string $region): string
    {
        $uriParts = \explode('/', $uri, 3);
        $bucket = $uriParts[1] ?? '';
        $bucketLen = \strlen($bucket);
        $configuration = $this->getConfiguration();

        if (
        $bucketLen < 3 || $bucketLen > 63
        || filter_var($bucket, \FILTER_VALIDATE_IP) 
        || !preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/', $bucket) 
        || filter_var(\parse_url($configuration->get('endpoint'), \PHP_URL_HOST), \FILTER_VALIDATE_IP) 
        || \filter_var($configuration->get('pathStyleEndpoint'), \FILTER_VALIDATE_BOOLEAN)
        ) {
            return parent::getEndpoint($uri, $query, $region);
        }

        return \preg_replace('|https?:
    }

    protected function getEndpointMetadata(?string $region): array
    {
        if (null === $region) {
            return [
                'endpoint' => 'https:
                'signRegion' => 'us-east-1',
                'signService' => 's3',
                'signVersions' => ['s3v4'],
            ];
        }

        switch ($region) {
            case 'cn-north-1':
            case 'cn-northwest-1':
                return [
                    'endpoint' => "https:
                    'signRegion' => $region,
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-isob-east-1':
                return [
                    'endpoint' => "https:
                    'signRegion' => $region,
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'ap-northeast-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'ap-northeast-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'ap-southeast-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'ap-southeast-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'ap-southeast-2':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'ap-southeast-2',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'eu-west-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'eu-west-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'fips-us-gov-west-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-gov-west-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 's3-external-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-east-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'sa-east-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'sa-east-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-east-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-east-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-gov-east-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-gov-east-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-gov-west-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-gov-west-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-iso-east-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-iso-east-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-west-1':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-west-1',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
            case 'us-west-2':
                return [
                    'endpoint' => 'https:
                    'signRegion' => 'us-west-2',
                    'signService' => 's3',
                    'signVersions' => ['s3v4'],
                ];
        }

        return [
            'endpoint' => "https:
            'signRegion' => $region,
            'signService' => 's3',
            'signVersions' => ['s3v4'],
        ];
    }

    protected function getServiceCode(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);

        return 's3';
    }

    protected function getSignatureScopeName(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);

        return 's3';
    }

    protected function getSignatureVersion(): string
    {
        @trigger_error('Using the client with an old version of Core is deprecated. Run "composer update async-aws/core".', \E_USER_DEPRECATED);

        return 's3v4';
    }

    
    protected function getSignerFactories(): array
    {
        return [
            's3v4' => function (string $service, string $region) {
                $configuration = $this->getConfiguration();
                $options = [];

                
                if (Configuration::optionExists('sendChunkedBody')) {
                    $options['sendChunkedBody'] = \filter_var($configuration->get('sendChunkedBody'), \FILTER_VALIDATE_BOOLEAN);
                }

                return new SignerV4ForS3($service, $region, $options);
            },
        ] + parent::getSignerFactories();
    }
}
