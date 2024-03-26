<?php


namespace Espo\Tools\LinkManager\Hook\Hooks;

use Espo\Tools\LinkManager\Hook\DeleteHook;
use Espo\Tools\LinkManager\Params;
use Espo\Core\Utils\Metadata;

use Espo\ORM\Defs;

class ForeignFieldDelete implements DeleteHook
{
    public function __construct(
        private Metadata $metadata,
        private Defs $defs
    ) {}

    public function process(Params $params): void
    {
        $this->processInternal($params->getEntityType(), $params->getLink());

        if ($params->getForeignEntityType()) {
            $this->processInternal($params->getForeignEntityType(), $params->getForeignLink());
        }
    }

    private function processInternal(string $entityType, string $link): void
    {
        if (!$this->defs->hasEntity($entityType)) {
            return;
        }

        foreach ($this->defs->getEntity($entityType)->getFieldList() as $fieldDefs) {
            if ($fieldDefs->getType() !== 'foreign') {
                continue;
            }

            if ($fieldDefs->getParam('link') === $link) {
                $this->deleteForeignField($entityType, $fieldDefs->getName());
            }
        }
    }

    private function deleteForeignField(string $entityType, string $field): void
    {
        $this->metadata->delete('entityDefs', $entityType, ['fields.' . $field]);

        $this->metadata->save();
    }
}
