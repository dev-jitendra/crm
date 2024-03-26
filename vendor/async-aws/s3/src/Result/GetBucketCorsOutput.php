<?php

namespace AsyncAws\S3\Result;

use AsyncAws\Core\Response;
use AsyncAws\Core\Result;
use AsyncAws\S3\ValueObject\CORSRule;

class GetBucketCorsOutput extends Result
{
    
    private $corsRules = [];

    
    public function getCorsRules(): array
    {
        $this->initialize();

        return $this->corsRules;
    }

    protected function populateResult(Response $response): void
    {
        $data = new \SimpleXMLElement($response->getContent());
        $this->corsRules = !$data->CORSRule ? [] : $this->populateResultCORSRules($data->CORSRule);
    }

    
    private function populateResultAllowedHeaders(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $a = ($v = $item) ? (string) $v : null;
            if (null !== $a) {
                $items[] = $a;
            }
        }

        return $items;
    }

    
    private function populateResultAllowedMethods(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $a = ($v = $item) ? (string) $v : null;
            if (null !== $a) {
                $items[] = $a;
            }
        }

        return $items;
    }

    
    private function populateResultAllowedOrigins(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $a = ($v = $item) ? (string) $v : null;
            if (null !== $a) {
                $items[] = $a;
            }
        }

        return $items;
    }

    
    private function populateResultCORSRules(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $items[] = new CORSRule([
                'AllowedHeaders' => !$item->AllowedHeader ? [] : $this->populateResultAllowedHeaders($item->AllowedHeader),
                'AllowedMethods' => $this->populateResultAllowedMethods($item->AllowedMethod),
                'AllowedOrigins' => $this->populateResultAllowedOrigins($item->AllowedOrigin),
                'ExposeHeaders' => !$item->ExposeHeader ? [] : $this->populateResultExposeHeaders($item->ExposeHeader),
                'MaxAgeSeconds' => ($v = $item->MaxAgeSeconds) ? (int) (string) $v : null,
            ]);
        }

        return $items;
    }

    
    private function populateResultExposeHeaders(\SimpleXMLElement $xml): array
    {
        $items = [];
        foreach ($xml as $item) {
            $a = ($v = $item) ? (string) $v : null;
            if (null !== $a) {
                $items[] = $a;
            }
        }

        return $items;
    }
}
