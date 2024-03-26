<?php


namespace Espo\Entities;

use Espo\Core\Job\Job as JobJob;
use Espo\Core\Job\Job\Status;
use Espo\Core\Job\JobDataLess;
use Espo\Core\ORM\Entity;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use stdClass;

class Job extends Entity
{
    public const ENTITY_TYPE = 'Job';

    
    public function getStatus(): string
    {
        return $this->get('status');
    }

    
    public function getJob(): ?string
    {
        return $this->get('job');
    }

    
    public function getScheduledJobJob(): ?string
    {
        return $this->get('scheduledJobJob');
    }

    
    public function getTargetType(): ?string
    {
        return $this->get('targetType');
    }

    
    public function getTargetId(): ?string
    {
        return $this->get('targetId');
    }

    
    public function getTargetGroup(): ?string
    {
        return $this->get('targetGroup');
    }

    
    public function getGroup(): ?string
    {
        return $this->get('group');
    }

    
    public function getQueue(): ?string
    {
        return $this->get('queue');
    }

    
    public function getData(): stdClass
    {
        return $this->get('data') ?? (object) [];
    }

    
    public function getClassName(): ?string
    {
        return $this->get('className');
    }

    
    public function getServiceName(): ?string
    {
        return $this->get('serviceName');
    }

    
    public function getMethodName(): ?string
    {
        return $this->get('methodName');
    }

    
    public function getScheduledJobId(): ?string
    {
        return $this->get('scheduledJobId');
    }

    
    public function getStartedAt(): ?string
    {
        return $this->get('startedAt');
    }

    
    public function getPid(): ?int
    {
        return $this->get('pid');
    }

    
    public function getAttempts(): int
    {
        return $this->get('attempts') ?? 0;
    }

    
    public function getFailedAttempts(): int
    {
        return $this->get('failedAttempts') ?? 0;
    }

    
    public function setStatus(string $status): self
    {
        $this->set('status', $status);

        return $this;
    }

    
    public function setPid(?int $pid): self
    {
        $this->set('pid', $pid);

        return $this;
    }

    
    public function setStartedAtNow(): self
    {
        $this->set('startedAt', DateTimeUtil::getSystemNowString());

        return $this;
    }

    
    public function setExecutedAtNow(): self
    {
        $this->set('executedAt', DateTimeUtil::getSystemNowString());

        return $this;
    }
}
