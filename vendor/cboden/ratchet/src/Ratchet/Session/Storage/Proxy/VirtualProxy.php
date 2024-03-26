<?php
namespace Ratchet\Session\Storage\Proxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class VirtualProxy extends SessionHandlerProxy {
    
    protected $_sessionId;

    
    protected $_sessionName;

    
    public function __construct(\SessionHandlerInterface $handler) {
        parent::__construct($handler);

        $this->saveHandlerName = 'user';
        $this->_sessionName    = ini_get('session.name');
    }

    
    public function getId() {
        return $this->_sessionId;
    }

    
    public function setId($id) {
        $this->_sessionId = $id;
    }

    
    public function getName() {
        return $this->_sessionName;
    }

    
    public function setName($name) {
        throw new \RuntimeException("Can not change session name in VirtualProxy");
    }
}
