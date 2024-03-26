<?php
namespace Ratchet\Wamp;
use Ratchet\ConnectionInterface;


class Topic implements \IteratorAggregate, \Countable {
    private $id;

    private $subscribers;

    
    public function __construct($topicId) {
        $this->id = $topicId;
        $this->subscribers = new \SplObjectStorage;
    }

    
    public function getId() {
        return $this->id;
    }

    public function __toString() {
        return $this->getId();
    }

    
    public function broadcast($msg, array $exclude = array(), array $eligible = array()) {
        $useEligible = (bool)count($eligible);
        foreach ($this->subscribers as $client) {
            if (in_array($client->WAMP->sessionId, $exclude)) {
                continue;
            }

            if ($useEligible && !in_array($client->WAMP->sessionId, $eligible)) {
                continue;
            }

            $client->event($this->id, $msg);
        }

        return $this;
    }

    
    public function has(ConnectionInterface $conn) {
        return $this->subscribers->contains($conn);
    }

    
    public function add(ConnectionInterface $conn) {
        $this->subscribers->attach($conn);

        return $this;
    }

    
    public function remove(ConnectionInterface $conn) {
        if ($this->subscribers->contains($conn)) {
            $this->subscribers->detach($conn);
        }

        return $this;
    }

    
    #[\ReturnTypeWillChange]
    public function getIterator() {
        return $this->subscribers;
    }

    
    #[\ReturnTypeWillChange]
    public function count() {
        return $this->subscribers->count();
    }
}
