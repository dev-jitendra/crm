<?php


namespace Espo\Core\FieldSanitize;

use Espo\Core\FieldSanitize\Sanitizer\Data;

interface Sanitizer
{
    public function sanitize(Data $data, string $field): void;
}
