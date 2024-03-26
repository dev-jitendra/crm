<?php



namespace Symfony\Component\Routing\Generator;


interface ConfigurableRequirementsInterface
{
    
    public function setStrictRequirements(?bool $enabled);

    
    public function isStrictRequirements(): ?bool;
}
