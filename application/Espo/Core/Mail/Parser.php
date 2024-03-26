<?php


namespace Espo\Core\Mail;

use Espo\Entities\Email;
use Espo\Entities\Attachment;
use Espo\Core\Mail\Message\Part;

use stdClass;

interface Parser
{
    public function hasHeader(Message $message, string $name): bool;

    public function getHeader(Message $message, string $name): ?string;

    public function getMessageId(Message $message): ?string;

    public function getAddressNameMap(Message $message): stdClass;

    
    public function getAddressData(Message $message, string $type): ?object;

    
    public function getAddressList(Message $message, string $type): array;

    
    public function getInlineAttachmentList(Message $message, Email $email): array;

    
    public function getPartList(Message $message): array;
}
