<?php


namespace Espo\Classes\AppInfo;

use Espo\Core\Console\Command\Params;
use Espo\Core\Utils\ClassFinder;
use Espo\Core\Job\MetadataProvider;

class Jobs
{
    private $classFinder;

    private $metadataProvider;

    public function __construct(ClassFinder $classFinder, MetadataProvider $metadataProvider)
    {
        $this->classFinder = $classFinder;
        $this->metadataProvider = $metadataProvider;
    }

    public function process(Params $params): string
    {
        $result = "Available jobs:\n\n";

        $list = array_map(
            function ($item) {
                return ' ' . $item;
            },
            array_unique(
                array_merge(
                    array_keys($this->classFinder->getMap('Jobs')),
                    $this->metadataProvider->getScheduledJobNameList()
                )
            )
        );

        asort($list);

        return $result . implode("\n", $list) . "\n";
    }
}
