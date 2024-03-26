<?php


namespace Espo\ORM\Type;

class RelationType
{
    public const MANY_MANY = 'manyMany';
    public const HAS_MANY = 'hasMany';
    public const BELONGS_TO = 'belongsTo';
    public const HAS_ONE = 'hasOne';
    public const BELONGS_TO_PARENT = 'belongsToParent';
    public const HAS_CHILDREN = 'hasChildren';
}
