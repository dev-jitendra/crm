<?php


namespace Espo\Services;


class AuthLogRecord extends Record
{
    protected $internalAttributeList = [];

    protected $actionHistoryDisabled = true;

    protected $forceSelectAllAttributes = true;
}
