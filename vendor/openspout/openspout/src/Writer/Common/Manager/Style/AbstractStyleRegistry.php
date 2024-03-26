<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;


abstract class AbstractStyleRegistry
{
    
    private array $serializedStyleToStyleIdMappingTable = [];

    
    private array $styleIdToStyleMappingTable = [];

    public function __construct(Style $defaultStyle)
    {
        
        $this->registerStyle($defaultStyle);
    }

    
    public function registerStyle(Style $style): Style
    {
        $serializedStyle = $this->serialize($style);

        if (!$this->hasSerializedStyleAlreadyBeenRegistered($serializedStyle)) {
            $nextStyleId = \count($this->serializedStyleToStyleIdMappingTable);
            $style->markAsRegistered($nextStyleId);

            $this->serializedStyleToStyleIdMappingTable[$serializedStyle] = $nextStyleId;
            $this->styleIdToStyleMappingTable[$nextStyleId] = $style;
        }

        return $this->getStyleFromSerializedStyle($serializedStyle);
    }

    
    final public function getRegisteredStyles(): array
    {
        return array_values($this->styleIdToStyleMappingTable);
    }

    final public function getStyleFromStyleId(int $styleId): Style
    {
        return $this->styleIdToStyleMappingTable[$styleId];
    }

    
    final public function serialize(Style $style): string
    {
        return serialize($style);
    }

    
    private function hasSerializedStyleAlreadyBeenRegistered(string $serializedStyle): bool
    {
        
        return isset($this->serializedStyleToStyleIdMappingTable[$serializedStyle]);
    }

    
    private function getStyleFromSerializedStyle(string $serializedStyle): Style
    {
        $styleId = $this->serializedStyleToStyleIdMappingTable[$serializedStyle];

        return $this->styleIdToStyleMappingTable[$styleId];
    }
}
