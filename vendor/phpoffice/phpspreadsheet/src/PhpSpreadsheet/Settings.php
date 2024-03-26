<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Chart\Renderer\IRenderer;
use PhpOffice\PhpSpreadsheet\Collection\Memory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\SimpleCache\CacheInterface;

class Settings
{
    
    private static $chartRenderer;

    
    private static $libXmlLoaderOptions = null;

    
    private static $libXmlDisableEntityLoader = true;

    
    private static $cache;

    
    private static $httpClient;

    
    private static $requestFactory;

    
    public static function setLocale($locale)
    {
        return Calculation::getInstance()->setLocale($locale);
    }

    
    public static function setChartRenderer($rendererClass): void
    {
        if (!is_a($rendererClass, IRenderer::class, true)) {
            throw new Exception('Chart renderer must implement ' . IRenderer::class);
        }

        self::$chartRenderer = $rendererClass;
    }

    
    public static function getChartRenderer()
    {
        return self::$chartRenderer;
    }

    
    public static function setLibXmlLoaderOptions($options): void
    {
        if ($options === null && defined('LIBXML_DTDLOAD')) {
            $options = LIBXML_DTDLOAD | LIBXML_DTDATTR;
        }
        self::$libXmlLoaderOptions = $options;
    }

    
    public static function getLibXmlLoaderOptions()
    {
        if (self::$libXmlLoaderOptions === null && defined('LIBXML_DTDLOAD')) {
            self::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR);
        } elseif (self::$libXmlLoaderOptions === null) {
            self::$libXmlLoaderOptions = true;
        }

        return self::$libXmlLoaderOptions;
    }

    
    public static function setLibXmlDisableEntityLoader($state): void
    {
        self::$libXmlDisableEntityLoader = (bool) $state;
    }

    
    public static function getLibXmlDisableEntityLoader()
    {
        return self::$libXmlDisableEntityLoader;
    }

    
    public static function setCache(CacheInterface $cache): void
    {
        self::$cache = $cache;
    }

    
    public static function getCache()
    {
        if (!self::$cache) {
            self::$cache = new Memory();
        }

        return self::$cache;
    }

    
    public static function setHttpClient(ClientInterface $httpClient, RequestFactoryInterface $requestFactory): void
    {
        self::$httpClient = $httpClient;
        self::$requestFactory = $requestFactory;
    }

    
    public static function unsetHttpClient(): void
    {
        self::$httpClient = null;
        self::$requestFactory = null;
    }

    
    public static function getHttpClient(): ClientInterface
    {
        self::assertHttpClient();

        return self::$httpClient;
    }

    
    public static function getRequestFactory(): RequestFactoryInterface
    {
        self::assertHttpClient();

        return self::$requestFactory;
    }

    private static function assertHttpClient(): void
    {
        if (!self::$httpClient || !self::$requestFactory) {
            throw new Exception('HTTP client must be configured via Settings::setHttpClient() to be able to use WEBSERVICE function.');
        }
    }
}
