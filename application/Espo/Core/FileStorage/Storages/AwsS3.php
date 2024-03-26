<?php


namespace Espo\Core\FileStorage\Storages;

use Psr\Http\Message\StreamInterface;
use AsyncAws\S3\S3Client;
use League\Flysystem\FilesystemException;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\Filesystem;
use GuzzleHttp\Psr7\Stream;

use Espo\Core\FileStorage\Attachment;
use Espo\Core\FileStorage\Storage;
use Espo\Core\Utils\Config;

use RuntimeException;


class AwsS3 implements Storage
{
    private Filesystem $filesystem;

    public function __construct(Config $config)
    {
        $bucketName = $config->get('awsS3Storage.bucketName') ?? null;
        $path = $config->get('awsS3Storage.path') ?? null;
        $region = $config->get('awsS3Storage.region') ?? null;
        $credentials = $config->get('awsS3Storage.credentials') ?? null;
        $endpoint = $config->get('awsS3Storage.endpoint') ?? null;
        $pathStyleEndpoint = $config->get('awsS3Storage.pathStyleEndpoint') ?? false;
        $sendChunkedBody = $config->get('awsS3Storage.sendChunkedBody') ?? null;

        if (!$bucketName) {
            throw new RuntimeException("AWS S3 bucket name is not specified in config.");
        }

        $clientOptions = [
            'region' => $region,
        ];

        if ($endpoint) {
            $clientOptions['endpoint'] = $endpoint;
        }

        if ($pathStyleEndpoint) {
            $clientOptions['pathStyleEndpoint'] = (bool) $pathStyleEndpoint;
        }

        
        if ($sendChunkedBody !== null) {
            $clientOptions['sendChunkedBody'] = (bool) $sendChunkedBody;
        }

        if ($credentials && is_array($credentials)) {
            $clientOptions['accessKeyId'] = $credentials['key'] ?? null;
            $clientOptions['accessKeySecret'] = $credentials['secret'] ?? null;
        }

        $client = new S3Client($clientOptions);
        $adapter = new AsyncAwsS3Adapter($client, $bucketName, $path);

        $this->filesystem = new Filesystem($adapter);
    }

    public function unlink(Attachment $attachment): void
    {
        try {
            $this->filesystem->delete($attachment->getSourceId());
        }
        catch (FilesystemException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function exists(Attachment $attachment): bool
    {
        try {
            return $this->filesystem->fileExists($attachment->getSourceId());
        }
        catch (FilesystemException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function getSize(Attachment $attachment): int
    {
        try {
            return $this->filesystem->fileSize($attachment->getSourceId());
        }
        catch (FilesystemException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function getStream(Attachment $attachment): StreamInterface
    {
        try {
            $resource = $this->filesystem->readStream($attachment->getSourceId());
        }
        catch (FilesystemException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        return new Stream($resource);
    }

    public function putStream(Attachment $attachment, StreamInterface $stream): void
    {
        
        

        $resource = fopen('php:

        if ($resource === false) {
            throw new RuntimeException("Could not open temp.");
        }

        $stream->rewind();

        fwrite($resource, $stream->getContents());
        rewind($resource);

        try {
            $this->filesystem->writeStream($attachment->getSourceId(), $resource);
        }
        catch (FilesystemException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        fclose($resource);
    }
}
