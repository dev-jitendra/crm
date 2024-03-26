<?php


namespace Espo\Core\Controllers;

use Espo\Core\Acl;
use Espo\Core\AclManager;
use Espo\Core\Container;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\ServiceFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

use Espo\Entities\Preferences;
use Espo\Entities\User;


abstract class Base
{
    
    protected $name;

    
    public static $defaultAction = 'index';

    
    private $container;

    
    protected $user;

    
    protected $acl;

    
    protected $aclManager;

    
    protected $config;

    
    protected $preferences;

    
    protected $metadata;

    
    protected $serviceFactory;

    
    public function __construct(
        Container $container,
        User $user,
        Acl $acl,
        AclManager $aclManager,
        Config $config,
        Preferences $preferences,
        Metadata $metadata,
        ServiceFactory $serviceFactory
    ) {
        $this->container = $container;
        $this->user = $user;
        $this->acl = $acl;
        $this->aclManager = $aclManager;
        $this->config = $config;
        $this->preferences = $preferences;
        $this->metadata = $metadata;
        $this->serviceFactory = $serviceFactory;

        if (empty($this->name)) {
            $name = get_class($this);

            $matches = null;

            if (preg_match('@\\\\([\w]+)$@', $name, $matches)) {
                $name = $matches[1];
            }

            $this->name = $name;
        }

        $this->checkControllerAccess();

        if (!$this->checkAccess()) {
            throw new Forbidden("No access to '{$this->name}'.");
        }
    }

    
    protected function getName(): string
    {
        
        return $this->name;
    }

    
    protected function checkAccess(): bool
    {
        return true;
    }

    
    protected function checkControllerAccess()
    {
        return;
    }

    
    protected function getService($name): object
    {
        return $this->serviceFactory->create($name);
    }

    
    protected function getContainer()
    {
        return $this->container;
    }

    
    protected function getUser()
    {
        
        return $this->container->get('user');
    }

    
    protected function getAcl()
    {
        
        return $this->container->get('acl');
    }

    
    protected function getAclManager()
    {
        
        return $this->container->get('aclManager');
    }

    
    protected function getConfig()
    {
        
        return $this->container->get('config');
    }

    
    protected function getPreferences()
    {
        
        return $this->container->get('preferences');
    }

    
    protected function getMetadata()
    {
        
        return $this->container->get('metadata');
    }

    
    protected function getServiceFactory()
    {
        
        return $this->container->get('serviceFactory');
    }
}
