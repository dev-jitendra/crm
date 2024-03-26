<?php
declare(strict_types=1);

namespace BugHonorFileTimeTest;

use DateTime;
use PHPUnit\Framework\TestCase;
use ZipStream\Option\{
    Archive,
    File
};
use ZipStream\ZipStream;

use function fopen;


class BugHonorFileTimeTest extends TestCase
{
    public function testHonorsFileTime(): void
    {
        $archiveOpt = new Archive();
        $fileOpt = new File();
        $expectedTime = new DateTime('2019-04-21T19:25:00-0800');

        $archiveOpt->setOutputStream(fopen('php:
        $fileOpt->setTime(clone $expectedTime);

        $zip = new ZipStream(null, $archiveOpt);

        $zip->addFile('sample.txt', 'Sample', $fileOpt);

        $zip->finish();

        $this->assertEquals($expectedTime, $fileOpt->getTime());
    }
}
