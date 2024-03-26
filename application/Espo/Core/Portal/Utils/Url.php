<?php


namespace Espo\Core\Portal\Utils;

class Url
{
    public static function detectPortalIdForApi(): ?string
    {
        $portalId = filter_input(INPUT_GET, 'portalId');

        if ($portalId)  {
            return $portalId;
       }

        $url = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        $scriptNameModified = str_replace('public/api/', 'api/', $scriptName);

        return explode('/', $url)[count(explode('/', $scriptNameModified)) - 1] ?? null;
    }

    public static function getPortalIdFromEnv(): ?string
    {
        return $_SERVER['ESPO_PORTAL_ID'] ?? null;
    }

    public static function detectPortalId(): ?string
    {
        $portalId = self::getPortalIdFromEnv();

        if ($portalId) {
            return $portalId;
        }

        $url = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        $scriptNameModified = str_replace('public/api/', 'api/', $scriptName);

        $portalId = explode('/', $url)[count(explode('/', $scriptNameModified)) - 1] ?? null;

        if (strpos($url, '=') !== false) {
            $portalId = null;
        }

        if ($portalId) {
            return $portalId;
        }

        $url = $_SERVER['REDIRECT_URL'] ?? null;

        if (!$url) {
            return null;
        }

        $portalId = explode('/', $url)[count(explode('/', $scriptNameModified)) - 1] ?? null;

        if ($portalId === '') {
            $portalId = null;
        }

        return $portalId;
    }

    protected static function detectIsCustomUrl(): bool
    {
        return (bool) ($_SERVER['ESPO_PORTAL_IS_CUSTOM_URL'] ?? false);
    }

    public static function detectIsInPortalDir(): bool
    {
        $isCustomUrl = self::detectIsCustomUrl();

        if ($isCustomUrl) {
            return false;
        }

        $a = explode('?', $_SERVER['REQUEST_URI']);

        $url = rtrim($a[0], '/');

        return strpos($url, '/portal') !== false;
    }

    public static function detectIsInPortalWithId(): bool
    {
        if (!self::detectIsInPortalDir()) {
            return false;
        }

        $url = $_SERVER['REQUEST_URI'];

        $a = explode('?', $url);

        $url = rtrim($a[0], '/');

        $folders = explode('/', $url);

        if (count($folders) > 1 && $folders[count($folders) - 2] === 'portal') {
            return true;
        }

        return false;
    }
}
