<?php



namespace Symfony\Contracts\Service;

use Psr\Container\ContainerInterface;


trait ServiceSubscriberTrait
{
    
    protected $container;

    public static function getSubscribedServices(): array
    {
        static $services;

        if (null !== $services) {
            return $services;
        }

        $services = \is_callable(['parent', __FUNCTION__]) ? parent::getSubscribedServices() : [];

        foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
            if ($method->isStatic() || $method->isAbstract() || $method->isGenerator() || $method->isInternal() || $method->getNumberOfRequiredParameters()) {
                continue;
            }

            if (self::class === $method->getDeclaringClass()->name && ($returnType = $method->getReturnType()) && !$returnType->isBuiltin()) {
                $services[self::class.'::'.$method->name] = '?'.($returnType instanceof \ReflectionNamedType ? $returnType->getName() : $type);
            }
        }

        return $services;
    }

    
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        if (\is_callable(['parent', __FUNCTION__])) {
            return parent::setContainer($container);
        }

        return null;
    }
}
