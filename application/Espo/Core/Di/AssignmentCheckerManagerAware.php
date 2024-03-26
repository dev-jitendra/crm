<?php


namespace Espo\Core\Di;

use Espo\Core\Acl\AssignmentChecker\AssignmentCheckerManager;

interface AssignmentCheckerManagerAware
{
    public function setAssignmentCheckerManager(AssignmentCheckerManager $assignmentCheckerManager): void;
}
