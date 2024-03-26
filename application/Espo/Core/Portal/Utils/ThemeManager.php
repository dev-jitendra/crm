<?php


namespace Espo\Core\Portal\Utils;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Portal;
use Espo\Core\Utils\ThemeManager as BaseThemeManager;

class ThemeManager extends BaseThemeManager
{
    private Portal $portal;

    public function __construct(Config $config, Metadata $metadata, Portal $portal)
    {
        parent::__construct($config, $metadata);

        $this->portal = $portal;
    }

    public function getName(): string
    {
        $theme = $this->portal->get('theme');

        if ($theme) {
            return $theme;
        }

        return parent::getName();
    }
}
