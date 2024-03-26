<?php
namespace Ratchet\Wamp;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsServerInterface;

class TopicManager implements WsServerInterface, WampServerInterface {
    
    protected $app;

    
    protected $topicLookup = array();

    public function __construct(WampServerInterface $app) {
        $this->app = $app;
    }

    
    public function onOpen(ConnectionInterface $conn) {
        $conn->WAMP->subscriptions = new \SplObjectStorage;
        $this->app->onOpen($conn);
    }

    
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        $this->app->onCall($conn, $id, $this->getTopic($topic), $params);
    }

    
    public function onSubscribe(ConnectionInterface $conn, $topic) {
        $topicObj = $this->getTopic($topic);

        if ($conn->WAMP->subscriptions->contains($topicObj)) {
            return;
        }

        $this->topicLookup[$topic]->add($conn);
        $conn->WAMP->subscriptions->attach($topicObj);
        $this->app->onSubscribe($conn, $topicObj);
    }

    
    public function onUnsubscribe(ConnectionInterface $conn, $topic) {
        $topicObj = $this->getTopic($topic);

        if (!$conn->WAMP->subscriptions->contains($topicObj)) {
            return;
        }

        $this->cleanTopic($topicObj, $conn);

        $this->app->onUnsubscribe($conn, $topicObj);
    }

    
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        $this->app->onPublish($conn, $this->getTopic($topic), $event, $exclude, $eligible);
    }

    
    public function onClose(ConnectionInterface $conn) {
        $this->app->onClose($conn);

        foreach ($this->topicLookup as $topic) {
            $this->cleanTopic($topic, $conn);
        }
    }

    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->app->onError($conn, $e);
    }

    
    public function getSubProtocols() {
        if ($this->app instanceof WsServerInterface) {
            return $this->app->getSubProtocols();
        }

        return array();
    }

    
    protected function getTopic($topic) {
        if (!array_key_exists($topic, $this->topicLookup)) {
            $this->topicLookup[$topic] = new Topic($topic);
        }

        return $this->topicLookup[$topic];
    }

    protected function cleanTopic(Topic $topic, ConnectionInterface $conn) {
        if ($conn->WAMP->subscriptions->contains($topic)) {
            $conn->WAMP->subscriptions->detach($topic);
        }

        $this->topicLookup[$topic->getId()]->remove($conn);

        if (0 === $topic->count()) {
            unset($this->topicLookup[$topic->getId()]);
        }
    }
}
