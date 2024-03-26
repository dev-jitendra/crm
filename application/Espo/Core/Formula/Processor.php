<?php


namespace Espo\Core\Formula;

use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\BadArgumentValue;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Functions\Base as DeprecatedBaseFunction;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Formula\Parser\Ast\Attribute;
use Espo\Core\Formula\Parser\Ast\Node;
use Espo\Core\Formula\Parser\Ast\Value;
use Espo\Core\Formula\Parser\Ast\Variable;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\InjectableFactory;

use Espo\ORM\Entity;

use InvalidArgumentException;
use stdClass;


class Processor
{
    private FunctionFactory $functionFactory;
    private stdClass $variables;

    
    public function __construct(
        InjectableFactory $injectableFactory,
        AttributeFetcher $attributeFetcher,
        ?array $functionClassNameMap = null,
        private ?Entity $entity = null,
        ?stdClass $variables = null
    ) {
        $this->functionFactory = new FunctionFactory(
            $this,
            $injectableFactory,
            $attributeFetcher,
            $functionClassNameMap
        );

        $this->variables = $variables ?? (object) [];
    }

    
    public function process(Evaluatable $item): mixed
    {
        if ($item instanceof ArgumentList) {
            return $this->processList($item);
        }

        if (!$item instanceof Argument) {
            throw new InvalidArgumentException();
        }

        $function = $this->functionFactory->create($item->getType(), $this->entity, $this->variables);

        if ($function instanceof Func) {
            $evaluatedArguments = array_map(
                fn($item) => $this->process($item),
                iterator_to_array($item->getArgumentList())
            );

            try {
                return $function->process(new EvaluatedArgumentList($evaluatedArguments));
            }
            catch (TooFewArguments|BadArgumentType|BadArgumentValue $e) {
                $message = sprintf('Function %s; %s', $item->getType(), $e->getLogMessage());

                throw new Error($message);
            }
        }

        if ($function instanceof DeprecatedBaseFunction) {
            return $function->process(self::dataToStdClass($item->getData()));
        }

        return $function->process($item->getArgumentList());
    }

    
    private function dataToStdClass(Node|Value|Attribute|Variable|string|float|int|bool|null $data): stdClass
    {
        if ($data instanceof Node) {
            return (object) [
                'type' => $data->getType(),
                'value' => $data->getChildNodes(),
            ];
        }

        if ($data instanceof Value) {
            return (object) [
                'type' => 'value',
                'value' => $data->getValue(),
            ];
        }

        if ($data instanceof Attribute) {
            return (object) [
                'type' => 'attribute',
                'value' => $data->getName(),
            ];
        }

        if ($data instanceof Variable) {
            return (object) [
                'type' => 'variable',
                'value' => $data->getName(),
            ];
        }

        throw new Error("Can't convert argument to a raw object.");
    }

    
    private function processList(ArgumentList $args): array
    {
        $list = [];

        foreach ($args as $item) {
            $list[] = $this->process($item);
        }

        return $list;
    }
}
