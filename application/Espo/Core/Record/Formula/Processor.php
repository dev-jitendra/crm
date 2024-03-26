<?php


namespace Espo\Core\Record\Formula;

use Espo\Core\Formula\Exceptions\Error as FormulaError;
use Espo\Core\Formula\Manager as FormulaManager;
use Espo\Core\Record\CreateParams;
use Espo\Core\Record\UpdateParams;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use RuntimeException;
use stdClass;


class Processor
{
    public function __construct(
        private FormulaManager $formulaManager,
        private Metadata $metadata
    ) {}

    
    public function processBeforeCreate(Entity $entity, CreateParams $params): void
    {
        $script = $this->getScript($entity->getEntityType());

        if (!$script) {
            return;
        }

        $variables = (object) [
            '__skipDuplicateCheck' => $params->skipDuplicateCheck(),
            '__isRecordService' => true,
        ];

        $this->run($script, $entity, $variables);
    }

    
    public function processBeforeUpdate(Entity $entity, UpdateParams $params): void
    {
        $script = $this->getScript($entity->getEntityType());

        if (!$script) {
            return;
        }

        $variables = (object) [
            '__skipDuplicateCheck' => $params->skipDuplicateCheck(),
            '__isRecordService' => true,
        ];

        $this->run($script, $entity, $variables);
    }

    private function run(string $script, Entity $entity, stdClass $variables): void
    {
        try {
            $this->formulaManager->run($script, $entity, $variables);
        }
        catch (FormulaError $e) {
            throw new RuntimeException('Formula script error: ' . $e->getMessage(), 500, $e);
        }
    }

    private function getScript(string $entityType): ?string
    {
        
        return $this->metadata->get(['formula', $entityType, 'beforeSaveApiScript']);
    }
}
