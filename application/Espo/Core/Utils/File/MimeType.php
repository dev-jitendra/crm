<?php


namespace Espo\Core\Utils\File;

use Espo\Core\Utils\Metadata;

class MimeType
{
    public function __construct(private Metadata $metadata)
    {}

    
    public function getMimeTypeListByExtension(string $extension): array
    {
        $extensionLowerCase = strtolower($extension);

        
        return $this->metadata
            ->get(['app', 'file', 'extensionMimeTypeMap', $extensionLowerCase]) ?? [];
    }

    public function getMimeTypeByExtension(string $extension): ?string
    {
        $typeList = $this->getMimeTypeListByExtension($extension);

        return $typeList[0] ?? null;
    }

    public static function matchMimeTypeToAcceptToken(string $mimeType, string $token): bool
    {
        if ($mimeType === $token) {
            return true;
        }

        if (in_array($token, ['audio/*', 'video/*', 'image/*'])) {
            return strpos($mimeType, substr($token, 0, -2)) === 0;
        }

        return false;
    }
}
