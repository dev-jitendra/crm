<?php


namespace Espo\Modules\Crm\Tools\Calendar\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Field\DateTime;
use Espo\Modules\Crm\Tools\Calendar\Item as CalendarItem;
use Espo\Modules\Crm\Tools\Calendar\Service;

use stdClass;


class GetBusyRanges implements Action
{
    public function __construct(private Service $calendarService) {}

    public function process(Request $request): Response
    {
        $from = $request->getQueryParam('from');
        $to = $request->getQueryParam('to');
        $userIdListString = $request->getQueryParam('userIdList');

        if (!$from || !$to || !$userIdListString) {
            throw new BadRequest();
        }

        $userIdList = explode(',', $userIdListString);

        $map = $this->calendarService->fetchBusyRangesForUsers(
            $userIdList,
            DateTime::fromString($from),
            DateTime::fromString($to),
            $request->getQueryParam('entityType'),
            $request->getQueryParam('entityId')
        );

        $result = (object) [];

        foreach ($map as $userId => $itemList) {
            $result->$userId = self::itemListToRaw($itemList);
        }

        return ResponseComposer::json($result);
    }

    
    private static function itemListToRaw(array $itemList): array
    {
        return array_map(fn (CalendarItem $item) => $item->getRaw(), $itemList);
    }
}
