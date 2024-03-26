<?php

namespace Doctrine\DBAL\Event\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;


class SQLiteSessionInit implements EventSubscriber
{
    
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()->executeStatement('PRAGMA foreign_keys=ON');
    }

    
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }
}
