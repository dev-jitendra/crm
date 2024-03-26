<?php

declare(strict_types=1);

namespace OpenSpout\Reader\ODS\Helper;

use OpenSpout\Reader\Exception\XMLProcessingException;
use OpenSpout\Reader\Wrapper\XMLReader;


final class SettingsHelper
{
    public const SETTINGS_XML_FILE_PATH = 'settings.xml';

    
    public const XML_NODE_CONFIG_ITEM = 'config:config-item';
    public const XML_ATTRIBUTE_CONFIG_NAME = 'config:name';
    public const XML_ATTRIBUTE_VALUE_ACTIVE_TABLE = 'ActiveTable';

    
    public function getActiveSheetName(string $filePath): ?string
    {
        $xmlReader = new XMLReader();
        if (false === $xmlReader->openFileInZip($filePath, self::SETTINGS_XML_FILE_PATH)) {
            return null;
        }

        $activeSheetName = null;

        try {
            while ($xmlReader->readUntilNodeFound(self::XML_NODE_CONFIG_ITEM)) {
                if (self::XML_ATTRIBUTE_VALUE_ACTIVE_TABLE === $xmlReader->getAttribute(self::XML_ATTRIBUTE_CONFIG_NAME)) {
                    $activeSheetName = $xmlReader->readString();

                    break;
                }
            }
        } catch (XMLProcessingException) {  
            
        }

        $xmlReader->close();

        return $activeSheetName;
    }
}
