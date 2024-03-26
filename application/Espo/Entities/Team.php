<?php


namespace Espo\Entities;

use Espo\Core\Field\Link;

class Team extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'Team';

    public const RELATIONSHIP_ENTITY_TEAM = 'EntityTeam';
    public const RELATIONSHIP_TEAM_USER = 'TeamUser';

    public function getWorkingTimeCalendar(): ?Link
    {
        
        return $this->getValueObject('workingTimeCalendar');
    }

    public function getLayoutSet(): ?Link
    {
        
        return $this->getValueObject('layoutSet');
    }
}
