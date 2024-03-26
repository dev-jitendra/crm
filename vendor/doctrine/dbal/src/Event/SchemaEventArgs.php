<?php

namespace Doctrine\DBAL\Event;

use Doctrine\Common\EventArgs;


class SchemaEventArgs extends EventArgs
{
    private bool $preventDefault = false;

    
    public function preventDefault()
    {
        $this->preventDefault = true;

        return $this;
    }

    
    public function isDefaultPrevented()
    {
        return $this->preventDefault;
    }
}
