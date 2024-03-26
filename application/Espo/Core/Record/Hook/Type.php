<?php


namespace Espo\Core\Record\Hook;

class Type
{
    public const BEFORE_CREATE = 'beforeCreate';
    public const BEFORE_READ = 'beforeRead';
    public const BEFORE_UPDATE = 'beforeUpdate';
    public const BEFORE_DELETE = 'beforeDelete';
    public const BEFORE_LINK = 'beforeLink';
    public const BEFORE_UNLINK = 'beforeUnlink';
}
