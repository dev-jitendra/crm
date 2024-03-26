<?php

namespace Doctrine\DBAL\Driver;


interface ServerInfoAwareConnection extends Connection
{
    
    public function getServerVersion();
}
