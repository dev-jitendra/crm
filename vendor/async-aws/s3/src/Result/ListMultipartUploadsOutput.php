<?php

namespace AsyncAws\S3\Result;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Response;
use AsyncAws\Core\Result;
use AsyncAws\S3\Enum\EncodingType;
use AsyncAws\S3\Input\ListMultipartUploadsRequest;
use AsyncAws\S3\S3Client;
use AsyncAws\S3\ValueObject\CommonPrefix;
use AsyncAws\S3\ValueObject\Initiator;
use AsyncAws\S3\ValueObject\MultipartUpload;
use AsyncAws\S3\ValueObject\Owner;


class ListMultipartUploadsOutput extends Result implements \IteratorAggregate
{
    
    private $bucket;

    
    private $keyMarker;

    
    private $uploadIdMarker;

    
    private $nextKeyMarker;

    
    private $prefix;

    
    private $delimiter;

    
    private $nextUploadIdMarker;

    
    private $maxUploads;

    
    private $isTruncated;

    
    private $uploads = [];

    
    private $commonPrefixes = [];

    
    private $encodingType;

    public function getBucket(): ?string
    {
        $this->initialize();

        return $this->bucket;
    }

    
    public function getCommonPrefixes(bool $currentPageOnly = false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->commonPrefixes;

            return;
        }

        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListMultipartUploadsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getIsTruncated()) {
                $input->setKeyMarker($page->getNextKeyMarker());

                $input->setUploadIdMarker($page->getNextUploadIdMarker());

                $this->registerPrefetch($nextPage = $client->ListMultipartUploads($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getCommonPrefixes(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    public function getDelimiter(): ?string
    {
        $this->initialize();

        return $this->delimiter;
    }

    
    public function getEncodingType(): ?string
    {
        $this->initialize();

        return $this->encodingType;
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
        if (!$this->input instanceof ListMultipartUploadsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getIsTruncated()) {
                $input->setKeyMarker($page->getNextKeyMarker());

                $input->setUploadIdMarker($page->getNextUploadIdMarker());

                $this->registerPrefetch($nextPage = $client->ListMultipartUploads($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getUploads(true);
            yield from $page->getCommonPrefixes(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    public function getKeyMarker(): ?string
    {
        $this->initialize();

        return $this->keyMarker;
    }

    public function getMaxUploads(): ?int
    {
        $this->initialize();

        return $this->maxUploads;
    }

    public function getNextKeyMarker(): ?string
    {
        $this->initialize();

        return $this->nextKeyMarker;
    }

    public function getNextUploadIdMarker(): ?string
    {
        $this->initialize();

        return $this->nextUploadIdMarker;
    }

    public function getPrefix(): ?string
    {
        $this->initialize();

        return $this->prefix;
    }

    public function getUploadIdMarker(): ?string
    {
        $this->initialize();

        return $this->uploadIdMarker;
    }

    
    public function getUploads(bool $currentPageOnly = false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->uploads;

            return;
        }

        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListMultipartUploadsRequest) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getIsTruncated()) {
                $input->setKeyMarker($page->getNextKeyMarker());

                $input->setUploadIdMarker($page->getNextUploadIdMarker());

                $this->registerPrefetch($nextPage = $client->ListMultipartUploads($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getUploads(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    protected function populateResult(Response $response): void
    {
        $data = new \SimpleXMLElement($response->getContent());
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->keyMarker = ($v = $data->KeyMarker) ? (string) $v : null;
        $this->uploadIdMarker = ($v = $data->UploadIdMarker) ? (string) $v : null;
        $this->nextKeyMarker = ($v = $data->NextKeyMarker) ? (string) $v : null;
        $this->prefix = ($v = $data->Prefix) ? (string) $v : null;
        $this->delimiter = ($v = $data->Delimiter) ? (string) $v : null;
        $this->nextUploadIdMarker = ($v = $data->NextUploadIdMarker) ? (string) $v : null;
        $this->maxUploads = ($v = $data->MaxUploads) ? (int) (string) $v : null;
        $this->isTruncated = ($v = $data->IsTruncated) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null;
        $this->uploads = !$data->Upload ? [] : $this->populateResultMultipartUploadList($data->Upload);
        $this->commonPrefixes = !$data->CommonPrefixes ? [] : $this->populateResultCommonPrefixList($data->CommonPrefixes);
        $this->encodingType = ($v = $data->EncodingType) ? (string) $v : null;
    }

    
    private function populateResultCommonPrefixList(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new CommonPrefix([
                'Prefix' => ($v = $item->Prefix) ? (string) $v : null,
            ]);
        }

        return $items;
    }

    
    private function populateResultMultipartUploadList(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new MultipartUpload([
                'UploadId' => ($v = $item->UploadId) ? (string) $v : null,
                'Key' => ($v = $item->Key) ? (string) $v : null,
                'Initiated' => ($v = $item->Initiated) ? new \DateTimeImmutable((string) $v) : null,
                'StorageClass' => ($v = $item->StorageClass) ? (string) $v : null,
                'Owner' => !$item->Owner ? null : new Owner([
                    'DisplayName' => ($v = $item->Owner->DisplayName) ? (string) $v : null,
                    'ID' => ($v = $item->Owner->ID) ? (string) $v : null,
                ]),
                'Initiator' => !$item->Initiator ? null : new Initiator([
                    'ID' => ($v = $item->Initiator->ID) ? (string) $v : null,
                    'DisplayName' => ($v = $item->Initiator->DisplayName) ? (string) $v : null,
                ]),
            ]);
        }

        return $items;
    }
}
