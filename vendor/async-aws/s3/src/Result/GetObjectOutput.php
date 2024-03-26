<?php

namespace AsyncAws\S3\Result;

use AsyncAws\Core\Response;
use AsyncAws\Core\Result;
use AsyncAws\Core\Stream\ResultStream;
use AsyncAws\S3\Enum\ObjectLockLegalHoldStatus;
use AsyncAws\S3\Enum\ObjectLockMode;
use AsyncAws\S3\Enum\ReplicationStatus;
use AsyncAws\S3\Enum\RequestCharged;
use AsyncAws\S3\Enum\ServerSideEncryption;
use AsyncAws\S3\Enum\StorageClass;

class GetObjectOutput extends Result
{
    
    private $body;

    
    private $deleteMarker;

    
    private $acceptRanges;

    
    private $expiration;

    
    private $restore;

    
    private $lastModified;

    
    private $contentLength;

    
    private $etag;

    
    private $missingMeta;

    
    private $versionId;

    
    private $cacheControl;

    
    private $contentDisposition;

    
    private $contentEncoding;

    
    private $contentLanguage;

    
    private $contentRange;

    
    private $contentType;

    
    private $expires;

    
    private $websiteRedirectLocation;

    
    private $serverSideEncryption;

    
    private $metadata = [];

    
    private $sseCustomerAlgorithm;

    
    private $sseCustomerKeyMd5;

    
    private $sseKmsKeyId;

    
    private $bucketKeyEnabled;

    
    private $storageClass;

    private $requestCharged;

    
    private $replicationStatus;

    
    private $partsCount;

    
    private $tagCount;

    
    private $objectLockMode;

    
    private $objectLockRetainUntilDate;

    
    private $objectLockLegalHoldStatus;

    public function getAcceptRanges(): ?string
    {
        $this->initialize();

        return $this->acceptRanges;
    }

    public function getBody(): ResultStream
    {
        $this->initialize();

        return $this->body;
    }

    public function getBucketKeyEnabled(): ?bool
    {
        $this->initialize();

        return $this->bucketKeyEnabled;
    }

    public function getCacheControl(): ?string
    {
        $this->initialize();

        return $this->cacheControl;
    }

    public function getContentDisposition(): ?string
    {
        $this->initialize();

        return $this->contentDisposition;
    }

    public function getContentEncoding(): ?string
    {
        $this->initialize();

        return $this->contentEncoding;
    }

    public function getContentLanguage(): ?string
    {
        $this->initialize();

        return $this->contentLanguage;
    }

    public function getContentLength(): ?string
    {
        $this->initialize();

        return $this->contentLength;
    }

    public function getContentRange(): ?string
    {
        $this->initialize();

        return $this->contentRange;
    }

    public function getContentType(): ?string
    {
        $this->initialize();

        return $this->contentType;
    }

    public function getDeleteMarker(): ?bool
    {
        $this->initialize();

        return $this->deleteMarker;
    }

    public function getEtag(): ?string
    {
        $this->initialize();

        return $this->etag;
    }

    public function getExpiration(): ?string
    {
        $this->initialize();

        return $this->expiration;
    }

    public function getExpires(): ?\DateTimeImmutable
    {
        $this->initialize();

        return $this->expires;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        $this->initialize();

        return $this->lastModified;
    }

    
    public function getMetadata(): array
    {
        $this->initialize();

        return $this->metadata;
    }

    public function getMissingMeta(): ?int
    {
        $this->initialize();

        return $this->missingMeta;
    }

    
    public function getObjectLockLegalHoldStatus(): ?string
    {
        $this->initialize();

        return $this->objectLockLegalHoldStatus;
    }

    
    public function getObjectLockMode(): ?string
    {
        $this->initialize();

        return $this->objectLockMode;
    }

    public function getObjectLockRetainUntilDate(): ?\DateTimeImmutable
    {
        $this->initialize();

        return $this->objectLockRetainUntilDate;
    }

    public function getPartsCount(): ?int
    {
        $this->initialize();

        return $this->partsCount;
    }

    
    public function getReplicationStatus(): ?string
    {
        $this->initialize();

        return $this->replicationStatus;
    }

    
    public function getRequestCharged(): ?string
    {
        $this->initialize();

        return $this->requestCharged;
    }

    public function getRestore(): ?string
    {
        $this->initialize();

        return $this->restore;
    }

    
    public function getServerSideEncryption(): ?string
    {
        $this->initialize();

        return $this->serverSideEncryption;
    }

    public function getSseCustomerAlgorithm(): ?string
    {
        $this->initialize();

        return $this->sseCustomerAlgorithm;
    }

    public function getSseCustomerKeyMd5(): ?string
    {
        $this->initialize();

        return $this->sseCustomerKeyMd5;
    }

    public function getSseKmsKeyId(): ?string
    {
        $this->initialize();

        return $this->sseKmsKeyId;
    }

    
    public function getStorageClass(): ?string
    {
        $this->initialize();

        return $this->storageClass;
    }

    public function getTagCount(): ?int
    {
        $this->initialize();

        return $this->tagCount;
    }

    public function getVersionId(): ?string
    {
        $this->initialize();

        return $this->versionId;
    }

    public function getWebsiteRedirectLocation(): ?string
    {
        $this->initialize();

        return $this->websiteRedirectLocation;
    }

    protected function populateResult(Response $response): void
    {
        $headers = $response->getHeaders();

        $this->deleteMarker = isset($headers['x-amz-delete-marker'][0]) ? filter_var($headers['x-amz-delete-marker'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->acceptRanges = $headers['accept-ranges'][0] ?? null;
        $this->expiration = $headers['x-amz-expiration'][0] ?? null;
        $this->restore = $headers['x-amz-restore'][0] ?? null;
        $this->lastModified = isset($headers['last-modified'][0]) ? new \DateTimeImmutable($headers['last-modified'][0]) : null;
        $this->contentLength = $headers['content-length'][0] ?? null;
        $this->etag = $headers['etag'][0] ?? null;
        $this->missingMeta = isset($headers['x-amz-missing-meta'][0]) ? filter_var($headers['x-amz-missing-meta'][0], \FILTER_VALIDATE_INT) : null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->cacheControl = $headers['cache-control'][0] ?? null;
        $this->contentDisposition = $headers['content-disposition'][0] ?? null;
        $this->contentEncoding = $headers['content-encoding'][0] ?? null;
        $this->contentLanguage = $headers['content-language'][0] ?? null;
        $this->contentRange = $headers['content-range'][0] ?? null;
        $this->contentType = $headers['content-type'][0] ?? null;
        $this->expires = isset($headers['expires'][0]) ? new \DateTimeImmutable($headers['expires'][0]) : null;
        $this->websiteRedirectLocation = $headers['x-amz-website-redirect-location'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->sseCustomerAlgorithm = $headers['x-amz-server-side-encryption-customer-algorithm'][0] ?? null;
        $this->sseCustomerKeyMd5 = $headers['x-amz-server-side-encryption-customer-key-md5'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->storageClass = $headers['x-amz-storage-class'][0] ?? null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $this->replicationStatus = $headers['x-amz-replication-status'][0] ?? null;
        $this->partsCount = isset($headers['x-amz-mp-parts-count'][0]) ? filter_var($headers['x-amz-mp-parts-count'][0], \FILTER_VALIDATE_INT) : null;
        $this->tagCount = isset($headers['x-amz-tagging-count'][0]) ? filter_var($headers['x-amz-tagging-count'][0], \FILTER_VALIDATE_INT) : null;
        $this->objectLockMode = $headers['x-amz-object-lock-mode'][0] ?? null;
        $this->objectLockRetainUntilDate = isset($headers['x-amz-object-lock-retain-until-date'][0]) ? new \DateTimeImmutable($headers['x-amz-object-lock-retain-until-date'][0]) : null;
        $this->objectLockLegalHoldStatus = $headers['x-amz-object-lock-legal-hold'][0] ?? null;

        $this->metadata = [];
        foreach ($headers as $name => $value) {
            if ('x-amz-meta-' === substr($name, 0, 11)) {
                $this->metadata[substr($name, 11)] = $value[0];
            }
        }

        $this->body = $response->toStream();
    }
}
