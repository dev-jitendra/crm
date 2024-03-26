<?php


namespace Espo\Core\Acl;


interface AccessEntityCREDSChecker extends
    AccessEntityCreateChecker,
    AccessEntityReadChecker,
    AccessEntityEditChecker,
    AccessEntityDeleteChecker,
    AccessEntityStreamChecker
{}
