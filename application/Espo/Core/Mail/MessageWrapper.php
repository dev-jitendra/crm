<?php


namespace Espo\Core\Mail;

use Espo\Core\Mail\Account\Storage;
use Espo\Core\Mail\Message\Part;

use RuntimeException;

class MessageWrapper implements Message
{
    private ?string $rawHeader = null;
    private ?string $rawContent = null;

    
    private ?array $flagList = null;

    public function __construct(
        private int $id,
        private ?Storage $storage = null,
        private ?Parser $parser = null,
        private ?string $fullRawContent = null
    ) {
        if ($storage) {
            $data = $storage->getHeaderAndFlags($id);

            $this->rawHeader = $data['header'];
            $this->flagList = $data['flags'];
        }

        if (
            !$storage &&
            $this->fullRawContent
        ) {
            $rawHeader = null;
            $rawBody = null;

            if (str_contains($this->fullRawContent, "\r\n\r\n")) {
                [$rawHeader, $rawBody] = explode("\r\n\r\n", $this->fullRawContent, 2);
            }
            else if (str_contains($this->fullRawContent, "\n\n")) {
                [$rawHeader, $rawBody] = explode("\n\n", $this->fullRawContent, 2);
            }

            $this->rawHeader = $rawHeader;
            $this->rawContent = $rawBody;
        }
    }

    public function getRawHeader(): string
    {
        return $this->rawHeader ?? '';
    }

    public function getParser(): ?Parser
    {
        return $this->parser;
    }

    public function hasHeader(string $name): bool
    {
        if (!$this->parser) {
            throw new RuntimeException();
        }

        return $this->parser->hasHeader($this, $name);
    }

    public function getHeader(string $attribute): ?string
    {
        if (!$this->parser) {
            throw new RuntimeException();
        }

        return $this->parser->getHeader($this, $attribute);
    }

    public function getRawContent(): string
    {
        if (is_null($this->rawContent)) {
            if (!$this->storage) {
                throw new RuntimeException();
            }

            $this->rawContent = $this->storage->getRawContent($this->id);
        }

        return $this->rawContent ?? '';
    }

    public function getFullRawContent(): string
    {
        if ($this->fullRawContent) {
            return $this->fullRawContent;
        }

        return $this->getRawHeader() . "\n" . $this->getRawContent();
    }

    
    public function getFlags(): array
    {
        return $this->flagList ?? [];
    }

    public function isFetched(): bool
    {
        return (bool) $this->rawHeader;
    }

    
    public function getPartList(): array
    {
        if (!$this->parser) {
            throw new RuntimeException();
        }

        return $this->parser->getPartList($this);
    }
}
