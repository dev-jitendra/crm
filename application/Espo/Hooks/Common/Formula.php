<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;
use Espo\Core\Hook\Hook\BeforeSave;
use Espo\Core\Formula\Manager as FormulaManager;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;

use Exception;
use stdClass;


class Formula implements BeforeSave
{
    public static int $order = 11;

    public function __construct(
        private Metadata $metadata,
        private FormulaManager $formulaManager,
        private Log $log
    ) {}

    public function beforeSave(Entity $entity, SaveOptions $options): void
    {
        if ($options->get('skipFormula')) {
            return;
        }

        $scriptList = $this->metadata->get(['formula', $entity->getEntityType(), 'beforeSaveScriptList'], []);

        $variables = (object) [];

        foreach ($scriptList as $script) {
            $this->runScript($script, $entity, $variables);;
        }

        $customScript = $this->metadata->get(['formula', $entity->getEntityType(), 'beforeSaveCustomScript']);

        if (!$customScript) {
            return;
        }

        $this->runScript($customScript, $entity, $variables);;
    }

    private function runScript(string $script, Entity $entity, stdClass $variables): void
    {
        try {
            $this->formulaManager->run($script, $entity, $variables);
        }
        catch (Exception $e) {
            $this->log->error('Before-save formula script failed: ' . $e->getMessage());
        }
    }
}
