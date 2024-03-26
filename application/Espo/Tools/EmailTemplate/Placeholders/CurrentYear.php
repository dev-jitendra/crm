<?php


namespace Espo\Tools\EmailTemplate\Placeholders;

use DateTime;
use DateTimezone;
use Espo\Core\Utils\Config;
use Espo\Tools\EmailTemplate\Data;
use Espo\Tools\EmailTemplate\Placeholder;
use Exception;
use RuntimeException;


class CurrentYear implements Placeholder
{
    public function __construct(
        private Config $config
    ) {}

    public function get(Data $data): string
    {
        try {
            $now = new DateTime('now', new DateTimezone($this->config->get('timeZone')));
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $now->format('Y');
    }
}
