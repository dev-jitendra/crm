<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Authentication\Ldap\Utils as LDAPUtils;
use Espo\Core\Authentication\Ldap\Client as LDAPClient;
use Espo\Core\Api\Request;
use Espo\Core\Utils\Config;
use Espo\Entities\User;

use Laminas\Ldap\Exception\LdapException;


class Ldap
{
    private User $user;
    private Config $config;

    public function __construct(
        User $user,
        Config $config
    ) {
        $this->user = $user;
        $this->config = $config;
    }

    
    public function postActionTestConnection(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $data = $request->getParsedBody();

        if (!isset($data->password)) {
            $data->password = $this->config->get('ldapPassword');
        }

        $ldapUtils = new LDAPUtils();

        $options = $ldapUtils->normalizeOptions(
            get_object_vars($data)
        );

        $ldapClient = new LDAPClient($options);

        
        $ldapClient->bind();

        return true;
    }
}
