<?php


namespace Espo\Core\Upgrades\Actions;

use Espo\Core\Exceptions\Error;

class Helper
{
    
    private $actionObject;

    
    public function __construct($actionObject = null)
    {
        if (isset($actionObject)) {
            $this->setActionObject($actionObject);
        }
    }

    
    public function setActionObject(\Espo\Core\Upgrades\Actions\Base $actionObject)
    {
        $this->actionObject = $actionObject;
    }

    
    protected function getActionObject()
    {
        return $this->actionObject;
    }

    
    public function checkDependencies($dependencyList)
    {
        if (!is_array($dependencyList)) { 
            $dependencyList = (array) $dependencyList;
        }

        

        $actionObject = $this->getActionObject();

        assert($actionObject !== null);

        foreach ($dependencyList as $extensionName => $extensionVersion) {
            $dependencyExtensionEntity = $actionObject
                ->getEntityManager()
                ->getRDBRepository('Extension')
                ->where([
                    'name' => trim($extensionName),
                    'isInstalled' => true,
                ])
                ->findOne();

            $versionString = is_array($extensionVersion) ?
                implode(', ', $extensionVersion) :
                $extensionVersion;

            $errorMessage = 'Dependency Error: The extension "' . $extensionName .'" with version "'.
                $versionString . '" is missing.';

            if (
                !isset($dependencyExtensionEntity) ||
                !$actionObject->checkVersions(
                    $extensionVersion,
                    $dependencyExtensionEntity->get('version'),
                    $errorMessage
                )
            ) {
                throw new Error($errorMessage);
            }
        }

        return true;
    }
}
