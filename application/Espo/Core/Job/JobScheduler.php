<?php


namespace Espo\Core\Job;

use Espo\ORM\EntityManager;
use Espo\Core\Utils\DateTime;
use Espo\Core\Job\Job\Data;
use Espo\Entities\Job as JobEntity;

use ReflectionClass;
use DateTimeInterface;
use DateTimeImmutable;
use DateInterval;
use RuntimeException;
use TypeError;


class JobScheduler
{
    
    private ?string $className = null;
    private ?string $queue = null;
    private ?string $group = null;
    private ?Data $data = null;
    private ?DateTimeImmutable $time = null;
    private ?DateInterval $delay = null;

    public function __construct(private EntityManager $entityManager)
    {}

    
    public function setClassName(string $className): self
    {
        if (!class_exists($className)) {
            throw new RuntimeException("Class '{$className}' does not exist.");
        }

        $class = new ReflectionClass($className);

        if (
            !$class->implementsInterface(Job::class) &&
            !$class->implementsInterface(JobDataLess::class)
        ) {
            throw new RuntimeException("Class '{$className}' does not implement 'Job' or 'JobDataLess' interface.");
        }

        $this->className = $className;

        return $this;
    }

    
    public function setQueue(?string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    
    public function setGroup(?string $group): self
    {
        $this->group = $group;

        return $this;
    }

    
    public function setTime(?DateTimeInterface $time): self
    {
        $timeCopy = $time;

        if (!is_null($time) && !$time instanceof DateTimeImmutable) {
            $timeCopy = DateTimeImmutable::createFromMutable($time);
        }

        

        $this->time = $timeCopy;

        return $this;
    }

    
    public function setDelay(?DateInterval $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    
    public function setData($data): self
    {
        

        if (!is_null($data) && !is_array($data) && !$data instanceof Data) {
            throw new TypeError();
        }

        if (!$data instanceof Data) {
            $data = Data::create($data);
        }

        $this->data = $data;

        return $this;
    }

    public function schedule(): JobEntity
    {
        if (!$this->className) {
            throw new RuntimeException("Class name is not set.");
        }

        if ($this->group && $this->queue) {
            throw new RuntimeException("Can't have both queue and group.");
        }

        $time = $this->time ?? new DateTimeImmutable();

        if ($this->delay) {
            $time = $time->add($this->delay);
        }

        $data = $this->data ?? Data::create();

        
        return $this->entityManager->createEntity(JobEntity::ENTITY_TYPE, [
            'name' => $this->className,
            'className' => $this->className,
            'queue' => $this->queue,
            'group' => $this->group,
            'targetType' => $data->getTargetType(),
            'targetId' => $data->getTargetId(),
            'data' => $data->getRaw(),
            'executeTime' => $time->format(DateTime::SYSTEM_DATE_TIME_FORMAT),
        ]);
    }
}
