<?php


namespace Espo\Modules\Crm\Entities;

class CampaignLogRecord extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'CampaignLogRecord';

    public const ACTION_LEAD_CREATED = 'Lead Created';
    public const ACTION_SENT = 'Sent';
    public const ACTION_BOUNCED = 'Bounced';
    public const ACTION_OPTED_IN = 'Opted In';
    public const ACTION_OPTED_OUT = 'Opted Out';
    public const ACTION_OPENED = 'Opened';
    public const ACTION_CLICKED = 'Clicked';

    public const BOUNCED_TYPE_HARD = 'Hard';
    public const BOUNCED_TYPE_SOFT = 'Soft';
}
