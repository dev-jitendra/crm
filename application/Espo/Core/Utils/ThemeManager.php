<?php


namespace Espo\Core\Utils;

class ThemeManager
{
    private string $defaultName = 'Espo';
    private string $defaultStylesheet = 'client/css/espo/espo.css';
    private string $defaultLogoSrc = 'client/img/logo.svg';

    public function __construct(
        private Config $config,
        private Metadata $metadata
    ) {}

    public function getName(): string
    {
        return $this->config->get('theme') ?? $this->defaultName;
    }

    public function getStylesheet(): string
    {
        return $this->metadata->get(['themes', $this->getName(), 'stylesheet']) ?? $this->defaultStylesheet;
    }

    public function getLogoSrc(): string
    {
        return $this->metadata->get(['themes', $this->getName(), 'logo']) ?? $this->defaultLogoSrc;
    }
}
