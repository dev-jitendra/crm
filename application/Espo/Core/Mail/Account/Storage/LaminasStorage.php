<?php


namespace Espo\Core\Mail\Account\Storage;

use Espo\Core\Mail\Account\Storage;
use Espo\Core\Mail\Mail\Storage\Imap;
use Espo\Core\Field\DateTime;

use RecursiveIteratorIterator;

class LaminasStorage implements Storage
{
    public function __construct(private Imap $imap)
    {}

    
    public function setFlags(int $id, array $flags): void
    {
        $this->imap->setFlags($id, $flags);
    }

    public function getSize(int $id): int
    {
        
        return $this->imap->getSize($id);
    }

    public function getRawContent(int $id): string
    {
        return $this->imap->getRawContent($id);
    }

    public function getUniqueId(int $id): string
    {
        
        return $this->imap->getUniqueId($id);
    }

    
    public function getIdsFromUniqueId(string $uniqueId): array
    {
        return $this->imap->getIdsFromUniqueId($uniqueId);
    }

    
    public function getIdsSinceDate(DateTime $since): array
    {
        return $this->imap->getIdsSinceDate(
            $since->toDateTime()->format('d-M-Y')
        );
    }

    
    public function getHeaderAndFlags(int $id): array
    {
        return $this->imap->getHeaderAndFlags($id);
    }

    public function close(): void
    {
        $this->imap->close();
    }

    
    public function getFolderNames(): array
    {
        $folderIterator = new RecursiveIteratorIterator(
            $this->imap->getFolders(),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $list = [];

        foreach ($folderIterator as $folder) {
            $list[] = mb_convert_encoding($folder->getGlobalName(), 'UTF-8', 'UTF7-IMAP');
        }

        
        return $list;
    }

    public function selectFolder(string $name): void
    {
        $nameConverted = mb_convert_encoding($name, 'UTF7-IMAP', 'UTF-8');

        $this->imap->selectFolder($nameConverted);
    }

    public function appendMessage(string $content, ?string $folder = null): void
    {
        $this->imap->appendMessage($content, $folder);
    }
}
