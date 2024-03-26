<?php


namespace Espo\Classes\DefaultLayouts;

use Espo\Core\Utils\Metadata;

class DefaultSidePanelType
{
    private $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    
    public function get(string $scope): array
    {
        $list = [];

        if (
            $this->metadata->get(['entityDefs', $scope, 'fields', 'assignedUser', 'type']) === 'link' &&
            $this->metadata->get(['entityDefs', $scope, 'links', 'assignedUser', 'entity']) === 'User'
            ||
            $this->metadata->get(['entityDefs', $scope, 'fields', 'assignedUsers', 'type']) === 'linkMultiple' &&
            $this->metadata->get(['entityDefs', $scope, 'links', 'assignedUsers', 'entity']) === 'User'
        ) {
            $list[] = (object) ['name' => ':assignedUser'];
        }

        if (
            $this->metadata->get(['entityDefs', $scope, 'fields', 'teams', 'type']) === 'linkMultiple' &&
            $this->metadata->get(['entityDefs', $scope, 'links', 'teams', 'entity']) === 'Team'
        ) {
            $list[] = (object) ['name' => 'teams'];
        }

        return $list;
    }
}
