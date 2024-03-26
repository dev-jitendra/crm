<?php


namespace Espo\Tools\EmailTemplate;

use Espo\Entities\Attachment;

use stdClass;

class Result
{
    private $subject;
    private $body;
    private $isHtml = false;
    private $attachmentList = [];

    
    public function __construct(
        string $subject,
        string $body,
        bool $isHtml,
        array $attachmentList
    ) {
        $this->subject = $subject;
        $this->body = $body;
        $this->isHtml = $isHtml;
        $this->attachmentList = $attachmentList;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    
    public function getAttachmentList(): array
    {
        return $this->attachmentList;
    }

    
    public function getAttachmentIdList(): array
    {
        $list = [];

        foreach ($this->attachmentList as $attachment) {
            $list[] = $attachment->getId();
        }

        return $list;
    }

    public function getValueMap(): stdClass
    {
        $attachmentsIds = [];
        $attachmentsNames = (object) [];

        foreach ($this->attachmentList as $attachment) {
            $id = $attachment->getId();

            $attachmentsIds[] = $id;
            $attachmentsNames->$id = $attachment->get('name');
        }

        return (object) [
            'subject' => $this->subject,
            'body' => $this->body,
            'isHtml' => $this->isHtml,
            'attachmentsIds' => $attachmentsIds,
            'attachmentsNames' => $attachmentsNames,
        ];
    }
}
