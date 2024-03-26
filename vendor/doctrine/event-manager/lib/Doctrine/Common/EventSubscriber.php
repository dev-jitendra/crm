<?php

declare(strict_types=1);

namespace Doctrine\Common;


interface EventSubscriber
{
    
    public function getSubscribedEvents();
}
