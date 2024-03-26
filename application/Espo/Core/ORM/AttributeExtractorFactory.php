<?php


namespace Espo\Core\ORM;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use Espo\ORM\Metadata as OrmMetadata;
use Espo\ORM\Value\AttributeExtractor;
use Espo\ORM\Value\AttributeExtractorFactory as AttributeExtractorFactoryInterface;

use RuntimeException;


class AttributeExtractorFactory implements AttributeExtractorFactoryInterface
{
    public function __construct(
        private Metadata $metadata,
        private OrmMetadata $ormMetadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(string $entityType, string $field): AttributeExtractor
    {
        $className = $this->getClassName($entityType, $field);

        if (!$className) {
            throw new RuntimeException("Could not get AttributeExtractor for '{$entityType}.{$field}'.");
        }

        return $this->injectableFactory->createWith($className, ['entityType' => $entityType]);
    }

    
    private function getClassName(string $entityType, string $field): ?string
    {
        $fieldDefs = $this->ormMetadata
            ->getDefs()
            ->getEntity($entityType)
            ->getField($field);

        $className = $fieldDefs->getParam('attributeExtractorClassName');

        if ($className) {
            
            return $className;
        }

        $type = $fieldDefs->getType();

        
        return $this->metadata->get(['fields', $type, 'attributeExtractorClassName']);
    }
}
