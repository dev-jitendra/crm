<?php


namespace Espo\Core\FileStorage;

use Espo\Entities\Attachment as AttachmentEntity;
use Psr\Http\Message\StreamInterface;

use GuzzleHttp\Psr7\Utils;

use RuntimeException;



class Manager
{
    
    private array $implHash = [];

    private const DEFAULT_STORAGE = 'EspoUploadDir';

    private Factory $factory;

    
    private $resourceMap = [];

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    
    public function exists(AttachmentEntity $attachment): bool
    {
        $implementation = $this->getImplementation($attachment);

        return $implementation->exists(self::wrapAttachmentEntity($attachment));
    }

    
    public function getSize(AttachmentEntity $attachment): int
    {
        $implementation = $this->getImplementation($attachment);

        return $implementation->getSize(self::wrapAttachmentEntity($attachment));
    }

    
    public function getContents(AttachmentEntity $attachment): string
    {
        $implementation = $this->getImplementation($attachment);

        return $implementation->getStream(self::wrapAttachmentEntity($attachment))->getContents();
    }

    
    public function getStream(AttachmentEntity $attachment): StreamInterface
    {
        $implementation = $this->getImplementation($attachment);

        return $implementation->getStream(self::wrapAttachmentEntity($attachment));
    }

    
    public function putStream(AttachmentEntity $attachment, StreamInterface $stream): void
    {
        $implementation = $this->getImplementation($attachment);

        $implementation->putStream(self::wrapAttachmentEntity($attachment), $stream);
    }

    
    public function putContents(AttachmentEntity $attachment, string $contents): void
    {
        $implementation = $this->getImplementation($attachment);

        $stream = Utils::streamFor($contents);

        $implementation->putStream(self::wrapAttachmentEntity($attachment), $stream);
    }

    
    public function unlink(AttachmentEntity $attachment): void
    {
        $implementation = $this->getImplementation($attachment);

        $implementation->unlink(self::wrapAttachmentEntity($attachment));
    }

    
    public function isLocal(AttachmentEntity $attachment): bool
    {
        $implementation = $this->getImplementation($attachment);

        return $implementation instanceof Local;
    }

    
    public function getLocalFilePath(AttachmentEntity $attachment): string
    {
        $implementation = $this->getImplementation($attachment);

        if ($implementation instanceof Local) {
            return $implementation->getLocalFilePath(self::wrapAttachmentEntity($attachment));
        }

        $contents = $this->getContents($attachment);

        $resource = tmpfile();

        if ($resource === false) {
            throw new RuntimeException("Could not create temp file.");
        }

        fwrite($resource, $contents);

        $path = stream_get_meta_data($resource)['uri'];

        
        $this->resourceMap[$path] = $resource;

        return $path;
    }

    private static function wrapAttachmentEntity(AttachmentEntity $attachment): AttachmentEntityWrapper
    {
        return new AttachmentEntityWrapper($attachment);
    }

    private function getImplementation(AttachmentEntity $attachment): Storage
    {
        $storage = $attachment->getStorage();

        if (!$storage) {
            $storage = self::DEFAULT_STORAGE;
        }

        if (!array_key_exists($storage, $this->implHash)) {
            $this->implHash[$storage] = $this->factory->create($storage);
        }

        return $this->implHash[$storage];
    }
}
