<?php

namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\DatePart;


class DateHeader extends AbstractHeader
{
    
    protected function getConsumer(ConsumerService $consumerService)
    {
        return $consumerService->getDateConsumer();
    }
    
    
    public function getDateTime()
    {
        if (!empty($this->parts) && $this->parts[0] instanceof DatePart) {
            return $this->parts[0]->getDateTime();
        }
        return null;
    }
}
