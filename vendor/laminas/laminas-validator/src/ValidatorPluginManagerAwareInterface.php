<?php

namespace Laminas\Validator;

interface ValidatorPluginManagerAwareInterface
{
    
    public function setValidatorPluginManager(ValidatorPluginManager $pluginManager);

    
    public function getValidatorPluginManager();
}
