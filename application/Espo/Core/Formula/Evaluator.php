<?php


namespace Espo\Core\Formula;

use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Exceptions\SyntaxError;
use Espo\Core\Formula\Functions\Base as DeprecatedBaseFunction;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\Parser\Ast\Attribute;
use Espo\Core\Formula\Parser\Ast\Node;
use Espo\Core\Formula\Parser\Ast\Value;
use Espo\Core\Formula\Parser\Ast\Variable;
use Espo\ORM\Entity;
use Espo\Core\InjectableFactory;

use LogicException;
use stdClass;


class Evaluator
{
    private Parser $parser;
    private AttributeFetcher $attributeFetcher;
    
    private $parsedHash;

    
    public function __construct(
        private InjectableFactory $injectableFactory,
        private array $functionClassNameMap = []
    ) {
        $this->attributeFetcher = $injectableFactory->create(AttributeFetcher::class);
        $this->parser = new Parser();
        $this->parsedHash = [];
    }

    
    public function process(string $expression, ?Entity $entity = null, ?stdClass $variables = null): mixed
    {
        $processor = new Processor(
            $this->injectableFactory,
            $this->attributeFetcher,
            $this->functionClassNameMap,
            $entity,
            $variables
        );

        $item = $this->getParsedExpression($expression);

        try {
            $result = $processor->process($item);
        }
        catch (ExecutionException $e) {
            throw new LogicException('Unexpected ExecutionException.', 0, $e);
        }

        $this->attributeFetcher->resetRuntimeCache();

        return $result;
    }

    
    private function getParsedExpression(string $expression): Argument
    {
        if (!array_key_exists($expression, $this->parsedHash)) {
            $this->parsedHash[$expression] = $this->parser->parse($expression);
        }

        return new Argument($this->parsedHash[$expression]);
    }
}
