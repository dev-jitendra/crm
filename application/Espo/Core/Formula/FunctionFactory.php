<?php


namespace Espo\Core\Formula;

use Espo\Core\Formula\Exceptions\UnknownFunction;

use Espo\Core\Formula\Functions\Base;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\ORM\Entity;
use Espo\Core\InjectableFactory;

use ReflectionClass;
use stdClass;

class FunctionFactory
{
    
    private $classNameMap;

    
    public function __construct(
        private Processor $processor,
        private InjectableFactory $injectableFactory,
        private AttributeFetcher $attributeFetcher,
        ?array $classNameMap = null
    ) {
        $this->classNameMap = $classNameMap ?? [];
    }

    
    public function create(string $name, ?Entity $entity = null, ?stdClass $variables = null): Func|BaseFunction|Base
    {
        if ($this->classNameMap && array_key_exists($name, $this->classNameMap)) {
            $className = $this->classNameMap[$name];
        }
        else {
            $arr = explode('\\', $name);

            foreach ($arr as $i => $part) {
                if ($i < count($arr) - 1) {
                    $part = $part . 'Group';
                }

                $arr[$i] = ucfirst($part);
            }

            $typeName = implode('\\', $arr);

            
            $className = 'Espo\\Core\\Formula\\Functions\\' . $typeName . 'Type';
        }

        if (!class_exists($className)) {
            throw new UnknownFunction("Unknown function: " . $name);
        }

        if ((new ReflectionClass($className))->implementsInterface(Func::class)) {
            return $this->injectableFactory->create($className);
        }

        $object = $this->injectableFactory->createWith($className, [
            'name' => $name,
            'processor' => $this->processor,
            'entity' => $entity,
            'variables' => $variables,
            'attributeFetcher' => $this->attributeFetcher,
        ]);

        if (method_exists($object, 'setAttributeFetcher')) {
            $object->setAttributeFetcher($this->attributeFetcher);
        }

        
        return $object;
    }
}
