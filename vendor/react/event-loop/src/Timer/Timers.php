<?php

namespace React\EventLoop\Timer;

use React\EventLoop\TimerInterface;


final class Timers
{
    private $time;
    private $timers = array();
    private $schedule = array();
    private $sorted = true;
    private $useHighResolution;

    public function __construct()
    {
        
        $this->useHighResolution = \function_exists('hrtime');
    }

    public function updateTime()
    {
        return $this->time = $this->useHighResolution ? \hrtime(true) * 1e-9 : \microtime(true);
    }

    public function getTime()
    {
        return $this->time ?: $this->updateTime();
    }

    public function add(TimerInterface $timer)
    {
        $id = \spl_object_hash($timer);
        $this->timers[$id] = $timer;
        $this->schedule[$id] = $timer->getInterval() + $this->updateTime();
        $this->sorted = false;
    }

    public function contains(TimerInterface $timer)
    {
        return isset($this->timers[\spl_object_hash($timer)]);
    }

    public function cancel(TimerInterface $timer)
    {
        $id = \spl_object_hash($timer);
        unset($this->timers[$id], $this->schedule[$id]);
    }

    public function getFirst()
    {
        
        if (!$this->sorted) {
            $this->sorted = true;
            \asort($this->schedule);
        }

        return \reset($this->schedule);
    }

    public function isEmpty()
    {
        return \count($this->timers) === 0;
    }

    public function tick()
    {
        
        if (!$this->schedule) {
            return;
        }

        
        if (!$this->sorted) {
            $this->sorted = true;
            \asort($this->schedule);
        }

        $time = $this->updateTime();

        foreach ($this->schedule as $id => $scheduled) {
            
            if ($scheduled >= $time) {
                break;
            }

            
            if (!isset($this->schedule[$id]) || $this->schedule[$id] !== $scheduled) {
                continue;
            }

            $timer = $this->timers[$id];
            \call_user_func($timer->getCallback(), $timer);

            
            if ($timer->isPeriodic() && isset($this->timers[$id])) {
                $this->schedule[$id] = $timer->getInterval() + $time;
                $this->sorted = false;
            } else {
                unset($this->timers[$id], $this->schedule[$id]);
            }
        }
    }
}
