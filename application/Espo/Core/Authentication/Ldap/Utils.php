<?php


namespace Espo\Core\Authentication\Ldap;

use Espo\Core\Utils\Config;

class Utils
{
    private Config $config;

    
    private ?array $options = null;

    
    private $fieldMap = [
        'host' => 'ldapHost',
        'port' => 'ldapPort',
        'useSsl' => 'ldapSecurity',
        'useStartTls' => 'ldapSecurity',
        'username' => 'ldapUsername',
        'password' => 'ldapPassword',
        'bindRequiresDn' => 'ldapBindRequiresDn',
        'baseDn' => 'ldapBaseDn',
        'accountCanonicalForm' => 'ldapAccountCanonicalForm',
        'accountDomainName' => 'ldapAccountDomainName',
        'accountDomainNameShort' => 'ldapAccountDomainNameShort',
        'accountFilterFormat' => 'ldapAccountFilterFormat',
        'optReferrals' => 'ldapOptReferrals',
        'tryUsernameSplit' => 'ldapTryUsernameSplit',
        'networkTimeout' => 'ldapNetworkTimeout',
        'createEspoUser' => 'ldapCreateEspoUser',
        'userNameAttribute' => 'ldapUserNameAttribute',
        'userTitleAttribute' => 'ldapUserTitleAttribute',
        'userFirstNameAttribute' => 'ldapUserFirstNameAttribute',
        'userLastNameAttribute' => 'ldapUserLastNameAttribute',
        'userEmailAddressAttribute' => 'ldapUserEmailAddressAttribute',
        'userPhoneNumberAttribute' => 'ldapUserPhoneNumberAttribute',
        'userLoginFilter' => 'ldapUserLoginFilter',
        'userTeamsIds' => 'ldapUserTeamsIds',
        'userDefaultTeamId' => 'ldapUserDefaultTeamId',
        'userObjectClass' => 'ldapUserObjectClass',
        'portalUserLdapAuth' => 'ldapPortalUserLdapAuth',
        'portalUserPortalsIds' => 'ldapPortalUserPortalsIds',
        'portalUserRolesIds' => 'ldapPortalUserRolesIds',
    ];

    
    private $permittedEspoOptions = [
        'createEspoUser',
        'userNameAttribute',
        'userObjectClass',
        'userTitleAttribute',
        'userFirstNameAttribute',
        'userLastNameAttribute',
        'userEmailAddressAttribute',
        'userPhoneNumberAttribute',
        'userLoginFilter',
        'userTeamsIds',
        'userDefaultTeamId',
        'portalUserLdapAuth',
        'portalUserPortalsIds',
        'portalUserRolesIds',
    ];

    
    private $accountCanonicalFormMap = [
        'Dn' => 1,
        'Username' => 2,
        'Backslash' => 3,
        'Principal' => 4,
    ];

    public function __construct(Config $config = null)
    {
        if (isset($config)) {
            $this->config = $config;
        }
    }

    
    public function getOptions(): array
    {
        if (isset($this->options)) {
            return $this->options;
        }

        $options = [];

        foreach ($this->fieldMap as $ldapName => $espoName) {
            $option = $this->config->get($espoName);

            if (isset($option)) {
                $options[$ldapName] = $option;
            }
        }

        $this->options = $this->normalizeOptions($options);

        return $this->options;
    }

    
    public function normalizeOptions(array $options): array
    {
        $useSsl = ($options['useSsl'] ?? null) == 'SSL';
        $useStartTls = ($options['useStartTls'] ?? null) == 'TLS';
        $accountCanonicalFormKey = $options['accountCanonicalForm'] ?? 'Dn';

        $options['useSsl'] = $useSsl;
        $options['useStartTls'] = $useStartTls;
        $options['accountCanonicalForm'] = $this->accountCanonicalFormMap[$accountCanonicalFormKey] ?? 1;

        return $options;
    }

    
    public function getOption($name, $returns = null)
    {
        if (!isset($this->options)) {
            $this->getOptions();
        }

        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return $returns;
    }

    
    public function getLdapClientOptions(): array
    {
        $options = $this->getOptions();

        $zendOptions = array_diff_key($options, array_flip($this->permittedEspoOptions));

        return $zendOptions;
    }
}
