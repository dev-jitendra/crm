<?php

namespace Laminas\Mail\Header;

class ReplyTo extends AbstractAddressList
{
    
    protected $fieldName = 'Reply-To';
    
    protected static $type = 'reply-to';
    
    protected static $typeAliases = ['replyto', 'reply_to'];
}
