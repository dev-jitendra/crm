<?php


namespace Espo\Services;


class ActionHistoryRecord extends Record
{
    protected $actionHistoryDisabled = true;

    protected $listCountQueryDisabled = true;

    protected $forceSelectAllAttributes = true;
}
