<?php


namespace Espo\Core\Record\Hook;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use ReflectionClass;
use RuntimeException;

class Provider
{
    
    private $map = [];

    
    private $typeInterfaceListMap = [
        Type::BEFORE_CREATE => [CreateHook::class, SaveHook::class],
        Type::BEFORE_READ => [ReadHook::class],
        Type::BEFORE_UPDATE => [UpdateHook::class, SaveHook::class],
        Type::BEFORE_DELETE => [DeleteHook::class],
        Type::BEFORE_LINK => [LinkHook::class],
        Type::BEFORE_UNLINK => [UnlinkHook::class],
    ];

    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function getList(string $entityType, string $type): array
    {
        $key = $entityType . '_' . $type;

        if (!array_key_exists($key, $this->map)) {
            $this->map[$key] = $this->loadList($entityType, $type);
        }

        return $this->map[$key];
    }

    
    private function loadList(string $entityType, string $type): array
    {
        $key = $type . 'HookClassNameList';

        
        $classNameList = $this->metadata->get(['recordDefs', $entityType, $key]) ?? [];

        $interfaces = $this->typeInterfaceListMap[$type] ?? null;

        if (!$interfaces) {
            throw new RuntimeException("Unsupported record hook type '$type'.");
        }

        $list = [];

        foreach ($classNameList as $className) {
            $class = new ReflectionClass($className);

            $found = false;

            foreach ($interfaces as $interface) {
                if ($class->implementsInterface($interface)) {
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                throw new RuntimeException("Hook '$className' does not implement any required interface.");
            }

            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }
}
