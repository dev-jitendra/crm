<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS\Helper;

use DateTimeImmutable;
use OpenSpout\Common\Helper\FileSystemHelper as CommonFileSystemHelper;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\ODS\Manager\Style\StyleManager;
use OpenSpout\Writer\ODS\Manager\WorksheetManager;


final class FileSystemHelper implements FileSystemWithRootFolderHelperInterface
{
    public const MIMETYPE = 'application/vnd.oasis.opendocument.spreadsheet';

    public const META_INF_FOLDER_NAME = 'META-INF';

    public const MANIFEST_XML_FILE_NAME = 'manifest.xml';
    public const CONTENT_XML_FILE_NAME = 'content.xml';
    public const META_XML_FILE_NAME = 'meta.xml';
    public const MIMETYPE_FILE_NAME = 'mimetype';
    public const STYLES_XML_FILE_NAME = 'styles.xml';

    private readonly string $baseFolderRealPath;

    
    private readonly string $creator;
    private readonly CommonFileSystemHelper $baseFileSystemHelper;

    
    private string $rootFolder;

    
    private string $metaInfFolder;

    
    private string $sheetsContentTempFolder;

    
    private readonly ZipHelper $zipHelper;

    
    public function __construct(string $baseFolderPath, ZipHelper $zipHelper, string $creator)
    {
        $this->baseFileSystemHelper = new CommonFileSystemHelper($baseFolderPath);
        $this->baseFolderRealPath = $this->baseFileSystemHelper->getBaseFolderRealPath();
        $this->zipHelper = $zipHelper;
        $this->creator = $creator;
    }

    public function createFolder(string $parentFolderPath, string $folderName): string
    {
        return $this->baseFileSystemHelper->createFolder($parentFolderPath, $folderName);
    }

    public function createFileWithContents(string $parentFolderPath, string $fileName, string $fileContents): string
    {
        return $this->baseFileSystemHelper->createFileWithContents($parentFolderPath, $fileName, $fileContents);
    }

    public function deleteFile(string $filePath): void
    {
        $this->baseFileSystemHelper->deleteFile($filePath);
    }

    public function deleteFolderRecursively(string $folderPath): void
    {
        $this->baseFileSystemHelper->deleteFolderRecursively($folderPath);
    }

    public function getRootFolder(): string
    {
        return $this->rootFolder;
    }

    public function getSheetsContentTempFolder(): string
    {
        return $this->sheetsContentTempFolder;
    }

    
    public function createBaseFilesAndFolders(): void
    {
        $this
            ->createRootFolder()
            ->createMetaInfoFolderAndFile()
            ->createSheetsContentTempFolder()
            ->createMetaFile()
            ->createMimetypeFile()
        ;
    }

    
    public function createContentFile(WorksheetManager $worksheetManager, StyleManager $styleManager, array $worksheets): self
    {
        $contentXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <office:document-content office:version="1.2" xmlns:dc="http:
            EOD;

        $contentXmlFileContents .= $styleManager->getContentXmlFontFaceSectionContent();
        $contentXmlFileContents .= $styleManager->getContentXmlAutomaticStylesSectionContent($worksheets);

        $contentXmlFileContents .= '<office:body><office:spreadsheet>';

        $topContentTempFile = uniqid(self::CONTENT_XML_FILE_NAME);
        $this->createFileWithContents($this->rootFolder, $topContentTempFile, $contentXmlFileContents);

        
        $contentXmlFilePath = $this->rootFolder.\DIRECTORY_SEPARATOR.self::CONTENT_XML_FILE_NAME;
        $contentXmlHandle = fopen($contentXmlFilePath, 'w');
        \assert(false !== $contentXmlHandle);

        $topContentTempPathname = $this->rootFolder.\DIRECTORY_SEPARATOR.$topContentTempFile;
        $topContentTempHandle = fopen($topContentTempPathname, 'r');
        \assert(false !== $topContentTempHandle);
        stream_copy_to_stream($topContentTempHandle, $contentXmlHandle);
        fclose($topContentTempHandle);
        unlink($topContentTempPathname);

        foreach ($worksheets as $worksheet) {
            
            fwrite($contentXmlHandle, $worksheetManager->getTableElementStartAsString($worksheet));

            $worksheetFilePath = $worksheet->getFilePath();
            $this->copyFileContentsToTarget($worksheetFilePath, $contentXmlHandle);

            fwrite($contentXmlHandle, '</table:table>');
        }

        
        $databaseRanges = '';
        foreach ($worksheets as $worksheet) {
            $databaseRanges .= $worksheetManager->getTableDatabaseRangeElementAsString($worksheet);
        }
        if ('' !== $databaseRanges) {
            fwrite($contentXmlHandle, '<table:database-ranges>');
            fwrite($contentXmlHandle, $databaseRanges);
            fwrite($contentXmlHandle, '</table:database-ranges>');
        }

        $contentXmlFileContents = '</office:spreadsheet></office:body></office:document-content>';

        fwrite($contentXmlHandle, $contentXmlFileContents);
        fclose($contentXmlHandle);

        return $this;
    }

    
    public function deleteWorksheetTempFolder(): self
    {
        $this->deleteFolderRecursively($this->sheetsContentTempFolder);

        return $this;
    }

    
    public function createStylesFile(StyleManager $styleManager, int $numWorksheets): self
    {
        $stylesXmlFileContents = $styleManager->getStylesXMLFileContent($numWorksheets);
        $this->createFileWithContents($this->rootFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

        return $this;
    }

    
    public function zipRootFolderAndCopyToStream($streamPointer): void
    {
        $zip = $this->zipHelper->createZip($this->rootFolder);

        $zipFilePath = $this->zipHelper->getZipFilePath($zip);

        
        
        
        $this->zipHelper->addUncompressedFileToArchive($zip, $this->rootFolder, self::MIMETYPE_FILE_NAME);

        $this->zipHelper->addFolderToArchive($zip, $this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $this->zipHelper->closeArchiveAndCopyToStream($zip, $streamPointer);

        
        $this->deleteFile($zipFilePath);
    }

    
    private function createRootFolder(): self
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('ods'));

        return $this;
    }

    
    private function createMetaInfoFolderAndFile(): self
    {
        $this->metaInfFolder = $this->createFolder($this->rootFolder, self::META_INF_FOLDER_NAME);

        $this->createManifestFile();

        return $this;
    }

    
    private function createManifestFile(): self
    {
        $manifestXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8"?>
            <manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">
                <manifest:file-entry manifest:full-path="/" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>
                <manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>
                <manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
                <manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>
            </manifest:manifest>
            EOD;

        $this->createFileWithContents($this->metaInfFolder, self::MANIFEST_XML_FILE_NAME, $manifestXmlFileContents);

        return $this;
    }

    
    private function createSheetsContentTempFolder(): self
    {
        $this->sheetsContentTempFolder = $this->createFolder($this->rootFolder, 'worksheets-temp');

        return $this;
    }

    
    private function createMetaFile(): self
    {
        $createdDate = (new DateTimeImmutable())->format(DateTimeImmutable::W3C);

        $metaXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <office:document-meta office:version="1.2" xmlns:dc="http:
                <office:meta>
                    <dc:creator>{$this->creator}</dc:creator>
                    <meta:creation-date>{$createdDate}</meta:creation-date>
                    <dc:date>{$createdDate}</dc:date>
                </office:meta>
            </office:document-meta>
            EOD;

        $this->createFileWithContents($this->rootFolder, self::META_XML_FILE_NAME, $metaXmlFileContents);

        return $this;
    }

    
    private function createMimetypeFile(): self
    {
        $this->createFileWithContents($this->rootFolder, self::MIMETYPE_FILE_NAME, self::MIMETYPE);

        return $this;
    }

    
    private function copyFileContentsToTarget(string $sourceFilePath, $targetResource): void
    {
        $sourceHandle = fopen($sourceFilePath, 'r');
        \assert(false !== $sourceHandle);
        stream_copy_to_stream($sourceHandle, $targetResource);
        fclose($sourceHandle);
    }
}
