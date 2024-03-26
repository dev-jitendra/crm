<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Helper;

use DateTimeImmutable;
use OpenSpout\Common\Helper\Escaper\XLSX;
use OpenSpout\Common\Helper\FileSystemHelper as CommonFileSystemHelper;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\Common\Entity\Worksheet;
use OpenSpout\Writer\Common\Helper\CellHelper;
use OpenSpout\Writer\Common\Helper\FileSystemWithRootFolderHelperInterface;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\XLSX\Manager\Style\StyleManager;
use OpenSpout\Writer\XLSX\MergeCell;
use OpenSpout\Writer\XLSX\Options;


final class FileSystemHelper implements FileSystemWithRootFolderHelperInterface
{
    public const RELS_FOLDER_NAME = '_rels';
    public const DRAWINGS_FOLDER_NAME = 'drawings';
    public const DOC_PROPS_FOLDER_NAME = 'docProps';
    public const XL_FOLDER_NAME = 'xl';
    public const WORKSHEETS_FOLDER_NAME = 'worksheets';

    public const RELS_FILE_NAME = '.rels';
    public const APP_XML_FILE_NAME = 'app.xml';
    public const CORE_XML_FILE_NAME = 'core.xml';
    public const CONTENT_TYPES_XML_FILE_NAME = '[Content_Types].xml';
    public const WORKBOOK_XML_FILE_NAME = 'workbook.xml';
    public const WORKBOOK_RELS_XML_FILE_NAME = 'workbook.xml.rels';
    public const STYLES_XML_FILE_NAME = 'styles.xml';

    private const SHEET_XML_FILE_HEADER = <<<'EOD'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <worksheet xmlns="http:
        EOD;

    private readonly string $baseFolderRealPath;
    private readonly CommonFileSystemHelper $baseFileSystemHelper;

    
    private readonly ZipHelper $zipHelper;

    
    private readonly string $creator;

    
    private readonly XLSX $escaper;

    
    private string $rootFolder;

    
    private string $relsFolder;

    
    private string $docPropsFolder;

    
    private string $xlFolder;

    
    private string $xlRelsFolder;

    
    private string $xlWorksheetsFolder;

    
    private string $sheetsContentTempFolder;

    
    public function __construct(string $baseFolderPath, ZipHelper $zipHelper, XLSX $escaper, string $creator)
    {
        $this->baseFileSystemHelper = new CommonFileSystemHelper($baseFolderPath);
        $this->baseFolderRealPath = $this->baseFileSystemHelper->getBaseFolderRealPath();
        $this->zipHelper = $zipHelper;
        $this->escaper = $escaper;
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

    public function getXlFolder(): string
    {
        return $this->xlFolder;
    }

    public function getXlWorksheetsFolder(): string
    {
        return $this->xlWorksheetsFolder;
    }

    public function getSheetsContentTempFolder(): string
    {
        return $this->sheetsContentTempFolder;
    }

    
    public function createBaseFilesAndFolders(): void
    {
        $this
            ->createRootFolder()
            ->createRelsFolderAndFile()
            ->createDocPropsFolderAndFiles()
            ->createXlFolderAndSubFolders()
            ->createSheetsContentTempFolder()
        ;
    }

    
    public function createContentTypesFile(array $worksheets): self
    {
        $contentTypesXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Types xmlns="http:
                <Default ContentType="application/xml" Extension="xml"/>
                <Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/>
                <Default ContentType="application/vnd.openxmlformats-officedocument.vmlDrawing" Extension="vml"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/>
            EOD;

        
        foreach ($worksheets as $worksheet) {
            $contentTypesXmlFileContents .= '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet'.$worksheet->getId().'.xml"/>';
            $contentTypesXmlFileContents .= '<Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.comments+xml" PartName="/xl/comments'.$worksheet->getId().'.xml" />';
        }

        $contentTypesXmlFileContents .= <<<'EOD'
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" PartName="/xl/styles.xml"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml" PartName="/xl/sharedStrings.xml"/>
                <Override ContentType="application/vnd.openxmlformats-package.core-properties+xml" PartName="/docProps/core.xml"/>
                <Override ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" PartName="/docProps/app.xml"/>
            </Types>
            EOD;

        $this->createFileWithContents($this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME, $contentTypesXmlFileContents);

        return $this;
    }

    
    public function createWorkbookFile(array $worksheets): self
    {
        $workbookXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <workbook xmlns="http:
                <sheets>
            EOD;

        
        foreach ($worksheets as $worksheet) {
            $worksheetName = $worksheet->getExternalSheet()->getName();
            $worksheetVisibility = $worksheet->getExternalSheet()->isVisible() ? 'visible' : 'hidden';
            $worksheetId = $worksheet->getId();
            $workbookXmlFileContents .= '<sheet name="'.$this->escaper->escape($worksheetName).'" sheetId="'.$worksheetId.'" r:id="rIdSheet'.$worksheetId.'" state="'.$worksheetVisibility.'"/>';
        }

        $workbookXmlFileContents .= <<<'EOD'
                </sheets>
            EOD;

        $definedNames = '';

        
        foreach ($worksheets as $worksheet) {
            $sheet = $worksheet->getExternalSheet();
            if (null !== $autofilter = $sheet->getAutoFilter()) {
                $worksheetName = $sheet->getName();
                $name = sprintf(
                    '\'%s\'!$%s$%s:$%s$%s',
                    $this->escaper->escape($worksheetName),
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->fromColumnIndex),
                    $autofilter->fromRow,
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->toColumnIndex),
                    $autofilter->toRow
                );
                $definedNames .= '<definedName function="false" hidden="true" localSheetId="'.$sheet->getIndex().'" name="_xlnm._FilterDatabase" vbProcedure="false">'.$name.'</definedName>';
            }
        }
        if ('' !== $definedNames) {
            $workbookXmlFileContents .= '<definedNames>'.$definedNames.'</definedNames>';
        }

        $workbookXmlFileContents .= <<<'EOD'
            </workbook>
            EOD;

        $this->createFileWithContents($this->xlFolder, self::WORKBOOK_XML_FILE_NAME, $workbookXmlFileContents);

        return $this;
    }

    
    public function createWorkbookRelsFile(array $worksheets): self
    {
        $workbookRelsXmlFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http:
                <Relationship Id="rIdStyles" Target="styles.xml" Type="http:
                <Relationship Id="rIdSharedStrings" Target="sharedStrings.xml" Type="http:
            EOD;

        
        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $workbookRelsXmlFileContents .= '<Relationship Id="rIdSheet'.$worksheetId.'" Target="worksheets/sheet'.$worksheetId.'.xml" Type="http:
        }

        $workbookRelsXmlFileContents .= '</Relationships>';

        $this->createFileWithContents($this->xlRelsFolder, self::WORKBOOK_RELS_XML_FILE_NAME, $workbookRelsXmlFileContents);

        return $this;
    }

    
    public function createWorksheetRelsFiles(array $worksheets): self
    {
        $this->createFolder($this->getXlWorksheetsFolder(), self::RELS_FOLDER_NAME);

        foreach ($worksheets as $worksheet) {
            $worksheetId = $worksheet->getId();
            $worksheetRelsContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
              <Relationships xmlns="http:
                <Relationship Id="rId_comments_vml1" Type="http:
                <Relationship Id="rId_comments1" Type="http:
              </Relationships>';

            $folder = $this->getXlWorksheetsFolder().\DIRECTORY_SEPARATOR.'_rels';
            $filename = 'sheet'.$worksheetId.'.xml.rels';

            $this->createFileWithContents($folder, $filename, $worksheetRelsContent);
        }

        return $this;
    }

    
    public function createStylesFile(StyleManager $styleManager): self
    {
        $stylesXmlFileContents = $styleManager->getStylesXMLFileContent();
        $this->createFileWithContents($this->xlFolder, self::STYLES_XML_FILE_NAME, $stylesXmlFileContents);

        return $this;
    }

    
    public function createContentFiles(Options $options, array $worksheets): self
    {
        $allMergeCells = $options->getMergeCells();

        foreach ($worksheets as $worksheet) {
            $contentXmlFilePath = $this->getXlWorksheetsFolder().\DIRECTORY_SEPARATOR.basename($worksheet->getFilePath());
            $worksheetFilePointer = fopen($contentXmlFilePath, 'w');
            \assert(false !== $worksheetFilePointer);

            $sheet = $worksheet->getExternalSheet();
            fwrite($worksheetFilePointer, self::SHEET_XML_FILE_HEADER);

            
            $range = '';
            if (null !== $autofilter = $sheet->getAutoFilter()) {
                $range = sprintf(
                    '%s%s:%s%s',
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->fromColumnIndex),
                    $autofilter->fromRow,
                    CellHelper::getColumnLettersFromColumnIndex($autofilter->toColumnIndex),
                    $autofilter->toRow
                );
                fwrite($worksheetFilePointer, '<sheetPr filterMode="false"><pageSetUpPr fitToPage="false"/></sheetPr>');
                fwrite($worksheetFilePointer, sprintf('<dimension ref="%s"/>', $range));
            }

            if (null !== ($sheetView = $sheet->getSheetView())) {
                fwrite($worksheetFilePointer, '<sheetViews>'.$sheetView->getXml().'</sheetViews>');
            }
            fwrite($worksheetFilePointer, $this->getXMLFragmentForDefaultCellSizing($options));
            fwrite($worksheetFilePointer, $this->getXMLFragmentForColumnWidths($options, $sheet));
            fwrite($worksheetFilePointer, '<sheetData>');

            $worksheetFilePath = $worksheet->getFilePath();
            $this->copyFileContentsToTarget($worksheetFilePath, $worksheetFilePointer);
            fwrite($worksheetFilePointer, '</sheetData>');

            
            if ('' !== $range) {
                fwrite($worksheetFilePointer, sprintf('<autoFilter ref="%s"/>', $range));
            }

            
            $mergeCells = array_filter(
                $allMergeCells,
                static fn (MergeCell $c) => $c->sheetIndex === $worksheet->getExternalSheet()->getIndex(),
            );
            if ([] !== $mergeCells) {
                $mergeCellString = '<mergeCells count="'.\count($mergeCells).'">';
                foreach ($mergeCells as $mergeCell) {
                    $topLeft = CellHelper::getColumnLettersFromColumnIndex($mergeCell->topLeftColumn).$mergeCell->topLeftRow;
                    $bottomRight = CellHelper::getColumnLettersFromColumnIndex($mergeCell->bottomRightColumn).$mergeCell->bottomRightRow;
                    $mergeCellString .= sprintf(
                        '<mergeCell ref="%s:%s"/>',
                        $topLeft,
                        $bottomRight
                    );
                }
                $mergeCellString .= '</mergeCells>';
                fwrite($worksheetFilePointer, $mergeCellString);
            }

            $this->getXMLFragmentForPageMargin($worksheetFilePointer, $options);

            $this->getXMLFragmentForPageSetup($worksheetFilePointer, $options);

            
            fwrite($worksheetFilePointer, '<legacyDrawing r:id="rId_comments_vml1"/>');

            fwrite($worksheetFilePointer, '</worksheet>');
            fclose($worksheetFilePointer);
        }

        return $this;
    }

    
    public function deleteWorksheetTempFolder(): self
    {
        $this->deleteFolderRecursively($this->sheetsContentTempFolder);

        return $this;
    }

    
    public function zipRootFolderAndCopyToStream($streamPointer): void
    {
        $zip = $this->zipHelper->createZip($this->rootFolder);

        $zipFilePath = $this->zipHelper->getZipFilePath($zip);

        
        
        
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::CONTENT_TYPES_XML_FILE_NAME);
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::XL_FOLDER_NAME.\DIRECTORY_SEPARATOR.self::WORKBOOK_XML_FILE_NAME);
        $this->zipHelper->addFileToArchive($zip, $this->rootFolder, self::XL_FOLDER_NAME.\DIRECTORY_SEPARATOR.self::STYLES_XML_FILE_NAME);

        $this->zipHelper->addFolderToArchive($zip, $this->rootFolder, ZipHelper::EXISTING_FILES_SKIP);
        $this->zipHelper->closeArchiveAndCopyToStream($zip, $streamPointer);

        
        $this->deleteFile($zipFilePath);
    }

    
    private function getXMLFragmentForPageMargin($targetResource, Options $options): void
    {
        $pageMargin = $options->getPageMargin();
        if (null === $pageMargin) {
            return;
        }

        fwrite($targetResource, "<pageMargins top=\"{$pageMargin->top}\" right=\"{$pageMargin->right}\" bottom=\"{$pageMargin->bottom}\" left=\"{$pageMargin->left}\" header=\"{$pageMargin->header}\" footer=\"{$pageMargin->footer}\"/>");
    }

    
    private function getXMLFragmentForPageSetup($targetResource, Options $options): void
    {
        $pageSetup = $options->getPageSetup();
        if (null === $pageSetup) {
            return;
        }

        $xml = '<pageSetup';

        if (null !== $pageSetup->pageOrientation) {
            $xml .= " orientation=\"{$pageSetup->pageOrientation->value}\"";
        }

        if (null !== $pageSetup->paperSize) {
            $xml .= " paperSize=\"{$pageSetup->paperSize->value}\"";
        }

        $xml .= '/>';

        fwrite($targetResource, $xml);
    }

    
    private function getXMLFragmentForColumnWidths(Options $options, Sheet $sheet): string
    {
        if ([] !== $sheet->getColumnWidths()) {
            $widths = $sheet->getColumnWidths();
        } elseif ([] !== $options->getColumnWidths()) {
            $widths = $options->getColumnWidths();
        } else {
            return '';
        }

        $xml = '<cols>';

        foreach ($widths as $columnWidth) {
            $xml .= '<col min="'.$columnWidth->start.'" max="'.$columnWidth->end.'" width="'.$columnWidth->width.'" customWidth="true"/>';
        }
        $xml .= '</cols>';

        return $xml;
    }

    
    private function getXMLFragmentForDefaultCellSizing(Options $options): string
    {
        $rowHeightXml = null === $options->DEFAULT_ROW_HEIGHT ? '' : " defaultRowHeight=\"{$options->DEFAULT_ROW_HEIGHT}\"";
        $colWidthXml = null === $options->DEFAULT_COLUMN_WIDTH ? '' : " defaultColWidth=\"{$options->DEFAULT_COLUMN_WIDTH}\"";
        if ('' === $colWidthXml && '' === $rowHeightXml) {
            return '';
        }
        
        $rowHeightXml = '' === $rowHeightXml ? ' defaultRowHeight="0"' : $rowHeightXml;

        return "<sheetFormatPr{$colWidthXml}{$rowHeightXml}/>";
    }

    
    private function createRootFolder(): self
    {
        $this->rootFolder = $this->createFolder($this->baseFolderRealPath, uniqid('xlsx', true));

        return $this;
    }

    
    private function createRelsFolderAndFile(): self
    {
        $this->relsFolder = $this->createFolder($this->rootFolder, self::RELS_FOLDER_NAME);

        $this->createRelsFile();

        return $this;
    }

    
    private function createRelsFile(): self
    {
        $relsFileContents = <<<'EOD'
            <?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http:
                <Relationship Id="rIdWorkbook" Type="http:
                <Relationship Id="rIdCore" Type="http:
                <Relationship Id="rIdApp" Type="http:
            </Relationships>
            EOD;

        $this->createFileWithContents($this->relsFolder, self::RELS_FILE_NAME, $relsFileContents);

        return $this;
    }

    
    private function createDocPropsFolderAndFiles(): self
    {
        $this->docPropsFolder = $this->createFolder($this->rootFolder, self::DOC_PROPS_FOLDER_NAME);

        $this->createAppXmlFile();
        $this->createCoreXmlFile();

        return $this;
    }

    
    private function createAppXmlFile(): self
    {
        $appXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Properties xmlns="http:
                <Application>{$this->creator}</Application>
                <TotalTime>0</TotalTime>
            </Properties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::APP_XML_FILE_NAME, $appXmlFileContents);

        return $this;
    }

    
    private function createCoreXmlFile(): self
    {
        $createdDate = (new DateTimeImmutable())->format(DateTimeImmutable::W3C);
        $coreXmlFileContents = <<<EOD
            <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <cp:coreProperties xmlns:cp="http:
                <dcterms:created xsi:type="dcterms:W3CDTF">{$createdDate}</dcterms:created>
                <dcterms:modified xsi:type="dcterms:W3CDTF">{$createdDate}</dcterms:modified>
                <cp:revision>0</cp:revision>
            </cp:coreProperties>
            EOD;

        $this->createFileWithContents($this->docPropsFolder, self::CORE_XML_FILE_NAME, $coreXmlFileContents);

        return $this;
    }

    
    private function createXlFolderAndSubFolders(): self
    {
        $this->xlFolder = $this->createFolder($this->rootFolder, self::XL_FOLDER_NAME);
        $this->createXlRelsFolder();
        $this->createXlWorksheetsFolder();
        $this->createDrawingsFolder();

        return $this;
    }

    
    private function createSheetsContentTempFolder(): self
    {
        $this->sheetsContentTempFolder = $this->createFolder($this->rootFolder, 'worksheets-temp');

        return $this;
    }

    
    private function createXlRelsFolder(): self
    {
        $this->xlRelsFolder = $this->createFolder($this->xlFolder, self::RELS_FOLDER_NAME);

        return $this;
    }

    
    private function createDrawingsFolder(): self
    {
        $this->createFolder($this->getXlFolder(), self::DRAWINGS_FOLDER_NAME);

        return $this;
    }

    
    private function createXlWorksheetsFolder(): self
    {
        $this->xlWorksheetsFolder = $this->createFolder($this->xlFolder, self::WORKSHEETS_FOLDER_NAME);

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
