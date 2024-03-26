<?php


namespace Espo\Modules\Crm\Tools\Activities\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Record\SearchParamsFetcher;
use Espo\Entities\User;
use Espo\Modules\Crm\Tools\Activities\Service as Service;


class GetUpcoming implements Action
{
    public function __construct(
        private User $user,
        private SearchParamsFetcher $searchParamsFetcher,
        private Service $service
    ) {}

    public function process(Request $request): Response
    {
        $userId = $request->getQueryParam('userId');

        if (!$userId) {
            $userId = $this->user->getId();
        }

        $searchParams = $this->searchParamsFetcher->fetch($request);

        $offset = $searchParams->getOffset();
        $maxSize = $searchParams->getMaxSize();

        $entityTypeList = (array) ($request->getQueryParams()['entityTypeList'] ?? null);

        $futureDays = intval($request->getQueryParam('futureDays'));

        $recordCollection = $this->service->getUpcomingActivities(
            $userId,
            [
                'offset' => $offset,
                'maxSize' => $maxSize,
            ],
            $entityTypeList,
            $futureDays
        );

        return ResponseComposer::json([
            'total' => $recordCollection->getTotal(),
            'list' => $recordCollection->getValueMapList(),
        ]);
    }
}
