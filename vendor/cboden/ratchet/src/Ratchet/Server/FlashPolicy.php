<?php
namespace Ratchet\Server;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class FlashPolicy implements MessageComponentInterface {

    
    protected $_policy = '<?xml version="1.0"?><!DOCTYPE cross-domain-policy SYSTEM "http:

    
    protected $_access = array();

    
    protected $_siteControl = '';

    
    protected $_cache = '';

    
    protected $_cacheValid = false;

    
    public function addAllowedAccess($domain, $ports = '*', $secure = false) {
        if (!$this->validateDomain($domain)) {
           throw new \UnexpectedValueException('Invalid domain');
        }

        if (!$this->validatePorts($ports)) {
           throw new \UnexpectedValueException('Invalid Port');
        }

        $this->_access[]   = array($domain, $ports, (boolean)$secure);
        $this->_cacheValid = false;

        return $this;
    }
    
    
    public function clearAllowedAccess() {
        $this->_access      = array();
        $this->_cacheValid = false;

        return $this;
    }

    
    public function setSiteControl($permittedCrossDomainPolicies = 'all') {
        if (!$this->validateSiteControl($permittedCrossDomainPolicies)) {
            throw new \UnexpectedValueException('Invalid site control set');
        }

        $this->_siteControl = $permittedCrossDomainPolicies;
        $this->_cacheValid  = false;

        return $this;
    }

    
    public function onOpen(ConnectionInterface $conn) {
    }

    
    public function onMessage(ConnectionInterface $from, $msg) {
        if (!$this->_cacheValid) {
            $this->_cache      = $this->renderPolicy()->asXML();
            $this->_cacheValid = true;
        }

        $from->send($this->_cache . "\0");
        $from->close();
    }

    
    public function onClose(ConnectionInterface $conn) {
    }

    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    
    public function renderPolicy() {
        $policy = new \SimpleXMLElement($this->_policy);

        $siteControl = $policy->addChild('site-control');

        if ($this->_siteControl == '') {
            $this->setSiteControl();
        }

        $siteControl->addAttribute('permitted-cross-domain-policies', $this->_siteControl);

        if (empty($this->_access)) {
            throw new \UnexpectedValueException('You must add a domain through addAllowedAccess()');
        }

        foreach ($this->_access as $access) {
            $tmp = $policy->addChild('allow-access-from');
            $tmp->addAttribute('domain', $access[0]);
            $tmp->addAttribute('to-ports', $access[1]);
            $tmp->addAttribute('secure', ($access[2] === true) ? 'true' : 'false');
        }

        return $policy;
    }

    
    public function validateSiteControl($permittedCrossDomainPolicies) {
        
        return (bool)in_array($permittedCrossDomainPolicies, array('none', 'master-only', 'all'));
    }

    
    public function validateDomain($domain) {
        return (bool)preg_match("/^((http(s)?:\/\/)?([a-z0-9-_]+\.|\*\.)*([a-z0-9-_\.]+)|\*)$/i", $domain);
    }

    
    public function validatePorts($port) {
        return (bool)preg_match('/^(\*|(\d+[,-]?)*\d+)$/', $port);
    }
}
