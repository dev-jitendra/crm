<?php

namespace Doctrine\DBAL\Event\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;

use function array_change_key_case;
use function array_merge;
use function count;
use function implode;

use const CASE_UPPER;


class OracleSessionInit implements EventSubscriber
{
    
    protected $_defaultSessionVars = [
        'NLS_TIME_FORMAT' => 'HH24:MI:SS',
        'NLS_DATE_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
        'NLS_TIMESTAMP_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
        'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
        'NLS_NUMERIC_CHARACTERS' => '.,',
    ];

    
    public function __construct(array $oracleSessionVars = [])
    {
        $this->_defaultSessionVars = array_merge($this->_defaultSessionVars, $oracleSessionVars);
    }

    
    public function postConnect(ConnectionEventArgs $args)
    {
        if (count($this->_defaultSessionVars) === 0) {
            return;
        }

        $vars = [];
        foreach (array_change_key_case($this->_defaultSessionVars, CASE_UPPER) as $option => $value) {
            if ($option === 'CURRENT_SCHEMA') {
                $vars[] = $option . ' = ' . $value;
            } else {
                $vars[] = $option . " = '" . $value . "'";
            }
        }

        $sql = 'ALTER SESSION SET ' . implode(' ', $vars);
        $args->getConnection()->executeStatement($sql);
    }

    
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }
}
