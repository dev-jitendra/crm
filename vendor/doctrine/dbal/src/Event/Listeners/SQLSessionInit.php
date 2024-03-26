<?php

namespace Doctrine\DBAL\Event\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;


class SQLSessionInit implements EventSubscriber
{
    
    protected $sql;

    
    public function __construct($sql)
    {
        $this->sql = $sql;
    }

    
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()->executeStatement($this->sql);
    }

    
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }
}
