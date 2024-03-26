<?php


namespace Espo\Core\Job;

use Espo\Core\Utils\Metadata;

class MetadataProvider
{
    public function __construct(private Metadata $metadata)
    {}

    
    public function getPreparableJobNameList(): array
    {
        $list = [];

        $items = $this->metadata->get(['app', 'scheduledJobs']) ?? [];

        foreach ($items as $name => $item) {
            $isPreparable = (bool) ($item['preparatorClassName'] ?? null);

            if ($isPreparable) {
                $list[] = $name;
            }
        }

        return $list;
    }

    public function isJobSystem(string $name): bool
    {
        return (bool) $this->metadata->get(['app', 'scheduledJobs', $name, 'isSystem']);
    }

    public function isJobPreparable(string $name): bool
    {
        return (bool) $this->metadata->get(['app', 'scheduledJobs', $name, 'preparatorClassName']);
    }

    public function getPreparatorClassName(string $name): ?string
    {
        return $this->metadata->get(['app', 'scheduledJobs', $name, 'preparatorClassName']);
    }

    public function getJobClassName(string $name): ?string
    {
        return $this->metadata->get(['app', 'scheduledJobs', $name, 'jobClassName']);
    }

    
    public function getScheduledJobNameList(): array
    {
        
        $items = $this->metadata->get(['app', 'scheduledJobs']) ?? [];

        return array_keys($items);
    }

    
    public function getNonSystemScheduledJobNameList(): array
    {
        return array_filter(
            $this->getScheduledJobNameList(),
            function (string $item) {
                $isSystem = (bool) $this->metadata->get(['app', 'scheduledJobs', $item, 'isSystem']);

                return !$isSystem;
            }
        );
    }
}
