<?php


namespace Espo\Tools\EmailTemplate\Placeholders;

use Espo\Core\Utils\DateTime;
use Espo\Tools\EmailTemplate\Data;
use Espo\Tools\EmailTemplate\Placeholder;


class Now implements Placeholder
{
    public function __construct(
        private DateTime $dateTime
    ) {}

    public function get(Data $data): string
    {
        return $this->dateTime->getNowString();
    }
}
