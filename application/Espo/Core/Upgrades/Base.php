<?php


namespace Espo\Core\Upgrades;

use Espo\Core\Exceptions\Error;

abstract class Base
{
    
    private $container;

    
    protected $actionManager;

    
    protected $name = null;

    
    protected $params = [];

    const UPLOAD = 'upload';

    const INSTALL = 'install';

    const UNINSTALL = 'uninstall';

    const DELETE = 'delete';

    
    public function __construct($container)
    {
        $this->container = $container;

        $this->actionManager = new ActionManager($this->name ?? '', $container, $this->params);
    }

    
    protected function getContainer()
    {
        return $this->container;
    }

    
    protected function getActionManager()
    {
        return $this->actionManager;
    }

    
    public function getManifest()
    {
        return $this->getActionManager()->getManifest();
    }

    
    public function getManifestById($processId)
    {
        $actionClass = $this->getActionManager()->getActionClass(self::INSTALL);
        $actionClass->setProcessId($processId);

        return $actionClass->getManifest();
    }

    
    public function upload($data)
    {
        $this->getActionManager()->setAction(self::UPLOAD);

        return $this->getActionManager()->run($data);
    }

    
    public function install($data)
    {
        $this->getActionManager()->setAction(self::INSTALL);

        return $this->getActionManager()->run($data);
    }

    
    public function uninstall($data)
    {
        $this->getActionManager()->setAction(self::UNINSTALL);

        return $this->getActionManager()->run($data);
    }

    
    public function delete($data)
    {
        $this->getActionManager()->setAction(self::DELETE);

        return $this->getActionManager()->run($data);
    }

    
    public function runInstallStep($stepName, array $params = [])
    {
        return $this->runActionStep(self::INSTALL, $stepName, $params);
    }

    
    protected function runActionStep($actionName, $stepName, array $params = [])
    {
        $actionClass = $this->getActionManager()->getActionClass($actionName);
        $methodName = 'step' . ucfirst($stepName);

        if (!method_exists($actionClass, $methodName)) {
            if (!empty($params['id'])) {
                $actionClass->setProcessId($params['id']);
                $actionClass->throwErrorAndRemovePackage('Step "'. $stepName .'" is not found.');
            }

            throw new Error('Step "'. $stepName .'" is not found.');
        }

        $actionClass->$methodName($params); 

        return true;
    }
}
