<?php


namespace Espo\Core\Acl\LinkChecker;

use Espo\Core\Acl\LinkChecker;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use RuntimeException;

class LinkCheckerFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(string $scope, string $link): LinkChecker
    {
        $className = $this->getClassName($scope, $link);

        if (!$className) {
            throw new RuntimeException("Link checker is not implemented for {$scope}.{$link}.");
        }

        return $this->injectableFactory->create($className);
    }

    public function isCreatable(string $scope, string $link): bool
    {
        return (bool) $this->getClassName($scope, $link);
    }

    
    private function getClassName(string $scope, string $link): ?string
    {
        
        return $this->metadata->get(['aclDefs', $scope, 'linkCheckerClassNameMap', $link]);
    }
}
