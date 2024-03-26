<?php


namespace Espo\Core\Di;

use Espo\Core\Acl\AssignmentChecker\AssignmentCheckerManager;

trait AssignmentCheckerManagerSetter
{
    
    protected $assignmentCheckerManager;

    public function setAssignmentCheckerManager(AssignmentCheckerManager $assignmentCheckerManager): void
    {
        $this->assignmentCheckerManager = $assignmentCheckerManager;
    }
}
