<?php


namespace Espo\Tools\LinkManager\Hook\Hooks;

use Espo\Core\Templates\Entities\Company;
use Espo\Core\Templates\Entities\Person;
use Espo\Tools\LinkManager\Hook\CreateHook;
use Espo\Tools\LinkManager\Params;
use Espo\Tools\LinkManager\Type;
use Espo\Modules\Crm\Entities\TargetList;
use Espo\Core\Utils\Metadata;

class TargetListCreate implements CreateHook
{
    public function __construct(private Metadata $metadata)
    {}

    public function process(Params $params): void
    {
        $toProcess =
            (
                $params->getEntityType() === TargetList::ENTITY_TYPE ||
                $params->getForeignEntityType() === TargetList::ENTITY_TYPE
            ) &&
            $params->getType() === Type::MANY_TO_MANY;

        if (!$toProcess) {
            return;
        }

        [$entityType, $link, $foreignLink] = $params->getEntityType() === TargetList::ENTITY_TYPE ?
            [
                $params->getForeignEntityType(),
                $params->getForeignLink(),
                $params->getLink(),
            ] :
            [
                $params->getEntityType(),
                $params->getLink(),
                $params->getForeignLink(),
            ];

        if (!$entityType) {
            return;
        }

        $type = $this->metadata->get(['scopes', $entityType, 'type']);

        if (!in_array($type, [Person::TEMPLATE_TYPE, Company::TEMPLATE_TYPE])) {
            return;
        }

        if ($link !== 'targetLists') {
            return;
        }

        $this->processInternal($entityType, $link, $foreignLink);
    }

    private function processInternal(string $entityType, string $link, string $foreignLink): void
    {
        $this->metadata->set('entityDefs', TargetList::ENTITY_TYPE, [
            'links' => [
                $foreignLink => [
                    'additionalColumns' => [
                        'optedOut' => [
                            'type' => 'bool',
                        ]
                    ],
                    'columnAttributeMap' => [
                        'optedOut' => 'isOptedOut',
                    ],
                ],
            ],
        ]);

        $this->metadata->set('entityDefs', $entityType, [
            'links' => [
                $link => [
                    'columnAttributeMap' => [
                        'optedOut' => 'targetListIsOptedOut',
                    ],
                ],
            ],
            'fields' => [
                'targetListIsOptedOut' => [
                    'type' => 'bool',
                    'notStorable' => true,
                    'readOnly' => true,
                    'disabled' => true,
                ],
            ]
        ]);

        $this->metadata->set('clientDefs', TargetList::ENTITY_TYPE, [
            'relationshipPanels' => [
                $foreignLink => [
                    'actionList' => [
                        [
                            'label' => 'Unlink All',
                            'action' => 'unlinkAllRelated',
                            'acl' => 'edit',
                            'data' => [
                                'link' => $foreignLink,
                            ],
                        ],
                    ],
                    'rowActionsView' => 'crm:views/target-list/record/row-actions/default',
                    'view' => 'crm:views/target-list/record/panels/relationship',
                    'massSelect' => true,
                    'removeDisabled' => true,
                ],
            ],
        ]);

        $targetLinkList = $this->metadata->get(['scopes', TargetList::ENTITY_TYPE, 'targetLinkList']) ?? [];

        if (!in_array($foreignLink, $targetLinkList)) {
            $targetLinkList[] = $foreignLink;

            $this->metadata->set('scopes', TargetList::ENTITY_TYPE, [
                'targetLinkList' => $targetLinkList,
            ]);
        }

        $this->metadata->save();
    }
}
