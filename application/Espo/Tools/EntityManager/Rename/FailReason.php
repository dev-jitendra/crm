<?php


namespace Espo\Tools\EntityManager\Rename;

class FailReason
{
    public const ENV_NOT_SUPPORTED = 'envNotSupported';

    public const TABLE_EXISTS = 'tableExists';

    public const DOES_NOT_EXIST = 'doesNotExist';

    public const NOT_CUSTOM = 'notCustom';

    public const NAME_USED = 'nameUsed';

    public const NAME_NOT_ALLOWED = 'nameIsNotAllosed';

    public const NAME_BAD = 'nameBad';

    public const NAME_TOO_LONG = 'nameTooLong';

    public const NAME_TOO_SHORT = 'nameTooShort';

    public const ERROR = 'error';
}
