<?php


namespace Espo\Core\Acl;


interface AccessEntityCREDChecker extends

    AccessEntityCreateChecker,
    AccessEntityReadChecker,
    AccessEntityEditChecker,
    AccessEntityDeleteChecker
{

}
