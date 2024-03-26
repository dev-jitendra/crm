<?php


namespace Espo\Controllers;

use Espo\Tools\Notification\RecordService as Service;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\RecordBase;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\SearchParams;
use Espo\Core\Select\Where\Item as WhereItem;

use stdClass;

class Notification extends RecordBase
{
    public static $defaultAction = 'list';

    
    public function getActionList(Request $request, Response $response): stdClass
    {
        $searchParamsAux = $this->searchParamsFetcher->fetch($request);

        $offset = $searchParamsAux->getOffset();
        $maxSize = $searchParamsAux->getMaxSize();

        $after = $request->getQueryParam('after');

        $searchParams = SearchParams
            ::create()
            ->withOffset($offset)
            ->withMaxSize($maxSize);

        if ($after) {
            $searchParams = $searchParams
                ->withWhereAdded(
                    WhereItem
                        ::createBuilder()
                        ->setAttribute('createdAt')
                        ->setType(WhereItem\Type::AFTER)
                        ->setValue($after)
                        ->build()
                );

        }

        $userId = $this->user->getId();

        $recordCollection = $this->getNotificationService()->get($userId, $searchParams);

        return (object) [
            'total' => $recordCollection->getTotal(),
            'list' => $recordCollection->getValueMapList(),
        ];
    }

    public function getActionNotReadCount(): int
    {
        $userId = $this->user->getId();

        return $this->getNotificationService()->getNotReadCount($userId);
    }

    public function postActionMarkAllRead(Request $request): bool
    {
        $userId = $this->user->getId();

        $this->getNotificationService()->markAllRead($userId);

        return true;
    }

    private function getNotificationService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
