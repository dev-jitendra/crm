<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner\Params;
use Espo\Core\Application\RunnerParameterized;
use Espo\Core\Job\JobManager;


class Job implements RunnerParameterized
{
    use Cli;
    use SetupSystemUser;

    public function __construct(private JobManager $jobManager)
    {}

    public function run(Params $params): void
    {
        $id = $params->get('id');

        $this->jobManager->runJobById($id);
    }
}
