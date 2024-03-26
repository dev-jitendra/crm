<?php


namespace Espo\Core\Utils;

use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseWrapper;
use Espo\Core\Utils\Client\DevModeJsFileListProvider;
use Espo\Core\Utils\Client\LoaderParamsProvider;
use Espo\Core\Utils\File\Manager as FileManager;

use Slim\Psr7\Response as Psr7Response;
use Slim\ResponseEmitter;


class ClientManager
{
    private string $mainHtmlFilePath = 'html/main.html';
    private string $runScript = 'app.start();';
    private string $favicon = 'client/img/favicon.ico';
    private string $favicon196 = 'client/img/favicon196x196.png';
    private string $basePath = '';
    private string $apiUrl = 'api/v1';

    private string $nonce;

    private const APP_DESCRIPTION = "EspoCRM – Open Source CRM application.";

    public function __construct(
        private Config $config,
        private ThemeManager $themeManager,
        private Metadata $metadata,
        private FileManager $fileManager,
        private DevModeJsFileListProvider $devModeJsFileListProvider,
        private Module $module,
        private LoaderParamsProvider $loaderParamsProvider
    ) {
        $this->nonce = Util::generateKey();
    }

    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    
    public function writeHeaders(Response $response): void
    {
        if ($this->config->get('clientSecurityHeadersDisabled')) {
            return;
        }

        $response->setHeader('X-Content-Type-Options', 'nosniff');

        $this->writeXFrameOptionsHeader($response);
        $this->writeContentSecurityPolicyHeader($response);
        $this->writeStrictTransportSecurityHeader($response);
    }

    private function writeXFrameOptionsHeader(Response $response): void
    {
        if ($this->config->get('clientXFrameOptionsHeaderDisabled')) {
            return;
        }

        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    private function writeContentSecurityPolicyHeader(Response $response): void
    {
        if ($this->config->get('clientCspDisabled')) {
            return;
        }

        $scriptSrc = "script-src 'self' 'nonce-$this->nonce' 'unsafe-eval'";

        $scriptSourceList = $this->config->get('clientCspScriptSourceList') ?? [];

        foreach ($scriptSourceList as $src) {
            $scriptSrc .= ' ' . $src;
        }

        $response->setHeader('Content-Security-Policy', $scriptSrc);
    }

    private function writeStrictTransportSecurityHeader(Response $response): void
    {
        if ($this->config->get('clientStrictTransportSecurityHeaderDisabled')) {
            return;
        }

        $siteUrl = $this->config->get('siteUrl') ?? '';

        if (str_starts_with($siteUrl, 'https:
            $response->setHeader('Strict-Transport-Security', 'max-age=10368000');
        }
    }

    
    public function display(?string $runScript = null, ?string $htmlFilePath = null, array $vars = []): void
    {
        $body = $this->render($runScript, $htmlFilePath, $vars);

        $response = new ResponseWrapper(new Psr7Response());

        $this->writeHeaders($response);
        $response->writeBody($body);

        (new ResponseEmitter())->emit($response->toPsr7());
    }

    
    public function render(?string $runScript = null, ?string $htmlFilePath = null, array $vars = []): string
    {
        $runScript ??= $this->runScript;
        $htmlFilePath ??= $this->mainHtmlFilePath;

        $cacheTimestamp = $this->getCacheTimestamp();
        $jsFileList = $this->getJsFileList();
        $appTimestamp = $this->getAppTimestamp();

        if ($this->isDeveloperMode()) {
            $useCache = $this->useCacheInDeveloperMode();
            $loaderCacheTimestamp = null;
        }
        else {
            $useCache = $this->useCache();
            $loaderCacheTimestamp = $appTimestamp;
        }

        $cssFileList = $this->metadata->get(['app', 'client', 'cssList'], []);
        $linkList = $this->metadata->get(['app', 'client', 'linkList'], []);
        $favicon196Path = $this->metadata->get(['app', 'client', 'favicon196']) ?? $this->favicon196;
        $faviconPath = $this->metadata->get(['app', 'client', 'favicon']) ?? $this->favicon;

        $scriptsHtml = implode('',
            array_map(fn ($file) => $this->getScriptItemHtml($file, $appTimestamp), $jsFileList)
        );

        $additionalStyleSheetsHtml = implode('',
            array_map(fn ($file) => $this->getCssItemHtml($file, $appTimestamp), $cssFileList)
        );

        $linksHtml = implode('',
            array_map(fn ($item) => $this->getLinkItemHtml($item, $appTimestamp), $linkList)
        );

        $internalModuleList = array_map(
            fn ($moduleName) => Util::fromCamelCase($moduleName, '-'),
            $this->module->getInternalList()
        );

        $data = [
            'applicationId' => 'espocrm-application-id',
            'apiUrl' => $this->apiUrl,
            'applicationName' => $this->config->get('applicationName', 'EspoCRM'),
            'cacheTimestamp' => $cacheTimestamp,
            'appTimestamp' => $appTimestamp,
            'loaderCacheTimestamp' => Json::encode($loaderCacheTimestamp),
            'stylesheet' => $this->themeManager->getStylesheet(),
            'runScript' => $runScript,
            'basePath' => $this->basePath,
            'useCache' => $useCache ? 'true' : 'false',
            'appClientClassName' => 'app',
            'scriptsHtml' => $scriptsHtml,
            'additionalStyleSheetsHtml' => $additionalStyleSheetsHtml,
            'linksHtml' => $linksHtml,
            'favicon196Path' => $favicon196Path,
            'faviconPath' => $faviconPath,
            'ajaxTimeout' => $this->config->get('ajaxTimeout') ?? 60000,
            'internalModuleList' => Json::encode($internalModuleList),
            'bundledModuleList' => Json::encode($this->getBundledModuleList()),
            'applicationDescription' => $this->config->get('applicationDescription') ?? self::APP_DESCRIPTION,
            'nonce' => $this->nonce,
            'loaderParams' => Json::encode([
                'basePath' => $this->basePath,
                'cacheTimestamp' => $loaderCacheTimestamp,
                'internalModuleList' => $internalModuleList,
                'transpiledModuleList' => $this->getTranspiledModuleList(),
                'libsConfig' => $this->loaderParamsProvider->getLibsConfig(),
                'aliasMap' => $this->loaderParamsProvider->getAliasMap(),
            ]),
        ];

        $html = $this->fileManager->getContents($htmlFilePath);

        foreach ($vars as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $vars)) {
                continue;
            }

            $html = str_replace('{{' . $key . '}}', $value, $html);
        }

        return $html;
    }

    
    private function getJsFileList(): array
    {
        if ($this->isDeveloperMode()) {
            return array_merge(
                $this->metadata->get(['app', 'client', 'developerModeScriptList']) ?? [],
                $this->getDeveloperModeBundleLibFileList(),
            );
        }

        return $this->metadata->get(['app', 'client', 'scriptList']) ?? [];
    }

    
    private function getDeveloperModeBundleLibFileList(): array
    {
        return $this->devModeJsFileListProvider->get();
    }

    private function isDeveloperMode(): bool
    {
        return (bool) $this->config->get('isDeveloperMode');
    }

    private function useCache(): bool
    {
        return (bool) $this->config->get('useCache');
    }

    private function useCacheInDeveloperMode(): bool
    {
        return (bool) $this->config->get('useCacheInDeveloperMode');
    }

    private function getCacheTimestamp(): int
    {
        if (!$this->useCache()) {
            return time();
        }

        return $this->config->get('cacheTimestamp', 0);
    }

    private function getAppTimestamp(): int
    {
        if (!$this->useCache()) {
            return time();
        }

        return $this->config->get('appTimestamp', 0);
    }

    private function getScriptItemHtml(string $file, int $appTimestamp): string
    {
        $src = $this->basePath . $file . '?r=' . $appTimestamp;

        return $this->getTabHtml() .
            "<script type=\"text/javascript\" src=\"$src\" data-base-path=\"$this->basePath\"></script>";
    }

    private function getCssItemHtml(string $file, int $appTimestamp): string
    {
        $src = $this->basePath . $file . '?r=' . $appTimestamp;

        return $this->getTabHtml() . "<link rel=\"stylesheet\" href=\"$src\">";
    }

    
    private function getLinkItemHtml(array $item, int $appTimestamp): string
    {
        $href = $this->basePath . $item['href'];

        if (empty($item['noTimestamp'])) {
            $href .= '?r=' . $appTimestamp;
        }

        $as = $item['as'] ?? '';
        $rel = $item['rel'] ?? '';
        $type = $item['type'] ?? '';
        $part = '';

        if ($item['crossorigin'] ?? false) {
            $part .= ' crossorigin';
        }

        return $this->getTabHtml() .
            "<link rel=\"$rel\" href=\"$href\" as=\"$as\" as=\"$type\"$part>";
    }

    private function getTabHtml(): string
    {
        return "\n        ";
    }

    
    private function getTranspiledModuleList(): array
    {
        $modules = array_values(array_filter(
            $this->module->getList(),
            fn ($item) => $this->module->get([$item, 'jsTranspiled'])
        ));

        return array_map(
            fn ($item) => Util::fromCamelCase($item, '-'),
            $modules
        );
    }

    
    private function getBundledModuleList(): array
    {
        $modules = array_values(array_filter(
            $this->module->getList(),
            fn ($item) => $this->module->get([$item, 'bundled'])
        ));

        return array_map(
            fn ($item) => Util::fromCamelCase($item, '-'),
            $modules
        );
    }

    
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }
}
