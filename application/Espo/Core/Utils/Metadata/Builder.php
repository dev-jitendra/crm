<?php


namespace Espo\Core\Utils\Metadata;

use Espo\Core\Utils\DataUtil;
use Espo\Core\Utils\Resource\Reader as ResourceReader;
use Espo\Core\Utils\Resource\Reader\Params as ResourceReaderParams;
use Espo\Core\Utils\Util;
use stdClass;

class Builder
{
    
    private $forceAppendPathList = [
        ['app', 'rebuild', 'actionClassNameList'],
        ['app', 'fieldProcessing', 'readLoaderClassNameList'],
        ['app', 'fieldProcessing', 'listLoaderClassNameList'],
        ['app', 'fieldProcessing', 'saverClassNameList'],
        ['app', 'hook', 'suppressClassNameList'],
        ['app', 'api', 'globalMiddlewareClassNameList'],
        ['app', 'api', 'routeMiddlewareClassNameListMap', self::ANY_KEY],
        ['app', 'api', 'controllerMiddlewareClassNameListMap', self::ANY_KEY],
        ['app', 'api', 'controllerActionMiddlewareClassNameListMap', self::ANY_KEY],
        ['app', 'entityManager', 'createHookClassNameList'],
        ['app', 'entityManager', 'deleteHookClassNameList'],
        ['app', 'entityManager', 'updateHookClassNameList'],
        ['app', 'linkManager', 'createHookClassNameList'],
        ['app', 'linkManager', 'deleteHookClassNameList'],
        ['recordDefs', self::ANY_KEY, 'readLoaderClassNameList'],
        ['recordDefs', self::ANY_KEY, 'listLoaderClassNameList'],
        ['recordDefs', self::ANY_KEY, 'saverClassNameList'],
        ['recordDefs', self::ANY_KEY, 'selectApplierClassNameList'],
        ['recordDefs', self::ANY_KEY, 'beforeReadHookClassNameList'],
        ['recordDefs', self::ANY_KEY, 'beforeCreateHookClassNameList'],
        ['recordDefs', self::ANY_KEY, 'beforeUpdateHookClassNameList'],
        ['recordDefs', self::ANY_KEY, 'beforeDeleteHookClassNameList'],
        ['recordDefs', self::ANY_KEY, 'beforeLinkHookClassNameList'],
        ['recordDefs', self::ANY_KEY, 'beforeUnlinkHookClassNameList'],
    ];

    private const ANY_KEY = '__ANY__';

    public function __construct(
        private ResourceReader $resourceReader,
        private BuilderHelper $builderHelper
    ) {}

    public function build(): stdClass
    {
        $readerParams = ResourceReaderParams::create()
            ->withForceAppendPathList($this->forceAppendPathList);

        $data = $this->resourceReader->read('metadata', $readerParams);

        $this->addAdditionalField($data);

        return $data;
    }

    private function addAdditionalField(stdClass $data): void
    {
        if (!isset($data->entityDefs)) {
            return;
        }

        $fieldDefinitionList = Util::objectToArray($data->fields);

        foreach (get_object_vars($data->entityDefs) as $entityType => $entityDefsItem) {
            if (isset($data->entityDefs->$entityType->collection)) {
                
                $collectionItem = $data->entityDefs->$entityType->collection;

                if (isset($collectionItem->orderBy)) {
                    $collectionItem->sortBy = $collectionItem->orderBy;
                }
                else if (isset($collectionItem->sortBy)) {
                    $collectionItem->orderBy = $collectionItem->sortBy;
                }

                if (isset($collectionItem->order)) {
                    $collectionItem->asc = $collectionItem->order === 'asc';
                }
                else if (isset($collectionItem->asc)) {
                    $collectionItem->order = $collectionItem->asc === true ? 'asc' : 'desc';
                }
            }

            if (!isset($entityDefsItem->fields)) {
                continue;
            }

            foreach (get_object_vars($entityDefsItem->fields) as $field => $fieldDefsItem) {
                $additionalFields = $this->builderHelper->getAdditionalFieldList(
                    $field,
                    Util::objectToArray($fieldDefsItem),
                    $fieldDefinitionList
                );

                if (!$additionalFields) {
                    continue;
                }

                foreach ($additionalFields as $subFieldName => $subFieldParams) {
                    $item = Util::arrayToObject($subFieldParams);

                    if (isset($entityDefsItem->fields->$subFieldName)) {
                        $data->entityDefs->$entityType->fields->$subFieldName =
                            DataUtil::merge(
                                $item,
                                $entityDefsItem->fields->$subFieldName
                            );

                        continue;
                    }

                    $data->entityDefs->$entityType->fields->$subFieldName = $item;
                }
            }
        }
    }

    
}
