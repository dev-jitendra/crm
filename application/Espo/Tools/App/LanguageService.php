<?php


namespace Espo\Tools\App;

use Espo\Core\Utils\Language as LanguageUtil;
use Espo\Core\Acl;
use Espo\Core\Container;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\Tools\App\Language\AclDependencyProvider;

class LanguageService
{
    public function __construct(
        private Metadata $metadata,
        private Acl $acl,
        private User $user,
        private AclDependencyProvider $aclDependencyProvider,
        private Container $container
    ) {}

    
    protected function getDefaultLanguage(): LanguageUtil
    {
        
        return $this->container->get('defaultLanguage');
    }

    protected function getLanguage(): LanguageUtil
    {
        
        return $this->container->get('language');
    }

    
    public function getDataForFrontend(bool $default = false): array
    {
        if ($default) {
            $languageObj = $this->getDefaultLanguage();
        }
        else {
            $languageObj = $this->getLanguage();
        }

        $data = $languageObj->getAll();

        if ($this->user->isSystem()) {
            unset($data['Global']['scopeNames']);
            unset($data['Global']['scopeNamesPlural']);
            unset($data['Global']['dashlets']);
            unset($data['Global']['links']);
            unset($data['Global']['fields']);
            unset($data['Global']['options']);

            foreach ($data as $k => $item) {
                if (
                    in_array($k, ['Global', 'User', 'Campaign']) ||
                    $this->metadata->get(['scopes', $k, 'languageIsGlobal'])
                ) {
                    continue;
                }

                unset($data[$k]);
            }

            unset($data['User']['fields']);
            unset($data['User']['links']);
            unset($data['User']['options']);
            unset($data['User']['filters']);
            unset($data['User']['presetFilters']);
            unset($data['User']['boolFilters']);
            unset($data['User']['tooltips']);

            unset($data['Campaign']['fields']);
            unset($data['Campaign']['links']);
            unset($data['Campaign']['options']);
            unset($data['Campaign']['tooltips']);
            unset($data['Campaign']['presetFilters']);
        }
        else {
            
            $scopeList = array_keys($this->metadata->get(['scopes'], []));

            foreach ($scopeList as $scope) {
                if (!$this->metadata->get(['scopes', $scope, 'entity'])) {
                    continue;
                }

                if ($this->metadata->get(['scopes', $scope, 'languageAclDisabled'])) {
                    continue;
                }

                if (!$this->acl->tryCheck($scope)) {
                    unset($data[$scope]);
                    unset($data['Global']['scopeNames'][$scope]);
                    unset($data['Global']['scopeNamesPlural'][$scope]);
                }
                else {
                    if (in_array($scope, ['EmailAccount', 'InboundEmail'])) {
                        continue;
                    }

                    foreach ($this->acl->getScopeForbiddenFieldList($scope) as $field) {
                        if (isset($data[$scope]['fields'])) {
                            unset($data[$scope]['fields'][$field]);
                        }

                        if (isset($data[$scope]['options'])) {
                            unset($data[$scope]['options'][$field]);
                        }

                        if (isset($data[$scope]['links'])) {
                            unset($data[$scope]['links'][$field]);
                        }
                    }

                    $this->unsetEmpty($data, $scope);
                }
            }

            if (!$this->user->isAdmin()) {
                $this->prepareDataNonAdmin($data, $languageObj);
            }
        }

        $data['User']['fields'] = $data['User']['fields'] ?? [];

        $data['User']['fields']['password'] = $languageObj->translate('password', 'fields', 'User');
        $data['User']['fields']['passwordConfirm'] = $languageObj->translate('passwordConfirm', 'fields', 'User');
        $data['User']['fields']['newPassword'] = $languageObj->translate('newPassword', 'fields', 'User');
        $data['User']['fields']['newPasswordConfirm'] = $languageObj->translate('newPasswordConfirm', 'fields', 'User');

        return $data;
    }

    
    private function unsetEmpty(array &$data, string $scope): void
    {
        if (($data[$scope]['options'] ?? null) === []) {
            unset($data[$scope]['options']);
        }

        if (($data[$scope]['fields'] ?? null) === []) {
            unset($data[$scope]['fields']);
        }

        if (($data[$scope]['links'] ?? null) === []) {
            unset($data[$scope]['links']);
        }
    }

    
    private function prepareDataNonAdmin(array &$data, LanguageUtil $languageObj): void
    {
        unset($data['Admin']);
        unset($data['LayoutManager']);
        unset($data['EntityManager']);
        unset($data['FieldManager']);
        unset($data['Settings']);
        unset($data['ApiUser']);
        unset($data['DynamicLogic']);

        $data['Settings'] = [
            'options' => [
                'auth2FAMethodList' => $languageObj->get(['Settings', 'options', 'auth2FAMethodList']),
            ],
        ];

        $data['Admin'] = [
            'messages' => [
                'userHasNoEmailAddress' => $languageObj->translate('userHasNoEmailAddress', 'messages', 'Admin'),
            ],
        ];

        foreach ($this->aclDependencyProvider->get() as $dependencyItem) {
            $target = $dependencyItem->getTarget();
            $aclScope = $dependencyItem->getScope();
            $aclField = $dependencyItem->getField();
            $anyScopeList = $dependencyItem->getAnyScopeList();

            $targetArr = explode('.', $target);

            $isFullScope = !str_contains($target, '.');

            if ($isFullScope && isset($data[$target])) {
                continue;
            }

            if ($anyScopeList) {
                $skip = true;

                foreach ($anyScopeList as $itemScope) {
                    if ($this->acl->tryCheck($itemScope)) {
                        $skip = false;

                        break;
                    }
                }

                if ($skip) {
                    continue;
                }
            }

            if ($aclScope) {
                if (!$this->acl->tryCheck($aclScope)) {
                    continue;
                }

                if ($aclField && in_array($aclField, $this->acl->getScopeForbiddenFieldList($aclScope))) {
                    continue;
                }
            }

            $pointer =& $data;

            foreach ($targetArr as $i => $k) {
                if ($i === count($targetArr) - 1) {
                    $pointer[$k] = $languageObj->get($targetArr);

                    break;
                }

                if (!isset($pointer[$k])) {
                    $pointer[$k] = [];
                }

                $pointer =& $pointer[$k];
            }

            if ($isFullScope) {
                $this->unsetEmpty($data, $target);
            }
        }
    }
}
