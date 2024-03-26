<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Test;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

use function method_exists;


trait CommonPluginManagerTrait
{
    public function testInstanceOfMatches()
    {
        $manager    = $this->getPluginManager();
        $reflection = new ReflectionProperty($manager, 'instanceOf');
        $this->assertEquals($this->getInstanceOf(), $reflection->getValue($manager), 'instanceOf does not match');
    }

    public function testShareByDefaultAndSharedByDefault()
    {
        $manager        = $this->getPluginManager();
        $reflection     = new ReflectionClass($manager);
        $shareByDefault = $sharedByDefault = true;

        foreach ($reflection->getProperties() as $prop) {
            if ($prop->getName() === 'shareByDefault') {
                $shareByDefault = $prop->getValue($manager);
            }
            if ($prop->getName() === 'sharedByDefault') {
                $sharedByDefault = $prop->getValue($manager);
            }
        }

        $this->assertSame(
            $shareByDefault,
            $sharedByDefault,
            'Values of shareByDefault and sharedByDefault do not match'
        );
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->expectException($this->getServiceNotFoundException());
        $this->getPluginManager()->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $manager = $this->getPluginManager();
        $manager->setInvokableClass('test', stdClass::class);
        $this->expectException($this->getServiceNotFoundException());
        $manager->get('test');
    }

    
    public function testPluginAliasesResolve($alias, $expected)
    {
        $this->assertInstanceOf($expected, $this->getPluginManager()->get($alias), "Alias '$alias' does not resolve'");
    }

    
    public static function aliasProvider(): array
    {
        $manager    = self::getPluginManager();
        $reflection = new ReflectionProperty($manager, 'aliases');
        $data       = [];
        foreach ($reflection->getValue($manager) as $alias => $expected) {
            $data[] = [$alias, $expected];
        }
        return $data;
    }

    protected function getServiceNotFoundException(): string
    {
        $manager = $this->getPluginManager();
        if (method_exists($manager, 'configure')) {
            return InvalidServiceException::class;
        }
        return $this->getV2InvalidPluginException();
    }

    
    abstract protected static function getPluginManager();

    
    abstract protected function getV2InvalidPluginException();

    
    abstract protected function getInstanceOf();
}
