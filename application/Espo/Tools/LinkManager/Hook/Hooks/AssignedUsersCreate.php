<?php


namespace Espo\Tools\LinkManager\Hook\Hooks;

use Espo\Tools\LinkManager\Hook\CreateHook;
use Espo\Tools\LinkManager\Params;
use Espo\Tools\LinkManager\Type;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;

class AssignedUsersCreate implements CreateHook
{
    private const LINK_NAME = 'assignedUsers';

    public function __construct(private Metadata $metadata)
    {}

    public function process(Params $params): void
    {
        if ($params->getType() !== Type::MANY_TO_MANY) {
            return;
        }

        $foreignEntityType = $params->getForeignEntityType();
        $entityType = $params->getEntityType();

        if (!$foreignEntityType || !$entityType) {
            return;
        }

        if (
            $params->getEntityType() === User::ENTITY_TYPE &&
            $params->getForeignLink() === self::LINK_NAME
        ) {
            $this->processInternal($foreignEntityType);

            return;
        }

        if (
            $params->getForeignEntityType() === User::ENTITY_TYPE &&
            $params->getLink() === self::LINK_NAME
        ) {
            $this->processInternal($entityType);
        }
    }

    private function processInternal(string $entityType): void
    {
        $fieldType = $this->metadata->get(['entityDefs', $entityType, 'fields', self::LINK_NAME, 'type']);

        if ($fieldType !== 'linkMultiple') {
            return;
        }

        $this->metadata->set('entityDefs', $entityType, [
            'fields' => [
                self::LINK_NAME => [
                    'view' => 'views/fields/assigned-users',
                ],
            ]
        ]);

        $this->metadata->save();
    }
}
