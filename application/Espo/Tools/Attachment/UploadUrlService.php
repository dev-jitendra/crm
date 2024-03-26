<?php


namespace Espo\Tools\Attachment;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\ErrorSilent;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Utils\File\MimeType;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Security\UrlCheck;
use Espo\Entities\Attachment as Attachment;
use Espo\ORM\EntityManager;
use Espo\Repositories\Attachment as AttachmentRepository;

class UploadUrlService
{
    private AccessChecker $accessChecker;
    private Metadata $metadata;
    private EntityManager $entityManager;
    private MimeType $mimeType;
    private DetailsObtainer $detailsObtainer;

    public function __construct(
        AccessChecker $accessChecker,
        Metadata $metadata,
        EntityManager $entityManager,
        MimeType $mimeType,
        DetailsObtainer $detailsObtainer,
        private UrlCheck $urlCheck
    ) {
        $this->accessChecker = $accessChecker;
        $this->metadata = $metadata;
        $this->entityManager = $entityManager;
        $this->mimeType = $mimeType;
        $this->detailsObtainer = $detailsObtainer;
    }

    
    public function uploadImage(string $url, FieldData $data): Attachment
    {
        if (!$this->urlCheck->isNotInternalUrl($url)) {
            throw new ForbiddenSilent("Not allowed URL.");
        }

        $attachment = $this->getAttachmentRepository()->getNew();

        $this->accessChecker->check($data);

        [$type, $contents] = $this->getImageDataByUrl($url) ?? [null, null];

        if (!$type || !$contents) {
            throw new ErrorSilent("Bad image data.");
        }

        $attachment->set([
            'name' => $url,
            'type' => $type,
            'contents' => $contents,
            'role' => Attachment::ROLE_ATTACHMENT,
        ]);

        $attachment->set('parentType', $data->getParentType());
        $attachment->set('relatedType', $data->getRelatedType());
        $attachment->set('field', $data->getField());

        $size = mb_strlen($contents, '8bit');
        $maxSize = $this->detailsObtainer->getUploadMaxSize($attachment);

        if ($maxSize && $size > $maxSize) {
            throw new Error("File size should not exceed {$maxSize}Mb.");
        }

        $this->getAttachmentRepository()->save($attachment);

        $attachment->clear('contents');

        return $attachment;
    }

    
    private function getImageDataByUrl(string $url): ?array
    {
        $type = null;

        if (!function_exists('curl_init')) {
            return null;
        }

        $opts = [];

        $httpHeaders = [];
        $httpHeaders[] = 'Expect:';

        $opts[\CURLOPT_URL]  = $url;
        $opts[\CURLOPT_HTTPHEADER] = $httpHeaders;
        $opts[\CURLOPT_CONNECTTIMEOUT] = 10;
        $opts[\CURLOPT_TIMEOUT] = 10;
        $opts[\CURLOPT_HEADER] = true;
        $opts[\CURLOPT_BINARYTRANSFER] = true;
        $opts[\CURLOPT_VERBOSE] = true;
        $opts[\CURLOPT_SSL_VERIFYPEER] = true;
        $opts[\CURLOPT_SSL_VERIFYHOST] = 2;
        $opts[\CURLOPT_RETURNTRANSFER] = true;
        
        $opts[\CURLOPT_FOLLOWLOCATION] = false;
        $opts[\CURLOPT_MAXREDIRS] = 2;
        $opts[\CURLOPT_IPRESOLVE] = \CURL_IPRESOLVE_V4;
        $opts[\CURLOPT_PROTOCOLS] = \CURLPROTO_HTTPS | \CURLPROTO_HTTP;
        $opts[\CURLOPT_REDIR_PROTOCOLS] = \CURLPROTO_HTTPS;

        $ch = curl_init();

        curl_setopt_array($ch, $opts);

        
        $response = curl_exec($ch);

        if ($response === false) {
            curl_close($ch);

            return null;
        }

        $headerSize = curl_getinfo($ch, \CURLINFO_HEADER_SIZE);

        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        $headLineList = explode("\n", $header);

        foreach ($headLineList as $i => $line) {
            if ($i === 0) {
                continue;
            }

            if (strpos(strtolower($line), strtolower('Content-Type:')) === 0) {
                $part = trim(substr($line, 13));

                if ($part) {
                    $type = trim(explode(";", $part)[0]);
                }
            }
        }

        if (!$type) {
            
            $extension = preg_replace('#\?.*#', '', pathinfo($url, \PATHINFO_EXTENSION));

            $type = $this->mimeType->getMimeTypeByExtension($extension);
        }

        curl_close($ch);

        if (!$type) {
            return null;
        }

        
        $imageTypeList = $this->metadata->get(['app', 'image', 'allowedFileTypeList']) ?? [];

        if (!in_array($type, $imageTypeList)) {
            return null;
        }

        return [$type, $body];
    }

    private function getAttachmentRepository(): AttachmentRepository
    {
        
        return $this->entityManager->getRepositoryByClass(Attachment::class);
    }
}
