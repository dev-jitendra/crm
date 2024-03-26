<?php


namespace Espo\Core\Templates\Entities;

use Espo\Core\ORM\Entity;

class Event extends Entity
{
    public const TEMPLATE_TYPE = 'Event';

    public const STATUS_PLANNED = 'Planned';
    public const STATUS_HELD = 'Held';
    public const STATUS_NOT_HELD = 'Not Held';
}
