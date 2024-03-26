<?php


namespace Espo\Core\Acl\AccessChecker;

use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;


class ScopeChecker
{
    public function __construct()
    {}

    public function check(ScopeData $data, ?string $action = null, ?ScopeCheckerData $checkerData = null): bool
    {
        if ($data->isFalse()) {
            return false;
        }

        if ($data->isTrue()) {
            return true;
        }

        if ($action === null) {
            if ($data->hasNotNo()) {
                return true;
            }

            return false;
        }

        $level = $data->get($action);

        if ($level === Table::LEVEL_ALL || $level === Table::LEVEL_YES) {
            return true;
        }

        if ($level === Table::LEVEL_NO) {
            return false;
        }

        if (!$checkerData) {
            return false;
        }

        if ($level === Table::LEVEL_OWN || $level === Table::LEVEL_TEAM) {
            if ($checkerData->isOwn()) {
                return true;
            }
        }

        if ($level === Table::LEVEL_TEAM) {
            if ($checkerData->inTeam()) {
                return true;
            }
        }

        return false;
    }
}
