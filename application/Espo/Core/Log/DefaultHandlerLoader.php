<?php


namespace Espo\Core\Log;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

use ReflectionClass;
use RuntimeException;


class DefaultHandlerLoader
{
    
    public function load(array $data, ?string $defaultLevel = null): HandlerInterface
    {
        $params = $data['params'] ?? [];
        $level = $data['level'] ?? $defaultLevel;

        if ($level) {
            
            $params['level'] = Logger::toMonologLevel($level);
        }

        $className = $data['className'] ?? null;

        if (!$className) {
            throw new RuntimeException("Log handler does not have className specified.");
        }

        $handler = $this->createInstance($className, $params);

        $formatter = $this->loadFormatter($data);

        if ($formatter && $handler instanceof FormattableHandlerInterface) {
            $handler->setFormatter($formatter);
        }

        return $handler;
    }

    
    private function loadFormatter(array $data): ?FormatterInterface
    {
        $formatterData = $data['formatter'] ?? null;

        if (!$formatterData || !is_array($formatterData)) {
            return null;
        }

        $className = $formatterData['className'] ?? null;

        if (!$className) {
            return null;
        }

        $params = $formatterData['params'] ?? [];

        return $this->createInstance($className, $params);
    }

    
    private function createInstance(string $className, array $params): object
    {
        $class = new ReflectionClass($className);

        $constructor = $class->getConstructor();

        if (!$constructor) {
            return $class->newInstanceArgs([]);
        }

        $argumentList = [];

        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $params)) {
                $value = $params[$name];
            }
            else if ($parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            } else {
                continue;
            }

            $argumentList[] = $value;
        }

        return $class->newInstanceArgs($argumentList);
    }
}
