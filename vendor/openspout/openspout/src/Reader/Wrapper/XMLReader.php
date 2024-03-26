<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Wrapper;

use ZipArchive;


final class XMLReader extends \XMLReader
{
    use XMLInternalErrorsHelper;

    public const ZIP_WRAPPER = 'zip:

    
    public function openFileInZip(string $zipFilePath, string $fileInsideZipPath): bool
    {
        $wasOpenSuccessful = false;
        $realPathURI = $this->getRealPathURIForFileInZip($zipFilePath, $fileInsideZipPath);

        
        
        if ($this->fileExistsWithinZip($realPathURI)) {
            $wasOpenSuccessful = $this->open($realPathURI, null, LIBXML_NONET);
        }

        return $wasOpenSuccessful;
    }

    
    public function getRealPathURIForFileInZip(string $zipFilePath, string $fileInsideZipPath): string
    {
        
        $fileInsideZipPathWithoutLeadingSlash = ltrim($fileInsideZipPath, '/');

        return self::ZIP_WRAPPER.realpath($zipFilePath).'#'.$fileInsideZipPathWithoutLeadingSlash;
    }

    
    public function read(): bool
    {
        $this->useXMLInternalErrors();

        $wasReadSuccessful = parent::read();

        $this->resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured();

        return $wasReadSuccessful;
    }

    
    public function readUntilNodeFound(string $nodeName): bool
    {
        do {
            $wasReadSuccessful = $this->read();
            $isNotPositionedOnStartingNode = !$this->isPositionedOnStartingNode($nodeName);
        } while ($wasReadSuccessful && $isNotPositionedOnStartingNode);

        return $wasReadSuccessful;
    }

    
    public function next($localName = null): bool
    {
        $this->useXMLInternalErrors();

        $wasNextSuccessful = parent::next($localName);

        $this->resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured();

        return $wasNextSuccessful;
    }

    
    public function isPositionedOnStartingNode(string $nodeName): bool
    {
        return $this->isPositionedOnNode($nodeName, self::ELEMENT);
    }

    
    public function isPositionedOnEndingNode(string $nodeName): bool
    {
        return $this->isPositionedOnNode($nodeName, self::END_ELEMENT);
    }

    
    public function getCurrentNodeName(): string
    {
        return $this->localName;
    }

    
    private function fileExistsWithinZip(string $zipStreamURI): bool
    {
        $doesFileExists = false;

        $pattern = '/zip:\/\/([^#]+)#(.*)/';
        if (1 === preg_match($pattern, $zipStreamURI, $matches)) {
            $zipFilePath = $matches[1];
            $innerFilePath = $matches[2];

            $zip = new ZipArchive();
            if (true === $zip->open($zipFilePath)) {
                $doesFileExists = (false !== $zip->locateName($innerFilePath));
                $zip->close();
            }
        }

        return $doesFileExists;
    }

    
    private function isPositionedOnNode(string $nodeName, int $nodeType): bool
    {
        
        $hasPrefix = str_contains($nodeName, ':');
        $currentNodeName = ($hasPrefix) ? $this->name : $this->localName;

        return $this->nodeType === $nodeType && $currentNodeName === $nodeName;
    }
}
