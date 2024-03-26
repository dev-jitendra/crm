<?php

namespace AsyncAws\S3\Result;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Response;
use AsyncAws\Core\Result;
use AsyncAws\S3\Enum\RequestCharged;
use AsyncAws\S3\Enum\StorageClass;
use AsyncAws\S3\Input\ListPartsRequest;
use AsyncAws\S3\S3Client;
use AsyncAws\S3\ValueObject\Initiator;
use AsyncAws\S3\ValueObject\Owner;
use AsyncAws\S3\ValueObject\Part;


class ListPartsOutput extends Result implements \IteratorAggregate
{
    
    private $abortDate;

    
    private $abortRuleId;

    
    private $bucket;

    
    private $key;

    
    private $uploadId;

    
    private $partNumberMarker;

    
    private $nextPartNumberMarker;

    
    private $maxParts;

    
    private $isTruncated;

    
    private $parts = [];

    
    private $initiator;

    
    private $owner;

    
    private $storageClass;

    private $requestCharged;

    public function getAbortDate(): ?\DateTimeImmutable
    {
        $this->initialize();

        return $this->abortDate;
    }

    public function getAbortRuleId(): ?string
    {
        $this->initialize();

        return $this->abortRuleId;
    }

    public function getBucket(): ?string
    {
        $this->initialize();

        return $this->bucket;
    }

    public function getInitiator(): ?Initiator
    {
        $this->initialize();

        return $this->initiator;
    }

    public function getIsTruncated(): ?bool
    {
        $this->initialize();

        return $this->isTruncated;
    }

    
    public function getIterator(): \Traversable
    {
        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListPartsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getIsTruncated()) {
                $input->setPartNumberMarker($page->getNextPartNumberMarker());

                $this->registerPrefetch($nextPage = $client->ListParts($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getParts(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    public function getKey(): ?string
    {
        $this->initialize();

        return $this->key;
    }

    public function getMaxParts(): ?int
    {
        $this->initialize();

        return $this->maxParts;
    }

    public function getNextPartNumberMarker(): ?int
    {
        $this->initialize();

        return $this->nextPartNumberMarker;
    }

    public function getOwner(): ?Owner
    {
        $this->initialize();

        return $this->owner;
    }

    public function getPartNumberMarker(): ?int
    {
        $this->initialize();

        return $this->partNumberMarker;
    }

    
    public function getParts(bool $currentPageOnly = false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->parts;

            return;
        }

        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListPartsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getIsTruncated()) {
                $input->setPartNumberMarker($page->getNextPartNumberMarker());

                $this->registerPrefetch($nextPage = $client->ListParts($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getParts(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    
    public function getRequestCharged(): ?string
    {
        $this->initialize();

        return $this->requestCharged;
    }

    
    public function getStorageClass(): ?string
    {
        $this->initialize();

        return $this->storageClass;
    }

    public function getUploadId(): ?string
    {
        $this->initialize();

        return $this->uploadId;
    }

    protected function populateResult(Response $response): void
    {
        $headers = $response->getHeaders();

        $this->abortDate = isset($headers['x-amz-abort-date'][0]) ? new \DateTimeImmutable($headers['x-amz-abort-date'][0]) : null;
        $this->abortRuleId = $headers['x-amz-abort-rule-id'][0] ?? null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;

        $data = new \SimpleXMLElement($response->getContent());
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->key = ($v = $data->Key) ? (string) $v : null;
        $this->uploadId = ($v = $data->UploadId) ? (string) $v : null;
        $this->partNumberMarker = ($v = $data->PartNumberMarker) ? (int) (string) $v : null;
        $this->nextPartNumberMarker = ($v = $data->NextPartNumberMarker) ? (int) (string) $v : null;
        $this->maxParts = ($v = $data->MaxParts) ? (int) (string) $v : null;
        $this->isTruncated = ($v = $data->IsTruncated) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null;
        $this->parts = !$data->Part ? [] : $this->populateResultParts($data->Part);
        $this->initiator = !$data->Initiator ? null : new Initiator([
            'ID' => ($v = $data->Initiator->ID) ? (string) $v : null,
            'DisplayName' => ($v = $data->Initiator->DisplayName) ? (string) $v : null,
        ]);
        $this->owner = !$data->Owner ? null : new Owner([
            'DisplayName' => ($v = $data->Owner->DisplayName) ? (string) $v : null,
            'ID' => ($v = $data->Owner->ID) ? (string) $v : null,
        ]);
        $this->storageClass = ($v = $data->StorageClass) ? (string) $v : null;
    }

    
    private function populateResultParts(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new Part([
                'PartNumber' => ($v = $item->PartNumber) ? (int) (string) $v : null,
                'LastModified' => ($v = $item->LastModified) ? new \DateTimeImmutable((string) $v) : null,
                'ETag' => ($v = $item->ETag) ? (string) $v : null,
                'Size' => ($v = $item->Size) ? (string) $v : null,
            ]);
        }

        return $items;
    }
}
