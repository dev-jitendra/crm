<?php

namespace ZBateson\MailMimeParser\Header\Part;
use ZBateson\MbWrapper\MbWrapper;


class CommentPart extends MimeLiteralPart
{
    
    protected $comment;
    
    
    public function __construct(MbWrapper $charsetConverter, $token)
    {
        parent::__construct($charsetConverter, $token);
        $this->comment = $this->value;
        $this->value = '';
        $this->canIgnoreSpacesBefore = true;
        $this->canIgnoreSpacesAfter = true;
    }
    
    
    public function getComment()
    {
        return $this->comment;
    }
    
    
}
