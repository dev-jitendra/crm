<?php

namespace Doctrine\Common;

use function spl_object_hash;


class EventManager
{
    
    private $_listeners = [];

    
    public function dispatchEvent($eventName, ?EventArgs $eventArgs = null)
    {
        if (! isset($this->_listeners[$eventName])) {
            return;
        }

        $eventArgs = $eventArgs ?? EventArgs::getEmptyInstance();

        foreach ($this->_listeners[$eventName] as $listener) {
            $listener->$eventName($eventArgs);
        }
    }

    
    public function getListeners($event = null)
    {
        return $event ? $this->_listeners[$event] : $this->_listeners;
    }

    
    public function hasListeners($event)
    {
        return ! empty($this->_listeners[$event]);
    }

    
    public function addEventListener($events, $listener)
    {
        
        $hash = spl_object_hash($listener);

        foreach ((array) $events as $event) {
            
            
            $this->_listeners[$event][$hash] = $listener;
        }
    }

    
    public function removeEventListener($events, $listener)
    {
        
        $hash = spl_object_hash($listener);

        foreach ((array) $events as $event) {
            unset($this->_listeners[$event][$hash]);
        }
    }

    
    public function addEventSubscriber(EventSubscriber $subscriber)
    {
        $this->addEventListener($subscriber->getSubscribedEvents(), $subscriber);
    }

    
    public function removeEventSubscriber(EventSubscriber $subscriber)
    {
        $this->removeEventListener($subscriber->getSubscribedEvents(), $subscriber);
    }
}
