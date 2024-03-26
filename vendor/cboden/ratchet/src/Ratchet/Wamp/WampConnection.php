<?php
namespace Ratchet\Wamp;
use Ratchet\ConnectionInterface;
use Ratchet\AbstractConnectionDecorator;
use Ratchet\Wamp\ServerProtocol as WAMP;


class WampConnection extends AbstractConnectionDecorator {
    
    public function __construct(ConnectionInterface $conn) {
        parent::__construct($conn);

        $this->WAMP            = new \StdClass;
        $this->WAMP->sessionId = str_replace('.', '', uniqid(mt_rand(), true));
        $this->WAMP->prefixes  = array();

        $this->send(json_encode(array(WAMP::MSG_WELCOME, $this->WAMP->sessionId, 1, \Ratchet\VERSION)));
    }

    
    public function callResult($id, $data = array()) {
        return $this->send(json_encode(array(WAMP::MSG_CALL_RESULT, $id, $data)));
    }

    
    public function callError($id, $errorUri, $desc = '', $details = null) {
        if ($errorUri instanceof Topic) {
            $errorUri = (string)$errorUri;
        }

        $data = array(WAMP::MSG_CALL_ERROR, $id, $errorUri, $desc);

        if (null !== $details) {
            $data[] = $details;
        }

        return $this->send(json_encode($data));
    }

    
    public function event($topic, $msg) {
        return $this->send(json_encode(array(WAMP::MSG_EVENT, (string)$topic, $msg)));
    }

    
    public function prefix($curie, $uri) {
        $this->WAMP->prefixes[$curie] = (string)$uri;

        return $this->send(json_encode(array(WAMP::MSG_PREFIX, $curie, (string)$uri)));
    }

    
    public function getUri($uri) {
        $curieSeperator = ':';

        if (preg_match('/http(s*)\:\/\
            if (strpos($uri, $curieSeperator) !== false) {
                list($prefix, $action) = explode($curieSeperator, $uri);
                
                if(isset($this->WAMP->prefixes[$prefix]) === true){
                  return $this->WAMP->prefixes[$prefix] . '#' . $action;
                }
            }
        }

        return $uri;
    }

    
    public function send($data) {
        $this->getConnection()->send($data);

        return $this;
    }

    
    public function close($opt = null) {
        $this->getConnection()->close($opt);
    }

}
