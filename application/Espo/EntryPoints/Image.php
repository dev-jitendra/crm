<?php


namespace Espo\EntryPoints;

use Espo\Repositories\Attachment as AttachmentRepository;

use Espo\Core\Acl;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\NotFoundSilent;
use Espo\Core\FileStorage\Manager as FileStorageManager;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata;

use Espo\Entities\Attachment;

class Image implements EntryPoint
{
    
    protected $allowedRelatedTypeList = null;
    
    protected $allowedFieldList = null;

    private FileStorageManager $fileStorageManager;
    private FileManager $fileManager;
    protected Acl $acl;
    protected EntityManager $entityManager;
    protected Config $config;
    protected Metadata $metadata;

    public function __construct(
        FileStorageManager $fileStorageManager,
        FileManager $fileManager,
        Acl $acl,
        EntityManager $entityManager,
        Config $config,
        Metadata $metadata
    ) {
        $this->fileStorageManager = $fileStorageManager;
        $this->fileManager = $fileManager;
        $this->acl = $acl;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->metadata = $metadata;
    }

    public function run(Request $request, Response $response): void
    {
        $id = $request->getQueryParam('id');
        $size = $request->getQueryParam('size') ?? null;

        if (!$id) {
            throw new BadRequest();
        }

        $this->show($response, $id, $size, false);
    }

    
    protected function show(Response $response, string $id, ?string $size, bool $disableAccessCheck = false): void
    {
        
        $attachment = $this->entityManager->getEntityById(Attachment::ENTITY_TYPE, $id);

        if (!$attachment) {
            throw new NotFoundSilent();
        }

        if (!$disableAccessCheck && !$this->acl->checkEntity($attachment)) {
            throw new ForbiddenSilent("No access to attachment.");
        }

        $fileType = $attachment->getType();

        if (!in_array($fileType, $this->getAllowedFileTypeList())) {
            throw new ForbiddenSilent("Not allowed file type '{$fileType}'.");
        }

        if ($this->allowedRelatedTypeList) {
            if (!in_array($attachment->getRelatedType(), $this->allowedRelatedTypeList)) {
                throw new NotFoundSilent();
            }
        }

        if ($this->allowedFieldList) {
            if (!in_array($attachment->getTargetField(), $this->allowedFieldList)) {
                throw new NotFoundSilent();
            }
        }

        $toResize = $size && in_array($fileType, $this->getResizableFileTypeList());

        if ($toResize) {
            $fileName = $size . '-' . $attachment->getName();

            $contents = $this->getThumbContents($attachment, $size);

            $fileSize = strlen($contents);

            $response->writeBody($contents);
        }
        else {
            $fileName = $attachment->getName();

            $stream = $this->fileStorageManager->getStream($attachment);

            $fileSize = $stream->getSize() ?? $this->fileStorageManager->getSize($attachment);

            $response->setBody($stream);
        }

        if ($fileType) {
            $response->setHeader('Content-Type', $fileType);
        }

        $response
            ->setHeader('Content-Disposition', 'inline;filename="' . $fileName . '"')
            ->setHeader('Pragma', 'public')
            ->setHeader('Cache-Control', 'max-age=360000, must-revalidate')
            ->setHeader('Content-Length', (string) $fileSize)
            ->setHeader('Content-Security-Policy', "default-src 'self'");
    }

    
    private function getThumbContents(Attachment $attachment, string $size): string
    {
        if (!array_key_exists($size, $this->getSizes())) {
            throw new Error("Bad size.");
        }

        $useCache = !$this->config->get('thumbImageCacheDisabled', false);

        $sourceId = $attachment->getSourceId();

        $cacheFilePath = "data/upload/thumbs/{$sourceId}_{$size}";

        if ($useCache && $this->fileManager->isFile($cacheFilePath)) {
            return $this->fileManager->getContents($cacheFilePath);
        }

        $filePath = $this->getAttachmentRepository()->getFilePath($attachment);

        if (!$this->fileManager->isFile($filePath)) {
            throw new NotFound();
        }

        $fileType = $attachment->getType() ?? '';

        $targetImage = $this->createThumbImage($filePath, $fileType, $size);

        ob_start();

        switch ($fileType) {
            case 'image/jpeg':
                imagejpeg($targetImage); 

                break;

            case 'image/png':
                imagepng($targetImage); 

                break;

            case 'image/gif':
                imagegif($targetImage); 

                break;

            case 'image/webp':
                imagewebp($targetImage); 

                break;
        }

        $contents = ob_get_contents() ?: '';

        ob_end_clean();

        imagedestroy($targetImage); 

        if ($useCache) {
            $this->fileManager->putContents($cacheFilePath, $contents);
        }

        return $contents;
    }

    
    private function createThumbImage(string $filePath, string $fileType, string $size)
    {
        if (!is_array(getimagesize($filePath))) {
            throw new Error();
        }

        list($originalWidth, $originalHeight) = getimagesize($filePath);

        list($width, $height) = $this->getSizes()[$size];

        if ($originalWidth <= $width && $originalHeight <= $height) {
            $targetWidth = $originalWidth;
            $targetHeight = $originalHeight;
        }
        else {
            if ($originalWidth > $originalHeight) {
                $targetWidth = $width;
                $targetHeight = $originalHeight / ($originalWidth / $width);

                if ($targetHeight > $height) {
                    $targetHeight = $height;
                    $targetWidth = $originalWidth / ($originalHeight / $height);
                }
            } else {
                $targetHeight = $height;
                $targetWidth = $originalWidth / ($originalHeight / $height);

                if ($targetWidth > $width) {
                    $targetWidth = $width;
                    $targetHeight = $originalHeight / ($originalWidth / $width);
                }
            }
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        switch ($fileType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filePath);

                imagecopyresampled(
                    $targetImage, $sourceImage, 0, 0, 0, 0, 
                    $targetWidth, $targetHeight, $originalWidth, $originalHeight
                );
                break;

            case 'image/png':
                $sourceImage = imagecreatefrompng($filePath);

                imagealphablending($targetImage, false); 
                imagesavealpha($targetImage, true); 

                $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127); 

                
                imagefilledrectangle($targetImage, 0, 0, $targetWidth, $targetHeight, $transparent);

                imagecopyresampled(
                    $targetImage, $sourceImage, 0, 0, 0, 0, 
                    $targetWidth, $targetHeight, $originalWidth, $originalHeight
                );

                break;

            case 'image/gif':
                $sourceImage = imagecreatefromgif($filePath);

                imagecopyresampled(
                    $targetImage, $sourceImage, 0, 0, 0, 0, 
                    $targetWidth, $targetHeight, $originalWidth, $originalHeight
                );

                break;

            case 'image/webp':
                $sourceImage = imagecreatefromwebp($filePath);

                imagecopyresampled(
                    $targetImage, $sourceImage, 0, 0, 0, 0, 
                    $targetWidth, $targetHeight, $originalWidth, $originalHeight
                );

                break;
        }

        if (in_array($fileType, $this->getFixOrientationFileTypeList())) {
            $targetImage = $this->fixOrientation($targetImage, $filePath); 
        }

        return $targetImage; 
    }

    
    private function getOrientation(string $filePath)
    {
        if (!function_exists('exif_read_data')) {
            return 0;
        }

        $data = exif_read_data($filePath) ?: [];

        return $data['Orientation'] ?? null;
    }

    
    private function fixOrientation($targetImage, string $filePath)
    {
        $orientation = $this->getOrientation($filePath);

        if ($orientation) {
            $angle = array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[$orientation];

            $targetImage = imagerotate($targetImage, $angle, 0) ?: $targetImage; 
        }

        
        return $targetImage;
    }

    
    private function getAllowedFileTypeList(): array
    {
        return $this->metadata->get(['app', 'image', 'allowedFileTypeList']) ?? [];
    }

    
    private function getResizableFileTypeList(): array
    {
        return $this->metadata->get(['app', 'image', 'resizableFileTypeList']) ?? [];
    }

    
    private function getFixOrientationFileTypeList(): array
    {
        return $this->metadata->get(['app', 'image', 'fixOrientationFileTypeList']) ?? [];
    }

    
    protected function getSizes(): array
    {
        return $this->metadata->get(['app', 'image', 'sizes']) ?? [];
    }

    private function getAttachmentRepository(): AttachmentRepository
    {
        
        return $this->entityManager->getRepository(Attachment::ENTITY_TYPE);
    }
}
