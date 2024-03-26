<?php


namespace Espo\Core\Job;

use Spatie\Async\Task as AsyncTask;

use Espo\Core\Application;
use Espo\Core\Application\Runner\Params as RunnerParams;
use Espo\Core\ApplicationRunners\Job as JobRunner;
use Espo\Core\Utils\Log;

use Throwable;

class JobTask extends AsyncTask
{
    private string $jobId;

    public function __construct(string $jobId)
    {
        $this->jobId = $jobId;
    }

    
    public function configure()
    {}

    
    public function run()
    {
        $app = new Application();

        $params = RunnerParams::create()->with('id', $this->jobId);

        try {
            $app->run(JobRunner::class, $params);
        }
        catch (Throwable $e) {
            $log = $app->getContainer()->getByClass(Log::class);

            $log->error("JobTask: Failed to run job '{$this->jobId}'. Error: " . $e->getMessage());
        }
    }
}
