<?php


namespace Espo\Core\Authentication\Ldap;

use Espo\Core\Api\Util;
use Espo\Core\FieldProcessing\Relation\LinkMultipleSaver;
use Espo\Core\FieldProcessing\EmailAddress\Saver as EmailAddressSaver;
use Espo\Core\FieldProcessing\PhoneNumber\Saver as PhoneNumberSaver;
use Espo\Core\FieldProcessing\Saver\Params as SaverParams;
use Espo\Core\Api\Request;
use Espo\Core\Authentication\AuthToken\AuthToken;
use Espo\Core\Authentication\Ldap\Client as Client;
use Espo\Core\Authentication\Ldap\ClientFactory as ClientFactory;
use Espo\Core\Authentication\Ldap\Utils as LDAPUtils;
use Espo\Core\Authentication\Login;
use Espo\Core\Authentication\Login\Data;
use Espo\Core\Authentication\Logins\Espo;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Result\FailReason;
use Espo\Core\ORM\EntityManager;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Language;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\PasswordHash;
use Espo\Entities\User;
use Exception;

class LdapLogin implements Login
{
    private LDAPUtils $utils;
    private ?Client $client = null;

    private Language $language;

    public function __construct(
        private Config $config,
        private EntityManager $entityManager,
        private PasswordHash $passwordHash,
        Language $defaultLanguage,
        private Log $log,
        private Espo $baseLogin,
        private ClientFactory $clientFactory,
        private LinkMultipleSaver $linkMultipleSaver,
        private EmailAddressSaver $emailAddressSaver,
        private PhoneNumberSaver $phoneNumberSaver,
        private Util $util,
        private bool $isPortal = false
    ) {
        $this->language = $defaultLanguage;

        $this->utils = new LDAPUtils($config);
    }

    
    private $ldapFieldMap = [
        'userName' => 'userNameAttribute',
        'firstName' => 'userFirstNameAttribute',
        'lastName' => 'userLastNameAttribute',
        'title' => 'userTitleAttribute',
        'emailAddress' => 'userEmailAddressAttribute',
        'phoneNumber' => 'userPhoneNumberAttribute',
    ];

    
    private $userFieldMap = [
        'teamsIds' => 'userTeamsIds',
        'defaultTeamId' => 'userDefaultTeamId',
    ];

    
    private $portalUserFieldMap = [
        'portalsIds' => 'portalUserPortalsIds',
        'portalRolesIds' => 'portalUserRolesIds',
    ];

    public function login(Data $data, Request $request): Result
    {
        $username = $data->getUsername();
        $password = $data->getPassword();
        $authToken = $data->getAuthToken();

        $isPortal = $this->isPortal;

        if ($authToken) {
            $user = $this->loginByToken($username, $authToken, $request);

            if ($user) {
                return Result::success($user);
            }
            else {
                return Result::fail(FailReason::WRONG_CREDENTIALS);
            }
        }

        if (!$password || $username == '**logout') {
            return Result::fail(FailReason::NO_PASSWORD);
        }

        if ($isPortal) {
            $useLdapAuthForPortalUser = $this->utils->getOption('portalUserLdapAuth');

            if (!$useLdapAuthForPortalUser) {
                return $this->baseLogin->login($data, $request);
            }
        }

        $ldapClient = $this->getLdapClient();

        
        try {
            $ldapClient->bind();
        }
        catch (Exception $e) {
            $options = $this->utils->getLdapClientOptions();

            $this->log->error(
                'LDAP: Could not connect to LDAP server [' . $options['host'] . '], details: ' . $e->getMessage()
            );

            

            $adminUser = $this->adminLogin($username, $password);

            if (!isset($adminUser)) {
                return Result::fail();
            }

            $this->log->info('LDAP: Administrator [' . $username . '] was logged in by Espo method.');
        }

        $userDn = null;

        if (!isset($adminUser)) {
            

            try {
                $userDn = $this->findLdapUserDnByUsername($username);
            }
            catch (Exception $e) {
                $this->log->error(
                    'Error while finding DN for [' . $username . '], details: ' . $e->getMessage() . '.'
                );
            }

            if (!isset($userDn)) {
                $this->log->error(
                    'LDAP: Authentication failed for user [' . $username . '], details: user is not found.'
                );

                $adminUser = $this->adminLogin($username, $password);

                if (!isset($adminUser)) {
                    return Result::fail();
                }

                $this->log->info('LDAP: Administrator [' . $username . '] was logged in by Espo method.');
            }

            $this->log->debug('User [' . $username . '] is found with this DN ['.$userDn.'].');

            try {
                $ldapClient->bind($userDn, $password);
            }
            catch (Exception $e) {
                $this->log->error(
                    'LDAP: Authentication failed for user [' . $username . '], details: ' . $e->getMessage()
                );

                return Result::fail();
            }
        }

        $user = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'userName' => $username,
                'type!=' => [
                    User::TYPE_API,
                    User::TYPE_SYSTEM,
                    User::TYPE_SUPER_ADMIN,
                ],
            ])
            ->findOne();

        if (!isset($user)) {
            if (!$this->utils->getOption('createEspoUser')) {
                $this->log->warning(
                    "LDAP: Authentication success for user {$username}, but user is not created in EspoCRM."
                );

                return Result::fail(FailReason::USER_NOT_FOUND);
            }

            

            $userData = $ldapClient->getEntry($userDn);

            $user = $this->createUser($userData, $isPortal);
        }

        if (!$user) {
            return Result::fail();
        }

        return Result::success($user);
    }

    private function getLdapClient(): Client
    {
        if (!isset($this->client)) {
            $options = $this->utils->getLdapClientOptions();

            try {
                $this->client = $this->clientFactory->create($options);
            }
            catch (Exception $e) {
                $this->log->error('LDAP error: ' . $e->getMessage());
            }
        }

        
        return $this->client;
    }

    
    private function loginByToken(?string $username, AuthToken $authToken, Request $request): ?User
    {
        if ($username === null) {
            return null;
        }

        $userId = $authToken->getUserId();

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        if (!$user) {
            return null;
        }

        $tokenUsername = $user->getUserName() ?? '';

        if (strtolower($username) !== strtolower($tokenUsername)) {
            $ip = $this->util->obtainIpFromRequest($request);

            $this->log->alert('Unauthorized access attempt for user [' . $username . '] from IP [' . $ip . ']');

            return null;
        }

        
        return $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'userName' => $username,
            ])
            ->findOne();
    }

    private function adminLogin(string $username, string $password): ?User
    {
        $hash = $this->passwordHash->hash($password);

        return $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'userName' => $username,
                'password' => $hash,
                'type' => [User::TYPE_ADMIN, User::TYPE_SUPER_ADMIN],
            ])
            ->findOne();
    }

    
    private function createUser(array $userData, bool $isPortal = false): ?User
    {
        $this->log->info('Creating new user...');

        $data = [];

        $this->log->debug('LDAP: user data: ' . print_r($userData, true));

        $ldapFields = $this->loadFields('ldap');

        foreach ($ldapFields as $espo => $ldap) {
            $ldap = strtolower($ldap);

            if (isset($userData[$ldap][0])) {
                $this->log->debug('LDAP: Create a user with [' . $espo . '] = [' . $userData[$ldap][0] . '].');

                $data[$espo] = $userData[$ldap][0];
            }
        }

        if ($isPortal) {
            $userFields = $this->loadFields('portalUser');

            $userFields['type'] = 'portal';
        }
        else {
            $userFields = $this->loadFields('user');
        }

        foreach ($userFields as $fieldName => $fieldValue) {
            $data[$fieldName] = $fieldValue;
        }

        $user = $this->entityManager->getNewEntity('User');

        $user->set($data);

        $this->entityManager->saveEntity($user, [
            
            SaveOption::SKIP_HOOKS => true,
            SaveOption::KEEP_NEW => true,
        ]);

        $saverParams = SaverParams::create()
            ->withRawOptions([
                'skipLinkMultipleHooks' => true,
            ]);

        $this->linkMultipleSaver->process($user, 'teams', $saverParams);
        $this->linkMultipleSaver->process($user, 'portals', $saverParams);
        $this->linkMultipleSaver->process($user, 'portalRoles', $saverParams);
        $this->emailAddressSaver->process($user, $saverParams);
        $this->phoneNumberSaver->process($user, $saverParams);

        $user->setAsNotNew();
        $user->updateFetchedValues();

        return $this->entityManager->getEntityById(User::ENTITY_TYPE, $user->getId());
    }

    
    private function findLdapUserDnByUsername(string $username): ?string
    {
        $ldapClient = $this->getLdapClient();

        $options = $this->utils->getOptions();

        $loginFilterString = '';

        if (!empty($options['userLoginFilter'])) {
            $loginFilterString = $this->convertToFilterFormat($options['userLoginFilter']);
        }

        $searchString =
            '(&(objectClass=' . $options['userObjectClass'] . ')' .
            '(' . $options['userNameAttribute'] . '=' . $username . ')' .
            $loginFilterString . ')';

        
        $result = $ldapClient->search($searchString, null, Client::SEARCH_SCOPE_SUB);

        $this->log->debug('LDAP: user search string: "' . $searchString . '"');

        foreach ($result as $item) {
            return $item["dn"];
        }

        return null;
    }

    
    private function convertToFilterFormat(string $filter): string
    {
        $filter = trim($filter);

        if (substr($filter, 0, 1) != '(') {
            $filter = '(' . $filter;
        }

        if (substr($filter, -1) != ')') {
            $filter = $filter . ')';
        }

        return $filter;
    }

    
    private function loadFields(string $type): array
    {
        $options = $this->utils->getOptions();

        $typeMap = $type . 'FieldMap';

        $fields = [];

        foreach ($this->$typeMap as $fieldName => $fieldValue) {
            
            if (isset($options[$fieldValue])) {
                $fields[$fieldName] = $options[$fieldValue];
            }
        }

        return $fields;
    }
}
