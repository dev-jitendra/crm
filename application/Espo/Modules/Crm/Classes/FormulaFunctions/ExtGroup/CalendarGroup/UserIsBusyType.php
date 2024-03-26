<?php


namespace Espo\Modules\Crm\Classes\FormulaFunctions\ExtGroup\CalendarGroup;

use Espo\Core\Field\DateTime;
use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Func;
use Espo\Modules\Crm\Tools\Calendar\FetchParams;
use Espo\Modules\Crm\Tools\Calendar\Items\Event;
use Espo\Modules\Crm\Tools\Calendar\Service;
use Exception;
use RuntimeException;

class UserIsBusyType implements Func
{
    public function __construct(private Service $service) {}

    public function process(EvaluatedArgumentList $arguments): bool
    {
        if (count($arguments) < 3) {
            throw TooFewArguments::create(3);
        }

        $userId = $arguments[0];
        $from = $arguments[1];
        $to = $arguments[2];
        $entityType = $arguments[3] ?? null;
        $id = $arguments[4] ?? null;

        if (!is_string($userId)) {
            throw BadArgumentType::create(1, 'string');
        }

        if (!is_string($from)) {
            throw BadArgumentType::create(2, 'string');
        }

        if (!is_string($to)) {
            throw BadArgumentType::create(3, 'string');
        }

        if ($entityType !== null && !is_string($entityType)) {
            throw BadArgumentType::create(4, 'string');
        }

        if ($id !== null && !is_string($id)) {
            throw BadArgumentType::create(5, 'string');
        }

        $params = FetchParams::create(DateTime::fromString($from), DateTime::fromString($to))
            ->withSkipAcl();

        $ignoreList = [];

        if ($entityType && $id) {
            $ignoreList[] = (new Event(null, null, $entityType, []))->withId($id);
        }

        try {
            $ranges = $this->service->fetchBusyRanges($userId, $params, $ignoreList);
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $ranges !== [];
    }
}
