<?php


namespace Espo\Core\Field\DateTime;

use DateTimeImmutable;

interface DateTimeable
{
    public function toDateTime(): DateTimeImmutable;
}
