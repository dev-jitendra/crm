<?php

namespace Laminas\Ldap;

use Laminas\Ldap\Collection;
use Laminas\Ldap\Exception;
use LDAP\Connection;
use Traversable;

use function array_change_key_case;
use function array_key_exists;
use function array_merge;
use function array_reverse;
use function array_shift;
use function array_values;
use function class_exists;
use function count;
use function dechex;
use function extension_loaded;
use function function_exists;
use function implode;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function is_subclass_of;
use function iterator_to_array;
use function key;
use function mb_strtolower;
use function min;
use function preg_match_all;
use function sprintf;
use function str_replace;
use function strcasecmp;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trim;
use function usleep;

use const CASE_LOWER;
use const E_WARNING;
use const PREG_SET_ORDER;

class Ldap
{
    public const SEARCH_SCOPE_SUB  = 1;
    public const SEARCH_SCOPE_ONE  = 2;
    public const SEARCH_SCOPE_BASE = 3;

    public const ACCTNAME_FORM_DN        = 1;
    public const ACCTNAME_FORM_USERNAME  = 2;
    public const ACCTNAME_FORM_BACKSLASH = 3;
    public const ACCTNAME_FORM_PRINCIPAL = 4;

    
    private ?string $connectString = null;

    
    protected $options;

    
    protected $resource;

    
    protected $boundUser = false;

    
    protected $rootDse;

    
    protected $schema;

    
    protected $reconnectCount = 0;

    
    protected $reconnectsAttempted = 0;

    
    protected $lastConnectBindParams = [];

    
    public function __construct($options = [])
    {
        if (! extension_loaded('ldap')) {
            throw new Exception\LdapException(
                null,
                'LDAP extension not loaded',
                Exception\LdapException::LDAP_X_EXTENSION_NOT_LOADED
            );
        }
        $this->setOptions($options);
    }

    
    public function __destruct()
    {
        $this->disconnect();
    }

    
    public function getResource()
    {
        if (! Handler::isLdapHandle($this->resource) || $this->boundUser === false) {
            $this->bind();
        }

        return $this->resource;
    }

    
    public function getLastErrorCode()
    {
        ErrorHandler::start(E_WARNING);
        $ret = false;
        if (Handler::isLdapHandle($this->resource)) {
            $ret = ldap_get_option($this->resource, LDAP_OPT_ERROR_NUMBER, $err);
        }
        ErrorHandler::stop();
        if ($ret === true) {
            if ($err <= -1 && $err >= -17) {
                
                $err = Exception\LdapException::LDAP_SERVER_DOWN + (-$err - 1);
            }
            return $err;
        }

        return 0;
    }

    
    public function getLastError(&$errorCode = null, ?array &$errorMessages = null)
    {
        $errorCode     = $this->getLastErrorCode();
        $errorMessages = [];

        
        ErrorHandler::start(E_WARNING);
        $estr1 = "getLastError: could not call ldap_error because LDAP resource was not of type resource";
        if (Handler::isLdapHandle($this->resource)) {
            $estr1 = ldap_error($this->resource);
        }
        ErrorHandler::stop();
        if ($errorCode !== 0 && $estr1 === 'Success') {
            ErrorHandler::start(E_WARNING);
            $estr1 = ldap_err2str($errorCode);
            ErrorHandler::stop();
        }
        if (! empty($estr1)) {
            $errorMessages[] = $estr1;
        }

        ErrorHandler::start(E_WARNING);
        $estr2 = "getLastError: could not call ldap_get_option because LDAP resource was not of type resource";
        if (Handler::isLdapHandle($this->resource)) {
            ldap_get_option($this->resource, LDAP_OPT_ERROR_STRING, $estr2);
            
        }
        ErrorHandler::stop();
        if (! empty($estr2) && ! in_array($estr2, $errorMessages)) {
            $errorMessages[] = $estr2;
        }

        $message = '';
        if ($errorCode > 0) {
            $message = '0x' . dechex($errorCode) . ' ';
        }

        if (count($errorMessages) > 0) {
            $message .= '(' . implode('; ', $errorMessages) . ')';
        } else {
            $message .= '(no error message from LDAP)';
        }

        return $message;
    }

    
    public function getBoundUser()
    {
        return $this->boundUser;
    }

    
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        $permittedOptions = [
            'host'                   => null,
            'port'                   => 0,
            'useSsl'                 => false,
            'username'               => null,
            'password'               => null,
            'bindRequiresDn'         => false,
            'baseDn'                 => null,
            'accountCanonicalForm'   => null,
            'accountDomainName'      => null,
            'accountDomainNameShort' => null,
            'accountFilterFormat'    => null,
            'allowEmptyPassword'     => false,
            'useStartTls'            => false,
            'optReferrals'           => false,
            'tryUsernameSplit'       => true,
            'reconnectAttempts'      => 0,
            'networkTimeout'         => null,
            'saslOpts'               => null,
        ];

        foreach ($permittedOptions as $key => $val) {
            if (array_key_exists($key, $options)) {
                $val = $options[$key];
                unset($options[$key]);
                
                switch ($key) {
                    case 'port':
                    case 'accountCanonicalForm':
                    case 'reconnectAttempts':
                    case 'networkTimeout':
                        $permittedOptions[$key] = (int) $val;
                        break;
                    case 'useSsl':
                    case 'bindRequiresDn':
                    case 'allowEmptyPassword':
                    case 'useStartTls':
                    case 'optReferrals':
                    case 'tryUsernameSplit':
                        $permittedOptions[$key] = $val === true
                            || $val === '1'
                            || strcasecmp($val, 'true') === 0;
                        break;
                    case 'saslOpts':
                        $permittedOptions[$key] = $val;
                        break;
                    default:
                        $permittedOptions[$key] = trim((string) $val);
                        break;
                }
            }
        }
        if (count($options) > 0) {
            $key = key($options);
            throw new Exception\LdapException(null, "Unknown Laminas\\Ldap\\Ldap option: $key");
        }
        $this->options = $permittedOptions;

        return $this;
    }

    
    public function getOptions()
    {
        return $this->options;
    }

    
    public function getReconnectsAttempted()
    {
        return $this->reconnectsAttempted;
    }

    
    public function resetReconnectsAttempted()
    {
        $this->reconnectsAttempted = 0;
    }

    
    protected function getHost()
    {
        return $this->options['host'];
    }

    
    protected function getPort()
    {
        return $this->options['port'];
    }

    
    protected function getUseSsl()
    {
        return $this->options['useSsl'];
    }

    
    protected function getUsername()
    {
        return $this->options['username'];
    }

    
    protected function getPassword()
    {
        return $this->options['password'];
    }

    
    protected function getBindRequiresDn()
    {
        return $this->options['bindRequiresDn'];
    }

    
    public function getBaseDn()
    {
        return $this->options['baseDn'];
    }

    
    public function getSaslOpts()
    {
        return $this->options['saslOpts'];
    }

    
    protected function getAccountCanonicalForm()
    {
        
        $accountCanonicalForm = $this->options['accountCanonicalForm'];
        if (! $accountCanonicalForm) {
            $accountDomainName      = $this->getAccountDomainName();
            $accountDomainNameShort = $this->getAccountDomainNameShort();
            if ($accountDomainNameShort) {
                $accountCanonicalForm = self::ACCTNAME_FORM_BACKSLASH;
            } else {
                if ($accountDomainName) {
                    $accountCanonicalForm = self::ACCTNAME_FORM_PRINCIPAL;
                } else {
                    $accountCanonicalForm = self::ACCTNAME_FORM_USERNAME;
                }
            }
        }

        return $accountCanonicalForm;
    }

    
    protected function getAccountDomainName()
    {
        return $this->options['accountDomainName'];
    }

    
    protected function getAccountDomainNameShort()
    {
        return $this->options['accountDomainNameShort'];
    }

    
    protected function getAccountFilterFormat()
    {
        return $this->options['accountFilterFormat'];
    }

    
    protected function getAllowEmptyPassword()
    {
        return $this->options['allowEmptyPassword'];
    }

    
    protected function getUseStartTls()
    {
        return $this->options['useStartTls'];
    }

    
    protected function getOptReferrals()
    {
        return $this->options['optReferrals'];
    }

    
    protected function getTryUsernameSplit()
    {
        return $this->options['tryUsernameSplit'];
    }

    
    protected function getReconnectsToAttempt()
    {
        return $this->options['reconnectAttempts'];
    }

    
    protected function getNetworkTimeout()
    {
        return $this->options['networkTimeout'];
    }

    
    protected function getAccountFilter($acctname)
    {
        $dname = '';
        $aname = '';
        $this->splitName($acctname, $dname, $aname);
        $accountFilterFormat = $this->getAccountFilterFormat();
        $aname               = Filter\AbstractFilter::escapeValue($aname);
        if ($accountFilterFormat) {
            return sprintf($accountFilterFormat, $aname);
        }
        if (! $this->getBindRequiresDn()) {
            
            return sprintf("(&(objectClass=user)(sAMAccountName=%s))", $aname);
        }

        return sprintf("(&(objectClass=posixAccount)(uid=%s))", $aname);
    }

    
    protected function splitName($name, &$dname, &$aname)
    {
        $dname = null;
        $aname = $name;

        if (! $this->getTryUsernameSplit()) {
            return;
        }

        $pos = strpos($name, '@');
        if ($pos) {
            $dname = substr($name, $pos + 1);
            $aname = substr($name, 0, $pos);
        } else {
            $pos = strpos($name, '\\');
            if ($pos) {
                $dname = substr($name, 0, $pos);
                $aname = substr($name, $pos + 1);
            }
        }
    }

    
    protected function getAccountDn($acctname)
    {
        if (Dn::checkDn($acctname)) {
            return $acctname;
        }
        $acctname = $this->getCanonicalAccountName($acctname, self::ACCTNAME_FORM_USERNAME);
        $acct     = $this->getAccount($acctname, ['dn']);

        return $acct['dn'];
    }

    
    protected function isPossibleAuthority($dname)
    {
        if ($dname === null) {
            return true;
        }
        $accountDomainName      = $this->getAccountDomainName();
        $accountDomainNameShort = $this->getAccountDomainNameShort();
        if ($accountDomainName === null && $accountDomainNameShort === null) {
            return true;
        }
        if (strcasecmp($dname, $accountDomainName) === 0) {
            return true;
        }
        if (strcasecmp($dname, $accountDomainNameShort) === 0) {
            return true;
        }

        return false;
    }

    
    public function getCanonicalAccountName($acctname, $form = 0)
    {
        $dname = '';
        $uname = '';

        $this->splitName($acctname, $dname, $uname);

        if (! $this->isPossibleAuthority($dname)) {
            throw new Exception\LdapException(
                null,
                "Binding domain is not an authority for user: $acctname",
                Exception\LdapException::LDAP_X_DOMAIN_MISMATCH
            );
        }

        if (! $uname) {
            throw new Exception\LdapException(null, "Invalid account name syntax: $acctname");
        }

        if (function_exists('mb_strtolower')) {
            $uname = mb_strtolower($uname, 'UTF-8');
        } else {
            $uname = strtolower($uname);
        }

        if ($form === 0) {
            $form = $this->getAccountCanonicalForm();
        }

        switch ($form) {
            case self::ACCTNAME_FORM_DN:
                return $this->getAccountDn($acctname);
            case self::ACCTNAME_FORM_USERNAME:
                return $uname;
            case self::ACCTNAME_FORM_BACKSLASH:
                $accountDomainNameShort = $this->getAccountDomainNameShort();
                if (! $accountDomainNameShort) {
                    throw new Exception\LdapException(null, 'Option required: accountDomainNameShort');
                }
                return "$accountDomainNameShort\\$uname";
            case self::ACCTNAME_FORM_PRINCIPAL:
                $accountDomainName = $this->getAccountDomainName();
                if (! $accountDomainName) {
                    throw new Exception\LdapException(null, 'Option required: accountDomainName');
                }
                return "$uname@$accountDomainName";
            default:
                throw new Exception\LdapException(null, "Unknown canonical name form: $form");
        }
    }

    
    protected function getAccount($acctname, ?array $attrs = null)
    {
        $baseDn = $this->getBaseDn();
        if (! $baseDn) {
            throw new Exception\LdapException(null, 'Base DN not set');
        }

        $accountFilter = $this->getAccountFilter($acctname);
        if (! $accountFilter) {
            throw new Exception\LdapException(null, 'Invalid account filter');
        }

        if (! Handler::isLdapHandle($this->getResource())) {
            $this->bind();
        }

        $accounts = $this->search($accountFilter, $baseDn, self::SEARCH_SCOPE_SUB, $attrs);
        $count    = $accounts->count();
        if ($count === 1) {
            $acct = $accounts->getFirst();
            $accounts->close();

            return $acct;
        } else {
            if ($count === 0) {
                $code = Exception\LdapException::LDAP_NO_SUCH_OBJECT;
                $str  = "No object found for: $accountFilter";
            } else {
                $code = Exception\LdapException::LDAP_OPERATIONS_ERROR;
                $str  = "Unexpected result count ($count) for: $accountFilter";
            }
        }
        $accounts->close();

        throw new Exception\LdapException($this, $str, $code);
    }

    
    protected function selectParam($method, $parameter, $property)
    {
        if ($this->reconnectCount > 0) {
            return self::coalesce(
                isset($this->lastConnectBindParams[$method]) ? $this->lastConnectBindParams[$method][$parameter] : null,
                $property
            );
        } else {
            return $property;
        }
    }

    
    protected static function coalesce($a, $b)
    {
        if ($a !== null) {
            return $a;
        }
        return $b;
    }

    
    public function disconnect()
    {
        $this->unbind();
        $this->resetReconnectsAttempted();
        return $this;
    }

    
    protected function unbind()
    {
        if (Handler::isLdapHandle($this->resource) && is_string($this->boundUser)) {
            ErrorHandler::start(E_WARNING);
            ldap_unbind($this->resource);
            ErrorHandler::stop();
        }
        $this->resource  = null;
        $this->boundUser = false;

        return $this;
    }

    
    public function connect($host = null, $port = null, $useSsl = null, $useStartTls = null, $networkTimeout = null)
    {
        if ($this->reconnectCount === 0) {
            $this->lastConnectBindParams[__METHOD__] = [
                'host'           => $host,
                'port'           => $port,
                'useSsl'         => $useSsl,
                'useStartTls'    => $useStartTls,
                'networkTimeout' => $networkTimeout,
            ];
        }

        if ($host === null) {
            $host = $this->selectParam(__METHOD__, 'host', $this->getHost());
        }
        if ($port === null) {
            $port = $this->selectParam(__METHOD__, 'port', $this->getPort());
        } else {
            $port = (int) $port;
        }

        if ($useSsl === null) {
            $useSsl = $this->selectParam(__METHOD__, 'useSsl', $this->getUseSsl());
        } else {
            $useSsl = (bool) $useSsl;
        }

        if ($port === 0) {
            $port = $useSsl ? 636 : 389;
        }

        if ($useStartTls === null) {
            $useStartTls = $this->selectParam(__METHOD__, 'useStartTls', $this->getUseStartTls());
        } else {
            $useStartTls = (bool) $useStartTls;
        }
        if ($networkTimeout === null) {
            $networkTimeout = $this->selectParam(__METHOD__, 'networkTimeout', $this->getNetworkTimeout());
        } else {
            $networkTimeout = (int) $networkTimeout;
        }

        if (! $host) {
            throw new Exception\LdapException(null, 'A host parameter is required');
        }

        
        $hosts = [];
        if (preg_match_all('~ldap(i|s)?:
            $this->connectString = $host;
            
            $useSsl = isset($hosts[0][1]) && $hosts[0][1] === 's' ? true : false;
        } else {
            if ($useSsl) {
                $this->connectString = 'ldaps:
            } else {
                $this->connectString = 'ldap:
            }
            if ($port) {
                $this->connectString .= ':' . $port;
            }
        }

        $this->disconnect();

        
        
        ErrorHandler::start();
        $resource = ldap_connect($this->connectString);
        ErrorHandler::stop();

        if (Handler::isLdapHandle($resource)) {
            $this->resource  = $resource;
            $this->boundUser = false;

            $optReferrals = $this->getOptReferrals() ? 1 : 0;
            ErrorHandler::start(E_WARNING);
            if (
                ldap_set_option($resource, LDAP_OPT_PROTOCOL_VERSION, 3)
                && ldap_set_option($resource, LDAP_OPT_REFERRALS, $optReferrals)
            ) {
                if ($networkTimeout) {
                    ldap_set_option($resource, LDAP_OPT_NETWORK_TIMEOUT, $networkTimeout);
                }
                if ($useSsl || ! $useStartTls || ldap_start_tls($resource)) {
                    ErrorHandler::stop();
                    return $this;
                }
            }
            ErrorHandler::stop();

            $zle = new Exception\LdapException($this, "$host:$port");
            $this->disconnect();
            throw $zle;
        }

        throw new Exception\LdapException(null, "Failed to connect to LDAP server: $host:$port", -1);
    }

    
    public function bind($username = null, $password = null, $saslOpts = null)
    {
        $moreCreds = true;

        if (is_string($password)) {
            
            
            $password = str_replace("\0", '', $password);
        }

        if ($this->reconnectCount === 0) {
            $this->lastConnectBindParams[__METHOD__] = [
                'username' => $username,
                'password' => $password,
            ];
        }

        if ($username === null) {
            $username  = $this->selectParam(__METHOD__, 'username', $this->getUsername());
            $password  = $this->selectParam(__METHOD__, 'password', $this->getPassword());
            $moreCreds = false;
        }

        if ($saslOpts === null) {
            $saslOpts = $this->getSaslOpts();
        }

        if (empty($username)) {
            
            $username = null;
            $password = null;
        } else {
            
            if (! Dn::checkDn($username)) {
                if ($this->getBindRequiresDn()) {
                    
                    if ($moreCreds) {
                        try {
                            $username = $this->getAccountDn($username);
                        } catch (Exception\LdapException $zle) {
                            switch ($zle->getCode()) {
                                case Exception\LdapException::LDAP_NO_SUCH_OBJECT:
                                case Exception\LdapException::LDAP_X_DOMAIN_MISMATCH:
                                case Exception\LdapException::LDAP_X_EXTENSION_NOT_LOADED:
                                    throw $zle;
                            }
                            throw new Exception\LdapException(
                                null,
                                'Failed to retrieve DN for account: ' . $username
                                    . ' [' . $zle->getMessage() . ']',
                                Exception\LdapException::LDAP_OPERATIONS_ERROR
                            );
                        }
                    } else {
                        throw new Exception\LdapException(null, 'Binding requires username in DN form');
                    }
                } else {
                    $username = $this->getCanonicalAccountName(
                        $username,
                        $this->getAccountCanonicalForm()
                    );
                }
            }
        }

        if (! Handler::isLdapHandle($this->resource)) {
            $this->connect();
        }

        if ($username !== null && $password === '' && $this->getAllowEmptyPassword() !== true) {
            $zle = new Exception\LdapException(
                null,
                'Empty password not allowed - see allowEmptyPassword option.'
            );
        } else {
            ErrorHandler::start(E_WARNING);
            if (is_array($saslOpts)) {
                $sasl_mech     = array_key_exists('sasl_mech', $saslOpts) ? $saslOpts['sasl_mech'] : null;
                $sasl_realm    = array_key_exists('sasl_realm', $saslOpts) ? $saslOpts['sasl_realm'] : null;
                $sasl_authc_id = array_key_exists('sasl_authc_id', $saslOpts) ? $saslOpts['sasl_authc_id'] : null;
                $sasl_authz_id = array_key_exists('sasl_authz_id', $saslOpts) ? $saslOpts['sasl_authz_id'] : null;
                $sasl_props    = array_key_exists('props', $saslOpts) ? $saslOpts['props'] : null;

                $bind = ldap_sasl_bind(
                    $this->resource,
                    $username,
                    $password,
                    $sasl_mech,
                    $sasl_realm,
                    $sasl_authc_id,
                    $sasl_authz_id,
                    $sasl_props
                );
            } else {
                $bind = ldap_bind($this->resource, $username, $password);
            }
            ErrorHandler::stop();

            if ($bind !== false) {
                $this->boundUser = $username;
                return $this;
            }

            $this->boundUser = false;

            if ($this->shouldReconnect($this->resource)) {
                return $this;
            }

            $message = $username ?? $this->connectString;
            switch ($this->getLastErrorCode()) {
                case Exception\LdapException::LDAP_SERVER_DOWN:
                    
                    $message = $this->connectString;
            }

            $zle = new Exception\LdapException($this, $message);
        }
        $this->unbind();

        throw $zle;
    }

    
    protected function shouldReconnect($resource)
    {
        if (
            $this->reconnectCount >= $this->getReconnectsToAttempt()
            || ldap_errno($resource) !== -1
        ) {
            $this->reconnectsAttempted = $this->reconnectCount;
            $this->reconnectCount      = 0;
            return false;
        }

        $this->reconnectCount++;
        $this->reconnectSleep();

        try {
            $this->connect();
            $this->bind();
            $this->reconnectsAttempted = $this->reconnectCount;
            $this->reconnectCount      = 0;
            return true;
        } catch (Exception\LdapException $e) {
            if ($e->getCode() !== -1) {
                return false;
            }
        }
        return $this->shouldReconnect($this->getResource());
    }

    protected function reconnectSleep()
    {
        $duration = min((pow(2, min($this->reconnectCount - 1, 0)) - 1) / 4, 10);
        usleep($duration * 1000000);
    }

    
    public function search(
        $filter,
        $basedn = null,
        $scope = self::SEARCH_SCOPE_SUB,
        array $attributes = [],
        $sort = null,
        $collectionClass = null,
        $sizelimit = 0,
        $timelimit = 0
    ) {
        if (is_array($filter)) {
            $options = array_change_key_case($filter, CASE_LOWER);
            foreach ($options as $key => $value) {
                switch ($key) {
                    case 'filter':
                    case 'basedn':
                    case 'scope':
                    case 'sort':
                        $$key = $value;
                        break;
                    case 'attributes':
                        if (is_array($value)) {
                            $attributes = $value;
                        }
                        break;
                    case 'collectionclass':
                        $collectionClass = $value;
                        break;
                    case 'sizelimit':
                    case 'timelimit':
                        $$key = (int) $value;
                        break;
                }
            }
        }

        if ($basedn === null) {
            $basedn = $this->getBaseDn();
        } elseif ($basedn instanceof Dn) {
            $basedn = $basedn->toString();
        }

        if ($filter instanceof Filter\AbstractFilter) {
            $filter = $filter->toString();
        }

        do {
            $resource = $this->getResource();
            ErrorHandler::start(E_WARNING);

            switch ($scope) {
                case self::SEARCH_SCOPE_ONE:
                    $search = ldap_list($resource, $basedn, $filter, $attributes, 0, $sizelimit, $timelimit);
                    break;
                case self::SEARCH_SCOPE_BASE:
                    $search = ldap_read($resource, $basedn, $filter, $attributes, 0, $sizelimit, $timelimit);
                    break;
                case self::SEARCH_SCOPE_SUB:
                default:
                    $search = ldap_search($resource, $basedn, $filter, $attributes, 0, $sizelimit, $timelimit);
                    break;
            }
            ErrorHandler::stop();
        } while ($search === false && $this->shouldReconnect($resource));

        if ($search === false || is_array($search)) {
            
            throw new Exception\LdapException($this, 'searching: ' . $filter);
        }

        $iterator = new Collection\DefaultIterator($this, $search);

        if ($sort !== null && is_string($sort)) {
            $iterator->sort($sort);
        }

        return $this->createCollection($iterator, $collectionClass);
    }

    
    protected function createCollection(Collection\DefaultIterator $iterator, $collectionClass)
    {
        if ($collectionClass === null) {
            return new Collection($iterator);
        } else {
            $collectionClass = (string) $collectionClass;
            if (! class_exists($collectionClass)) {
                throw new Exception\LdapException(
                    null,
                    "Class '$collectionClass' can not be found"
                );
            }
            if (! is_subclass_of($collectionClass, Collection::class)) {
                throw new Exception\LdapException(
                    null,
                    "Class '$collectionClass' must subclass 'Laminas\\Ldap\\Collection'"
                );
            }

            return new $collectionClass($iterator);
        }
    }

    
    public function count($filter, $basedn = null, $scope = self::SEARCH_SCOPE_SUB)
    {
        try {
            $result = $this->search($filter, $basedn, $scope, ['dn'], null);
        } catch (Exception\LdapException $e) {
            if ($e->getCode() === Exception\LdapException::LDAP_NO_SUCH_OBJECT) {
                return 0;
            }
            throw $e;
        }

        return $result->count();
    }

    
    public function countChildren($dn)
    {
        return $this->count('(objectClass=*)', $dn, self::SEARCH_SCOPE_ONE);
    }

    
    public function exists($dn)
    {
        return $this->count('(objectClass=*)', $dn, self::SEARCH_SCOPE_BASE) === 1;
    }

    
    public function searchEntries(
        $filter,
        $basedn = null,
        $scope = self::SEARCH_SCOPE_SUB,
        array $attributes = [],
        $sort = null,
        $reverseSort = false,
        $sizelimit = 0,
        $timelimit = 0
    ) {
        if (is_array($filter)) {
            $filter = array_change_key_case($filter, CASE_LOWER);
            if (isset($filter['collectionclass'])) {
                unset($filter['collectionclass']);
            }
            if (isset($filter['reversesort'])) {
                $reverseSort = $filter['reversesort'];
                unset($filter['reversesort']);
            }
        }
        $result = $this->search($filter, $basedn, $scope, $attributes, $sort, null, $sizelimit, $timelimit);
        $items  = $result->toArray();
        if ((bool) $reverseSort === true) {
            $items = array_reverse($items, false);
        }

        return $items;
    }

    
    public function getEntry($dn, array $attributes = [], $throwOnNotFound = false)
    {
        try {
            $result = $this->search(
                "(objectClass=*)",
                $dn,
                self::SEARCH_SCOPE_BASE,
                $attributes,
                null
            );

            return $result->getFirst();
        } catch (Exception\LdapException $e) {
            if ($throwOnNotFound !== false) {
                throw $e;
            }
        }

        return null;
    }

    
    public static function prepareLdapEntryArray(array &$entry)
    {
        if (array_key_exists('dn', $entry)) {
            unset($entry['dn']);
        }
        foreach ($entry as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    if ($v === null) {
                        unset($value[$i]);
                    } elseif (! is_scalar($v)) {
                        throw new Exception\InvalidArgumentException('Only scalar values allowed in LDAP data');
                    } else {
                        $v = (string) $v;
                        if (strlen($v) === 0) {
                            unset($value[$i]);
                        } else {
                            $value[$i] = $v;
                        }
                    }
                }
                $entry[$key] = array_values($value);
            } else {
                if ($value === null) {
                    $entry[$key] = [];
                } elseif (! is_scalar($value)) {
                    throw new Exception\InvalidArgumentException('Only scalar values allowed in LDAP data');
                } else {
                    $value = (string) $value;
                    if (strlen($value) === 0) {
                        $entry[$key] = [];
                    } else {
                        $entry[$key] = [$value];
                    }
                }
            }
        }
        $entry = array_change_key_case($entry, CASE_LOWER);
    }

    
    public function add($dn, array $entry)
    {
        if (! $dn instanceof Dn) {
            $dn = Dn::factory($dn, null);
        }
        static::prepareLdapEntryArray($entry);
        foreach ($entry as $key => $value) {
            if (is_array($value) && count($value) === 0) {
                unset($entry[$key]);
            }
        }

        $rdnParts = $dn->getRdn(Dn::ATTR_CASEFOLD_LOWER);
        foreach ($rdnParts as $key => $value) {
            $value = Dn::unescapeValue($value);
            if (! array_key_exists($key, $entry)) {
                $entry[$key] = [$value];
            } elseif (! in_array($value, $entry[$key])) {
                $entry[$key] = array_merge([$value], $entry[$key]);
            }
        }
        $adAttributes = [
            'distinguishedname',
            'instancetype',
            'name',
            'objectcategory',
            'objectguid',
            'usnchanged',
            'usncreated',
            'whenchanged',
            'whencreated',
        ];
        foreach ($adAttributes as $attr) {
            if (array_key_exists($attr, $entry)) {
                unset($entry[$attr]);
            }
        }

        do {
            $resource = $this->getResource();
            ErrorHandler::start(E_WARNING);
            $isAdded = ldap_add($resource, $dn->toString(), $entry);
            ErrorHandler::stop();
        } while ($isAdded === false && $this->shouldReconnect($resource));

        if ($isAdded === false) {
            throw new Exception\LdapException($this, 'adding: ' . $dn->toString());
        }

        return $this;
    }

    
    public function update($dn, array $entry)
    {
        if (! $dn instanceof Dn) {
            $dn = Dn::factory($dn, null);
        }
        static::prepareLdapEntryArray($entry);

        $rdnParts = $dn->getRdn(Dn::ATTR_CASEFOLD_LOWER);
        foreach ($rdnParts as $key => $value) {
            $value = Dn::unescapeValue($value);
            if (array_key_exists($key, $entry) && ! in_array($value, $entry[$key])) {
                $entry[$key] = array_merge([$value], $entry[$key]);
            }
        }
        $adAttributes = [
            'distinguishedname',
            'instancetype',
            'name',
            'objectcategory',
            'objectguid',
            'usnchanged',
            'usncreated',
            'whenchanged',
            'whencreated',
        ];
        foreach ($adAttributes as $attr) {
            if (array_key_exists($attr, $entry)) {
                unset($entry[$attr]);
            }
        }

        if (count($entry) > 0) {
            do {
                $resource = $this->getResource();
                ErrorHandler::start(E_WARNING);
                $isModified = ldap_modify($resource, $dn->toString(), $entry);
                ErrorHandler::stop();
            } while ($isModified === false && $this->shouldReconnect($resource));

            if ($isModified === false) {
                throw new Exception\LdapException($this, 'updating: ' . $dn->toString());
            }
        }

        return $this;
    }

    
    public function save($dn, array $entry)
    {
        if ($dn instanceof Dn) {
            $dn = $dn->toString();
        }
        if ($this->exists($dn)) {
            $this->update($dn, $entry);
        } else {
            $this->add($dn, $entry);
        }

        return $this;
    }

    
    public function delete($dn, $recursively = false)
    {
        if ($dn instanceof Dn) {
            $dn = $dn->toString();
        }
        if ($recursively === true) {
            if ($this->countChildren($dn) > 0) {
                $children = $this->getChildrenDns($dn);
                foreach ($children as $c) {
                    $this->delete($c, true);
                }
            }
        }

        do {
            $resource = $this->getResource();
            ErrorHandler::start(E_WARNING);
            $isDeleted = ldap_delete($resource, $dn);
            ErrorHandler::stop();
        } while ($isDeleted === false && $this->shouldReconnect($resource));

        if ($isDeleted === false) {
            throw new Exception\LdapException($this, 'deleting: ' . $dn);
        }

        return $this;
    }

    
    public function addAttributes($dn, array $attributes, $allowEmptyAttributes = false)
    {
        
        
        if ($allowEmptyAttributes !== true) {
            foreach ($attributes as $key => $value) {
                if (empty($value)) {
                    unset($attributes[$key]);
                }
            }
        }

        if ($dn instanceof Dn) {
            $dn = $dn->toString();
        }

        do {
            ErrorHandler::start(E_WARNING);
            $entryAdded = ldap_mod_add($this->resource, $dn, $attributes);
            ErrorHandler::stop();
        } while ($entryAdded === false && $this->shouldReconnect($this->resource));

        if ($entryAdded === false) {
            throw new Exception\LdapException($this, 'adding attribute: ' . $dn);
        }

        return $this;
    }

    
    public function updateAttributes($dn, array $attributes, $allowEmptyAttributes = false)
    {
        
        
        if ($allowEmptyAttributes !== true) {
            foreach ($attributes as $key => $value) {
                if (empty($value)) {
                    unset($attributes[$key]);
                }
            }
        }

        if ($dn instanceof Dn) {
            $dn = $dn->toString();
        }

        do {
            ErrorHandler::start(E_WARNING);
            $entryUpdated = ldap_mod_replace($this->resource, $dn, $attributes);
            ErrorHandler::stop();
        } while ($entryUpdated === false && $this->shouldReconnect($this->resource));

        if ($entryUpdated === false) {
            throw new Exception\LdapException($this, 'updating attribute: ' . $dn);
        }

        return $this;
    }

    
    public function deleteAttributes($dn, array $attributes, $allowEmptyAttributes = false)
    {
        
        
        if ($allowEmptyAttributes !== true) {
            foreach ($attributes as $key => $value) {
                if (empty($value)) {
                    unset($attributes[$key]);
                }
            }
        }

        if ($dn instanceof Dn) {
            $dn = $dn->toString();
        }

        do {
            ErrorHandler::start(E_WARNING);
            $isDeleted = ldap_mod_del($this->resource, $dn, $attributes);
            ErrorHandler::stop();
        } while ($isDeleted === false && $this->shouldReconnect($this->resource));

        if ($isDeleted === false) {
            throw new Exception\LdapException($this, 'deleting: ' . $dn);
        }

        return $this;
    }

    
    protected function getChildrenDns($parentDn)
    {
        if ($parentDn instanceof Dn) {
            $parentDn = $parentDn->toString();
        }
        $children = [];

        do {
            $resource = $this->getResource();
            ErrorHandler::start(E_WARNING);
            $search = ldap_list($resource, $parentDn, '(objectClass=*)', ['dn']);
            ErrorHandler::stop();
        } while ($search === false && $this->shouldReconnect($resource));

        if ($search === false || is_array($search)) {
            
            throw new Exception\LdapException($this, 'listing: ' . $parentDn);
        }

        ErrorHandler::start(E_WARNING);
        for (
            $entry = ldap_first_entry($resource, $search);
            $entry !== false;
            $entry = ldap_next_entry($resource, $entry)
        ) {
            $childDn = ldap_get_dn($resource, $entry);
            if ($childDn === false) {
                ErrorHandler::stop();
                throw new Exception\LdapException($this, 'getting dn');
            }
            $children[] = $childDn;
        }
        ldap_free_result($search);
        ErrorHandler::stop();

        return $children;
    }

    
    public function moveToSubtree($from, $to, $recursively = false, $alwaysEmulate = false)
    {
        if ($from instanceof Dn) {
            $orgDnParts = $from->toArray();
        } else {
            $orgDnParts = Dn::explodeDn($from);
        }

        if ($to instanceof Dn) {
            $newParentDnParts = $to->toArray();
        } else {
            $newParentDnParts = Dn::explodeDn($to);
        }

        $newDnParts = array_merge([array_shift($orgDnParts)], $newParentDnParts);
        $newDn      = Dn::fromArray($newDnParts);

        return $this->rename($from, $newDn, $recursively, $alwaysEmulate);
    }

    
    public function move($from, $to, $recursively = false, $alwaysEmulate = false)
    {
        return $this->rename($from, $to, $recursively, $alwaysEmulate);
    }

    
    public function rename($from, $to, $recursively = false, $alwaysEmulate = false)
    {
        $emulate = (bool) $alwaysEmulate;
        if (! function_exists('ldap_rename')) {
            $emulate = true;
        } elseif ($recursively) {
            $emulate = true;
        }

        if ($emulate === false) {
            if ($from instanceof Dn) {
                $from = $from->toString();
            }

            if ($to instanceof Dn) {
                $newDnParts = $to->toArray();
            } else {
                $newDnParts = Dn::explodeDn($to);
            }

            $newRdn    = Dn::implodeRdn(array_shift($newDnParts));
            $newParent = Dn::implodeDn($newDnParts);

            do {
                $resource = $this->getResource();
                ErrorHandler::start(E_WARNING);
                $isOK = ldap_rename($resource, $from, $newRdn, $newParent, true);
                ErrorHandler::stop();
            } while ($isOK === false && $this->shouldReconnect($resource));

            if ($isOK === false) {
                throw new Exception\LdapException($this, 'renaming ' . $from . ' to ' . $to);
            } elseif (! $this->exists($to)) {
                $emulate = true;
            }
        }
        if ($emulate) {
            $this->copy($from, $to, $recursively);
            $this->delete($from, $recursively);
        }

        return $this;
    }

    
    public function copyToSubtree($from, $to, $recursively = false)
    {
        if ($from instanceof Dn) {
            $orgDnParts = $from->toArray();
        } else {
            $orgDnParts = Dn::explodeDn($from);
        }

        if ($to instanceof Dn) {
            $newParentDnParts = $to->toArray();
        } else {
            $newParentDnParts = Dn::explodeDn($to);
        }

        $newDnParts = array_merge([array_shift($orgDnParts)], $newParentDnParts);
        $newDn      = Dn::fromArray($newDnParts);

        return $this->copy($from, $newDn, $recursively);
    }

    
    public function copy($from, $to, $recursively = false)
    {
        $entry = $this->getEntry($from, [], true);

        if ($to instanceof Dn) {
            $toDnParts = $to->toArray();
        } else {
            $toDnParts = Dn::explodeDn($to);
        }
        $this->add($to, $entry);

        if ($recursively === true && $this->countChildren($from) > 0) {
            $children = $this->getChildrenDns($from);
            foreach ($children as $c) {
                $cDnParts      = Dn::explodeDn($c);
                $newChildParts = array_merge([array_shift($cDnParts)], $toDnParts);
                $newChild      = Dn::implodeDn($newChildParts);
                $this->copy($c, $newChild, true);
            }
        }

        return $this;
    }

    
    public function getNode($dn)
    {
        return Node::fromLdap($dn, $this);
    }

    
    public function getBaseNode()
    {
        $baseNode = $this->getNode($this->getBaseDn());

        assert($baseNode instanceof Node);

        return $baseNode;
    }

    
    public function getRootDse()
    {
        if ($this->rootDse === null) {
            $this->rootDse = Node\RootDse::create($this);
        }

        return $this->rootDse;
    }

    
    public function getSchema()
    {
        if ($this->schema === null) {
            $this->schema = Node\Schema::create($this);
        }

        return $this->schema;
    }
}
