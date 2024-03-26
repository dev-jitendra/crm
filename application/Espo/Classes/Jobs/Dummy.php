<?php


namespace Espo\Classes\Jobs;

use Espo\Core\Job\JobDataLess;

class Dummy implements JobDataLess
{
    public function run(): void {}
}
