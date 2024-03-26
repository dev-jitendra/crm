<?php


namespace Espo\Core\Portal\Utils;

use Espo\Core\Utils\Config as BaseConfig;

use RuntimeException;
use stdClass;

class Config extends BaseConfig
{
    private bool $portalParamsSet = false;

    
    private $portalData = [];

    
    private $portalParamList = [
        'companyLogoId',
        'tabList',
        'quickCreateList',
        'dashboardLayout',
        'dashletsOptions',
        'theme',
        'themeParams',
        'language',
        'timeZone',
        'dateFormat',
        'timeFormat',
        'weekStart',
        'defaultCurrency',
    ];

    
    public function get(string $name, $default = null)
    {
        if (array_key_exists($name, $this->portalData)) {
            return $this->portalData[$name];
        }

        return parent::get($name, $default);
    }

    public function has(string $name): bool
    {
        if (array_key_exists($name, $this->portalData)) {
            return true;
        }

        return parent::has($name);
    }

    public function getAllNonInternalData(): stdClass
    {
        $data = parent::getAllNonInternalData();

        foreach ($this->portalData as $k => $v) {
            $data->$k = $v;
        }

        return $data;
    }

    
    public function setPortalParameters(array $data = []): void
    {
        if ($this->portalParamsSet) {
            throw new RuntimeException("Can't set portal params second time.");
        }

        $this->portalParamsSet = true;

        if (empty($data['language'])) {
            unset($data['language']);
        }

        if (empty($data['theme'])) {
            unset($data['theme']);
        }

        if (empty($data['timeZone'])) {
            unset($data['timeZone']);
        }

        if (empty($data['dateFormat'])) {
            unset($data['dateFormat']);
        }

        if (empty($data['timeFormat'])) {
            unset($data['timeFormat']);
        }

        if (empty($data['defaultCurrency'])) {
            unset($data['defaultCurrency']);
        }

        if (isset($data['weekStart']) && $data['weekStart'] === -1) {
            unset($data['weekStart']);
        }

        if (array_key_exists('weekStart', $data) && is_null($data['weekStart'])) {
            unset($data['weekStart']);
        }

        if ($this->get('webSocketInPortalDisabled')) {
            $this->portalData['useWebSocket'] = false;
        }

        foreach ($data as $attribute => $value) {
            if (!in_array($attribute, $this->portalParamList)) {
                continue;
            }

            $this->portalData[$attribute] = $value;
        }
    }
}
