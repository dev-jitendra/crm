<?php


namespace Espo\Tools\EntityManager\Hook\Hooks;

use Espo\Core\Templates\Entities\BasePlus;
use Espo\Core\Templates\Entities\Company;
use Espo\Core\Templates\Entities\Person;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Modules\Crm\Entities\Task;
use Espo\Tools\EntityManager\Hook\CreateHook;
use Espo\Tools\EntityManager\Params;

class PlusDeleteHook implements CreateHook
{
    public function __construct(
        private Config $config,
        private Metadata $metadata
    ) {}

    public function process(Params $params): void
    {
        if (
            !in_array($params->getType(), [
                BasePlus::TEMPLATE_TYPE,
                Company::TEMPLATE_TYPE,
                Person::TEMPLATE_TYPE,
            ])
        ) {
            return;
        }

        $name = $params->getName();

        $activitiesEntityTypeList = $this->config->get('activitiesEntityList', []);
        $historyEntityTypeList = $this->config->get('historyEntityList', []);

        $entityTypeList = array_merge($activitiesEntityTypeList, $historyEntityTypeList);
        $entityTypeList[] = Task::ENTITY_TYPE;
        $entityTypeList = array_unique($entityTypeList);

        foreach ($entityTypeList as $entityType) {
            if (!$this->metadata->get(['entityDefs', $entityType, 'fields', 'parent', 'entityList'])) {
                continue;
            }

            $list = $this->metadata->get(['entityDefs', $entityType, 'fields', 'parent', 'entityList'], []);

            if (in_array($name, $list)) {
                $key = array_search($name, $list);

                unset($list[$key]);

                $list = array_values($list);

                $data = [
                    'fields' => [
                        'parent' => ['entityList' => $list]
                    ]
                ];

                $this->metadata->set('entityDefs', $entityType, $data);
            }
        }

        $this->metadata->save();
    }
}
