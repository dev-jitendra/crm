<?php


namespace Espo\Core\Utils\Client;

use Espo\Core\Utils\File\Manager as FileManager;
use RuntimeException;


class DevModeJsFileListProvider
{
    private const LIBS_FILE = 'frontend/libs.json';

    public function __construct(private FileManager $fileManager)
    {}

    
    public function get(): array
    {
        $list = [];

        $items = json_decode($this->fileManager->getContents(self::LIBS_FILE));

        foreach ($items as $item) {
            if (!($item->bundle ?? false)) {
                continue;
            }

            $files = $item->files ?? null;

            if ($files !== null) {
                $list = array_merge(
                    $list,
                    array_map(
                        fn ($item) => self::prepareBundleLibFilePath($item),
                        $files
                    )
                );

                continue;
            }

            if (!isset($item->src)) {
                continue;
            }

            $list[] = self::prepareBundleLibFilePath($item);
        }

        return $list;
    }


    private function prepareBundleLibFilePath(object $item): string
    {
        $amdId = $item->amdId ?? null;

        if ($amdId) {
            return 'client/lib/original/' . $amdId . '.js';
        }

        $src = $item->src ?? null;

        if (!$src) {
            throw new RuntimeException("Missing 'src' in bundled lib definition.");
        }

        $arr = explode('/', $src);

        return 'client/lib/original/' . array_slice($arr, -1)[0];
    }
}
