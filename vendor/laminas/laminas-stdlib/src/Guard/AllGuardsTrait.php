<?php

declare(strict_types=1);

namespace Laminas\Stdlib\Guard;


trait AllGuardsTrait
{
    use ArrayOrTraversableGuardTrait;
    use EmptyGuardTrait;
    use NullGuardTrait;
}
