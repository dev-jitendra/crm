<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Record\SearchParamsFetcher;
use Espo\Tools\ActionHistory\Service as Service;

use stdClass;

class LastViewed
{
    public function __construct(private SearchParamsFetcher $searchParamsFetcher, private Service $service)
    {}

    public function getActionIndex(Request $request): stdClass
    {
        $searchParams = $this->searchParamsFetcher->fetch($request);

        $offset = $searchParams->getOffset();
        $maxSize = $searchParams->getMaxSize();

        $result = $this->service->getLastViewed($maxSize, $offset);

        return (object) [
            'total' => $result->getTotal(),
            'list' => $result->getValueMapList(),
        ];
    }
}
