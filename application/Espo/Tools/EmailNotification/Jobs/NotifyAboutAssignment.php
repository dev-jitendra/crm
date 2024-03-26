<?php


namespace Espo\Tools\EmailNotification\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;

use Espo\Tools\EmailNotification\AssignmentProcessor;
use Espo\Tools\EmailNotification\AssignmentProcessorData;

class NotifyAboutAssignment implements Job
{
    public function __construct(private AssignmentProcessor $assignmentProcessor)
    {}

    public function run(Data $data): void
    {
        $this->assignmentProcessor->process(
            AssignmentProcessorData::create()
                ->withAssignerUserId($data->get('assignerUserId'))
                ->withEntityId($data->get('entityId'))
                ->withEntityType($data->get('entityType'))
                ->withUserId($data->get('userId'))
        );
    }
}
