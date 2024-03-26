<?php

declare(strict_types=1);

namespace OpenSpout\Reader\Wrapper;

use OpenSpout\Reader\Exception\XMLProcessingException;


trait XMLInternalErrorsHelper
{
    
    private bool $initialUseInternalErrorsValue;

    
    private function useXMLInternalErrors(): void
    {
        libxml_clear_errors();
        $this->initialUseInternalErrorsValue = libxml_use_internal_errors(true);
    }

    
    private function resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured(): void
    {
        if ($this->hasXMLErrorOccured()) {
            $this->resetXMLInternalErrorsSetting();

            throw new XMLProcessingException($this->getLastXMLErrorMessage());
        }

        $this->resetXMLInternalErrorsSetting();
    }

    private function resetXMLInternalErrorsSetting(): void
    {
        libxml_use_internal_errors($this->initialUseInternalErrorsValue);
    }

    
    private function hasXMLErrorOccured(): bool
    {
        return false !== libxml_get_last_error();
    }

    
    private function getLastXMLErrorMessage(): string
    {
        $errorMessage = '';
        $error = libxml_get_last_error();

        if (false !== $error) {
            $errorMessage = trim($error->message);
        }

        return $errorMessage;
    }
}
