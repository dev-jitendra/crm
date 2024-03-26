<?php

namespace ZBateson\MailMimeParser\Message\Part;


class NonMimePart extends MessagePart
{
    
    public function isTextPart()
    {
        return true;
    }
    
    
    public function getContentType()
    {
        return 'text/plain';
    }
    
    
    public function getCharset()
    {
        return 'ISO-8859-1';
    }
    
    
    public function getContentDisposition()
    {
        return 'inline';
    }
    
    
    public function getContentTransferEncoding()
    {
        return '7bit';
    }
    
    
    public function isMime()
    {
        return false;
    }

    
    public function getContentId()
    {
        return null;
    }
}
