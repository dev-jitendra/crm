<?php

namespace ZBateson\MailMimeParser\Message;


class PartFilterFactory
{
    
    public function newFilterFromContentType($mimeType)
    {
        return PartFilter::fromContentType($mimeType);
    }
    
    
    public function newFilterFromInlineContentType($mimeType)
    {
        return PartFilter::fromInlineContentType($mimeType);
    }
    
    
    public function newFilterFromDisposition($disposition, $multipart = PartFilter::FILTER_OFF)
    {
        return PartFilter::fromDisposition($disposition, $multipart);
    }
    
    
    public function newFilterFromArray(array $init)
    {
        return new PartFilter($init);
    }
}
