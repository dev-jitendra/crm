<?php


namespace Espo\ORM\Value;

use Espo\ORM\Entity;
use Espo\ORM\EventDispatcher;

class ValueAccessorFactory
{
    private ?GeneralValueFactory $generalValueFactory = null;
    private ?GeneralAttributeExtractor $generalAttributeExtractor = null;

    
    public function __construct(
        private ValueFactoryFactory $valueFactoryFactory,
        private AttributeExtractorFactory $attributeExtractorFactory,
        private EventDispatcher $eventDispatcher
    ) {

        $this->subscribeToMetadataUpdate();
    }

    public function create(Entity $entity): ValueAccessor
    {
        return new ValueAccessor(
            $entity,
            $this->getGeneralValueFactory(),
            $this->getGeneralAttributeExtractor()
        );
    }

    private function getGeneralValueFactory(): GeneralValueFactory
    {
        if (!$this->generalValueFactory) {
            $this->generalValueFactory = new GeneralValueFactory($this->valueFactoryFactory);
        }

        return $this->generalValueFactory;
    }

    private function getGeneralAttributeExtractor(): GeneralAttributeExtractor
    {
        if (!$this->generalAttributeExtractor) {
            $this->generalAttributeExtractor = new GeneralAttributeExtractor($this->attributeExtractorFactory);
        }

        return $this->generalAttributeExtractor;
    }

    private function subscribeToMetadataUpdate(): void
    {
        $this->eventDispatcher->subscribeToMetadataUpdate(
            function () {
                $this->generalValueFactory = null;
                $this->generalAttributeExtractor = null;
            }
        );
    }
}
