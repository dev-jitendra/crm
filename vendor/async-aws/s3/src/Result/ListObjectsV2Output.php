<?php

namespace AsyncAws\S3\Result;

use AsyncAws\Core\Exception\InvalidArgument;
use AsyncAws\Core\Response;
use AsyncAws\Core\Result;
use AsyncAws\S3\Enum\EncodingType;
use AsyncAws\S3\Input\ListObjectsV2Request;
use AsyncAws\S3\S3Client;
use AsyncAws\S3\ValueObject\AwsObject;
use AsyncAws\S3\ValueObject\CommonPrefix;
use AsyncAws\S3\ValueObject\Owner;


class ListObjectsV2Output extends Result implements \IteratorAggregate
{
    
    private $isTruncated;

    
    private $contents = [];

    
    private $name;

    
    private $prefix;

    
    private $delimiter;

    
    private $maxKeys;

    
    private $commonPrefixes = [];

    
    private $encodingType;

    
    private $keyCount;

    
    private $continuationToken;

    
    private $nextContinuationToken;

    
    private $startAfter;

    
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
        if (!$this->input instanceof ListObjectsV2Request) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getNextContinuationToken()) {
                $input->setContinuationToken($page->getNextContinuationToken());

                $this->registerPrefetch($nextPage = $client->ListObjectsV2($input));
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

    
    public function getContents(bool $currentPageOnly = false): iterable
    {
        if ($currentPageOnly) {
            $this->initialize();
            yield from $this->contents;

            return;
        }

        $client = $this->awsClient;
        if (!$client instanceof S3Client) {
            throw new InvalidArgument('missing client injected in paginated result');
        }
        if (!$this->input instanceof ListObjectsV2Request) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getNextContinuationToken()) {
                $input->setContinuationToken($page->getNextContinuationToken());

                $this->registerPrefetch($nextPage = $client->ListObjectsV2($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getContents(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    public function getContinuationToken(): ?string
    {
        $this->initialize();

        return $this->continuationToken;
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
        if (!$this->input instanceof ListObjectsV2Request) {
            throw new InvalidArgument('missing last request injected in paginated result');
        }
        $input = clone $this->input;
        $page = $this;
        while (true) {
            if ($page->getNextContinuationToken()) {
                $input->setContinuationToken($page->getNextContinuationToken());

                $this->registerPrefetch($nextPage = $client->ListObjectsV2($input));
            } else {
                $nextPage = null;
            }

            yield from $page->getContents(true);
            yield from $page->getCommonPrefixes(true);

            if (null === $nextPage) {
                break;
            }

            $this->unregisterPrefetch($nextPage);
            $page = $nextPage;
        }
    }

    public function getKeyCount(): ?int
    {
        $this->initialize();

        return $this->keyCount;
    }

    public function getMaxKeys(): ?int
    {
        $this->initialize();

        return $this->maxKeys;
    }

    public function getName(): ?string
    {
        $this->initialize();

        return $this->name;
    }

    public function getNextContinuationToken(): ?string
    {
        $this->initialize();

        return $this->nextContinuationToken;
    }

    public function getPrefix(): ?string
    {
        $this->initialize();

        return $this->prefix;
    }

    public function getStartAfter(): ?string
    {
        $this->initialize();

        return $this->startAfter;
    }

    protected function populateResult(Response $response): void
    {
        $data = new \SimpleXMLElement($response->getContent());
        $this->isTruncated = ($v = $data->IsTruncated) ? filter_var((string) $v, \FILTER_VALIDATE_BOOLEAN) : null;
        $this->contents = !$data->Contents ? [] : $this->populateResultObjectList($data->Contents);
        $this->name = ($v = $data->Name) ? (string) $v : null;
        $this->prefix = ($v = $data->Prefix) ? (string) $v : null;
        $this->delimiter = ($v = $data->Delimiter) ? (string) $v : null;
        $this->maxKeys = ($v = $data->MaxKeys) ? (int) (string) $v : null;
        $this->commonPrefixes = !$data->CommonPrefixes ? [] : $this->populateResultCommonPrefixList($data->CommonPrefixes);
        $this->encodingType = ($v = $data->EncodingType) ? (string) $v : null;
        $this->keyCount = ($v = $data->KeyCount) ? (int) (string) $v : null;
        $this->continuationToken = ($v = $data->ContinuationToken) ? (string) $v : null;
        $this->nextContinuationToken = ($v = $data->NextContinuationToken) ? (string) $v : null;
        $this->startAfter = ($v = $data->StartAfter) ? (string) $v : null;
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

    
    private function populateResultObjectList(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new AwsObject([
                'Key' => ($v = $item->Key) ? (string) $v : null,
                'LastModified' => ($v = $item->LastModified) ? new \DateTimeImmutable((string) $v) : null,
                'ETag' => ($v = $item->ETag) ? (string) $v : null,
                'Size' => ($v = $item->Size) ? (string) $v : null,
                'StorageClass' => ($v = $item->StorageClass) ? (string) $v : null,
                'Owner' => !$item->Owner ? null : new Owner([
                    'DisplayName' => ($v = $item->Owner->DisplayName) ? (string) $v : null,
                    'ID' => ($v = $item->Owner->ID) ? (string) $v : null,
                ]),
            ]);
        }

        return $items;
    }
}
