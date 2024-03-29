<?php

declare(strict_types=1);

namespace League\Flysystem;

use function sprintf;

class MountManager implements FilesystemOperator
{
    
    private $filesystems = [];

    
    public function __construct(array $filesystems = [])
    {
        $this->mountFilesystems($filesystems);
    }

    public function fileExists(string $location): bool
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->fileExists($path);
        } catch (UnableToCheckFileExistence $exception) {
            throw UnableToCheckFileExistence::forLocation($location, $exception);
        }
    }

    public function read(string $location): string
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->read($path);
        } catch (UnableToReadFile $exception) {
            throw UnableToReadFile::fromLocation($location, $exception->reason(), $exception);
        }
    }

    public function readStream(string $location)
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->readStream($path);
        } catch (UnableToReadFile $exception) {
            throw UnableToReadFile::fromLocation($location, $exception->reason(), $exception);
        }
    }

    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        
        [$filesystem, $path, $mountIdentifier] = $this->determineFilesystemAndPath($location);

        return
            $filesystem
                ->listContents($path, $deep)
                ->map(
                    function (StorageAttributes $attributes) use ($mountIdentifier) {
                        return $attributes->withPath(sprintf('%s:
                    }
                );
    }

    public function lastModified(string $location): int
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->lastModified($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::lastModified($location, $exception->reason(), $exception);
        }
    }

    public function fileSize(string $location): int
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->fileSize($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::fileSize($location, $exception->reason(), $exception);
        }
    }

    public function mimeType(string $location): string
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->mimeType($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::mimeType($location, $exception->reason(), $exception);
        }
    }

    public function visibility(string $location): string
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            return $filesystem->visibility($path);
        } catch (UnableToRetrieveMetadata $exception) {
            throw UnableToRetrieveMetadata::visibility($location, $exception->reason(), $exception);
        }
    }

    public function write(string $location, string $contents, array $config = []): void
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->write($path, $contents, $config);
        } catch (UnableToWriteFile $exception) {
            throw UnableToWriteFile::atLocation($location, $exception->reason(), $exception);
        }
    }

    public function writeStream(string $location, $contents, array $config = []): void
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);
        $filesystem->writeStream($path, $contents, $config);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($path);
        $filesystem->setVisibility($path, $visibility);
    }

    public function delete(string $location): void
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->delete($path);
        } catch (UnableToDeleteFile $exception) {
            throw UnableToDeleteFile::atLocation($location, '', $exception);
        }
    }

    public function deleteDirectory(string $location): void
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->deleteDirectory($path);
        } catch (UnableToDeleteDirectory $exception) {
            throw UnableToDeleteDirectory::atLocation($location, '', $exception);
        }
    }

    public function createDirectory(string $location, array $config = []): void
    {
        
        [$filesystem, $path] = $this->determineFilesystemAndPath($location);

        try {
            $filesystem->createDirectory($path, $config);
        } catch (UnableToCreateDirectory $exception) {
            throw UnableToCreateDirectory::dueToFailure($location, $exception);
        }
    }

    public function move(string $source, string $destination, array $config = []): void
    {
        
        
        [$sourceFilesystem, $sourcePath] = $this->determineFilesystemAndPath($source);
        [$destinationFilesystem, $destinationPath] = $this->determineFilesystemAndPath($destination);

        $sourceFilesystem === $destinationFilesystem ? $this->moveInTheSameFilesystem(
            $sourceFilesystem,
            $sourcePath,
            $destinationPath,
            $source,
            $destination
        ) : $this->moveAcrossFilesystems($source, $destination);
    }

    public function copy(string $source, string $destination, array $config = []): void
    {
        
        
        [$sourceFilesystem, $sourcePath] = $this->determineFilesystemAndPath($source);
        [$destinationFilesystem, $destinationPath] = $this->determineFilesystemAndPath($destination);

        $sourceFilesystem === $destinationFilesystem ? $this->copyInSameFilesystem(
            $sourceFilesystem,
            $sourcePath,
            $destinationPath,
            $source,
            $destination
        ) : $this->copyAcrossFilesystem(
            $config['visibility'] ?? null,
            $sourceFilesystem,
            $sourcePath,
            $destinationFilesystem,
            $destinationPath,
            $source,
            $destination
        );
    }

    private function mountFilesystems(array $filesystems): void
    {
        foreach ($filesystems as $key => $filesystem) {
            $this->guardAgainstInvalidMount($key, $filesystem);
            
            
            $this->mountFilesystem($key, $filesystem);
        }
    }

    
    private function guardAgainstInvalidMount($key, $filesystem): void
    {
        if ( ! is_string($key)) {
            throw UnableToMountFilesystem::becauseTheKeyIsNotValid($key);
        }

        if ( ! $filesystem instanceof FilesystemOperator) {
            throw UnableToMountFilesystem::becauseTheFilesystemWasNotValid($filesystem);
        }
    }

    private function mountFilesystem(string $key, FilesystemOperator $filesystem): void
    {
        $this->filesystems[$key] = $filesystem;
    }

    
    private function determineFilesystemAndPath(string $path): array
    {
        if (strpos($path, ':
            throw UnableToResolveFilesystemMount::becauseTheSeparatorIsMissing($path);
        }

        
        
        [$mountIdentifier, $mountPath] = explode(':

        if ( ! array_key_exists($mountIdentifier, $this->filesystems)) {
            throw UnableToResolveFilesystemMount::becauseTheMountWasNotRegistered($mountIdentifier);
        }

        return [$this->filesystems[$mountIdentifier], $mountPath, $mountIdentifier];
    }

    private function copyInSameFilesystem(
        FilesystemOperator $sourceFilesystem,
        string $sourcePath,
        string $destinationPath,
        string $source,
        string $destination
    ): void {
        try {
            $sourceFilesystem->copy($sourcePath, $destinationPath);
        } catch (UnableToCopyFile $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function copyAcrossFilesystem(
        ?string $visibility,
        FilesystemOperator $sourceFilesystem,
        string $sourcePath,
        FilesystemOperator $destinationFilesystem,
        string $destinationPath,
        string $source,
        string $destination
    ): void {
        try {
            $visibility = $visibility ?? $sourceFilesystem->visibility($sourcePath);
            $stream = $sourceFilesystem->readStream($sourcePath);
            $destinationFilesystem->writeStream($destinationPath, $stream, compact('visibility'));
        } catch (UnableToRetrieveMetadata | UnableToReadFile | UnableToWriteFile $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function moveInTheSameFilesystem(
        FilesystemOperator $sourceFilesystem,
        string $sourcePath,
        string $destinationPath,
        string $source,
        string $destination
    ): void {
        try {
            $sourceFilesystem->move($sourcePath, $destinationPath);
        } catch (UnableToMoveFile $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    private function moveAcrossFilesystems(string $source, string $destination): void
    {
        try {
            $this->copy($source, $destination);
            $this->delete($source);
        } catch (UnableToCopyFile | UnableToDeleteFile $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }
}
