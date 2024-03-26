<?php


namespace Espo\Tools\LinkManager;

class Type
{
    public const MANY_TO_MANY = 'manyToMany';
    public const MANY_TO_ONE = 'manyToOne';
    public const ONE_TO_MANY = 'oneToMany';
    public const ONE_TO_ONE_LEFT = 'oneToOneLeft';
    public const ONE_TO_ONE_RIGHT = 'oneToOneRight';
    public const CHILDREN_TO_PARENT = 'childrenToParent';
}
