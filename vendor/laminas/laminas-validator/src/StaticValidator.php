<?php

namespace Laminas\Validator;

use Laminas\ServiceManager\ServiceManager;

use function array_values;
use function method_exists;

class StaticValidator
{
    
    protected static $plugins;

    
    public static function setPluginManager(?ValidatorPluginManager $plugins = null)
    {
        
        if ($plugins instanceof ValidatorPluginManager) {
            
            if (method_exists($plugins, 'configure')) {
                $plugins->configure(['shared_by_default' => false]);
            } else {
                $plugins->setShareByDefault(false);
            }
        }
        static::$plugins = $plugins;
    }

    
    public static function getPluginManager()
    {
        if (! static::$plugins instanceof ValidatorPluginManager) {
            $plugins = new ValidatorPluginManager(new ServiceManager());
            static::setPluginManager($plugins);

            return $plugins;
        }
        return static::$plugins;
    }

    
    public static function execute(mixed $value, $classBaseName, array $options = [])
    {
        if ($options && array_values($options) === $options) {
            throw new Exception\InvalidArgumentException(
                'Invalid options provided via $options argument; must be an associative array'
            );
        }

        $plugins   = static::getPluginManager();
        $validator = $plugins->get($classBaseName, $options);

        return $validator->isValid($value);
    }
}
