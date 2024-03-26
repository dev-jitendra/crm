<?php


namespace Espo\Core\Formula;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;

use stdClass;


class Manager
{
    private Evaluator $evaluator;

    public function __construct(InjectableFactory $injectableFactory, Metadata $metadata)
    {
        $functionClassNameMap = $metadata->get(['app', 'formula', 'functionClassNameMap'], []);

        $this->evaluator = new Evaluator($injectableFactory, $functionClassNameMap);
    }

    
    public function run(string $script, ?Entity $entity = null, ?stdClass $variables = null)
    {
        return $this->evaluator->process($script, $entity, $variables);
    }
}
