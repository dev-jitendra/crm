<?php


namespace Espo\Tools\App;

use Espo\Core\Utils\ThemeManager;
use Espo\Entities\Settings;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Authentication\Util\MethodProvider as AuthenticationMethodProvider;
use Espo\Core\ApplicationState;
use Espo\Core\Acl;
use Espo\Core\InjectableFactory;
use Espo\Core\DataManager;
use Espo\Core\FieldValidation\FieldValidationManager;
use Espo\Core\Utils\Currency\DatabasePopulator as CurrencyDatabasePopulator;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Core\Utils\Config\Access;

use Espo\Entities\Portal;
use Espo\Repositories\Portal as PortalRepository;

use stdClass;

class SettingsService
{
    public function __construct(
        private ApplicationState $applicationState,
        private Config $config,
        private ConfigWriter $configWriter,
        private Metadata $metadata,
        private Acl $acl,
        private EntityManager $entityManager,
        private DataManager $dataManager,
        private FieldValidationManager $fieldValidationManager,
        private InjectableFactory $injectableFactory,
        private Access $access,
        private AuthenticationMethodProvider $authenticationMethodProvider,
        private ThemeManager $themeManager
    ) {}

    
    public function getConfigData(): stdClass
    {
        $data = $this->config->getAllNonInternalData();

        $this->filterDataByAccess($data);
        $this->filterData($data);
        $this->loadAdditionalParams($data);

        return $data;
    }

    
    public function getMetadataConfigData(): stdClass
    {
        $data = (object) [];

        unset($data->loginView);

        $loginView = $this->metadata->get(['clientDefs', 'App', 'loginView']);

        if ($loginView) {
            $data->loginView = $loginView;
        }

        $loginData = $this->getLoginData();

        if ($loginData) {
            $data->loginData = (object) $loginData;
        }

        return $data;
    }

    
    private function getLoginData(): ?array
    {
        $method = $this->authenticationMethodProvider->get();

        
        $mData = $this->metadata->get(['authenticationMethods', $method, 'login']) ?? [];

        
        $handler = $mData['handler'] ?? null;

        if (!$handler) {
            return null;
        }

        $isProvider = $this->isPortalWithAuthenticationProvider();

        if (!$isProvider && $this->applicationState->isPortal()) {
            
            $portal = $mData['portal'] ?? null;

            if ($portal === null) {
                
                $portalConfigParam = $mData['portalConfigParam'] ?? null;

                $portal = $portalConfigParam && $this->config->get($portalConfigParam);
            }

            if (!$portal) {
                return null;
            }
        }

        
        $fallback = !$this->applicationState->isPortal() ?
            ($mData['fallback'] ?? null) :
            false;

        if ($fallback === null) {
            
            $fallbackConfigParam = $mData['fallbackConfigParam'] ?? null;

            $fallback = $fallbackConfigParam && $this->config->get($fallbackConfigParam);
        }

        if ($isProvider) {
            $fallback = false;
        }

        
        $data = (object) ($mData['data'] ?? []);

        return [
            'handler' => $handler,
            'fallback' => $fallback,
            'method' => $method,
            'data' => $data,
        ];
    }

    private function isPortalWithAuthenticationProvider(): bool
    {
        if (!$this->applicationState->isPortal()) {
            return false;
        }

        $portal = $this->applicationState->getPortal();

        return (bool) $this->authenticationMethodProvider->getForPortal($portal);
    }

    
    public function setConfigData(stdClass $data): void
    {
        $user = $this->applicationState->getUser();

        if (!$user->isAdmin()) {
            throw new Forbidden();
        }

        $ignoreItemList = array_merge(
            $this->access->getSystemParamList(),
            $this->access->getReadOnlyParamList(),
            $this->isRestrictedMode() && !$user->isSuperAdmin() ?
                $this->access->getSuperAdminParamList() : []
        );

        foreach ($ignoreItemList as $item) {
            unset($data->$item);
        }

        $entity = $this->entityManager->getNewEntity(Settings::ENTITY_TYPE);

        $entity->set($data);
        $entity->setAsNotNew();

        $this->processValidation($entity, $data);

        if (
            isset($data->useCache) &&
            $data->useCache !== $this->config->get('useCache')
        ) {
            $this->dataManager->clearCache();
        }

        $this->configWriter->setMultiple(get_object_vars($data));
        $this->configWriter->save();

        if (isset($data->personNameFormat)) {
            $this->dataManager->clearCache();
        }

        if (isset($data->defaultCurrency) || isset($data->baseCurrency) || isset($data->currencyRates)) {
            $this->populateDatabaseWithCurrencyRates();
        }
    }

    private function loadAdditionalParams(stdClass $data): void
    {
        if ($this->applicationState->isPortal()) {
            $portal = $this->applicationState->getPortal();

            $this->getPortalRepository()->loadUrlField($portal);

            $data->siteUrl = $portal->get('url');
        }

        if (
            ($this->config->get('outboundEmailFromAddress') || $this->config->get('internalSmtpServer')) &&
            !$this->config->get('passwordRecoveryDisabled')
        ) {
            $data->passwordRecoveryEnabled = true;
        }

        $data->logoSrc = $this->themeManager->getLogoSrc();
    }

    private function filterDataByAccess(stdClass $data): void
    {
        $user = $this->applicationState->getUser();

        $ignoreItemList = [];

        foreach ($this->access->getSystemParamList() as $item) {
            $ignoreItemList[] = $item;
        }

        foreach ($this->access->getInternalParamList() as $item) {
            $ignoreItemList[] = $item;
        }

        if (!$user->isAdmin() || $user->isSystem()) {
            foreach ($this->access->getAdminParamList() as $item) {
                $ignoreItemList[] = $item;
            }
        }

        if ($this->isRestrictedMode() && !$user->isSuperAdmin()) {
            
        }

        foreach ($ignoreItemList as $item) {
            unset($data->$item);
        }

        if ($user->isSystem()) {
            $globalItemList = $this->access->getGlobalParamList();

            foreach (array_keys(get_object_vars($data)) as $item) {
                if (!in_array($item, $globalItemList)) {
                    unset($data->$item);
                }
            }
        }
    }

    private function filterEntityTypeParams(stdClass $data): void
    {
        $entityTypeListParamList = $this->metadata->get(['app', 'config', 'entityTypeListParamList']) ?? [];

        
        $scopeList = array_keys($this->metadata->get(['entityDefs'], []));

        foreach ($scopeList as $scope) {
            if (!$this->metadata->get(['scopes', $scope, 'acl'])) {
                continue;
            }

            if ($this->acl->tryCheck($scope)) {
                continue;
            }

            foreach ($entityTypeListParamList as $param) {
                $list = $data->$param ?? [];

                foreach ($list as $i => $item) {
                    if ($item === $scope) {
                        unset($list[$i]);
                    }
                }

                $data->$param = array_values($list);
            }
        }
    }

    private function populateDatabaseWithCurrencyRates(): void
    {
        $this->injectableFactory->create(CurrencyDatabasePopulator::class)->process();
    }

    private function filterData(stdClass $data): void
    {
        $user = $this->applicationState->getUser();

        if (!$user->isAdmin() && !$user->isSystem()) {
            $this->filterEntityTypeParams($data);
        }

        $fieldDefs = $this->metadata->get(['entityDefs', 'Settings', 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if ($fieldParams['type'] === 'password') {
                unset($data->$field);
            }
        }

        if (empty($data->useWebSocket)) {
            unset($data->webSocketUrl);
        }

        if ($user->isSystem()) {
            return;
        }

        if ($user->isAdmin()) {
            return;
        }

        if (
            !$this->acl->checkScope('Email', 'create') ||
            !$this->config->get('outboundEmailIsShared')
        ) {
            unset($data->outboundEmailFromAddress);
            unset($data->outboundEmailFromName);
            unset($data->outboundEmailBccAddress);
        }
    }

    private function isRestrictedMode(): bool
    {
        return (bool) $this->config->get('restrictedMode');
    }

    
    private function processValidation(Entity $entity, stdClass $data): void
    {
        $this->fieldValidationManager->process($entity, $data);
    }

    private function getPortalRepository(): PortalRepository
    {
        
        return $this->entityManager->getRepository(Portal::ENTITY_TYPE);
    }
}
