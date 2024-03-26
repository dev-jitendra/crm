<?php

namespace ZBateson\MailMimeParser\Message\Part;

use ZBateson\MailMimeParser\Message\PartFilter;


class MimePart extends ParentHeaderPart
{
    
    public function isMultiPart()
    {
        
        return (bool) (preg_match(
            '~multipart/.*~i',
            $this->getContentType()
        ));
    }
    
    
    public function getFilename()
    {
        return $this->getHeaderParameter(
            'Content-Disposition',
            'filename',
            $this->getHeaderParameter(
                'Content-Type',
                'name'
            )
        );
    }
    
    
    public function isMime()
    {
        return true;
    }
    
    
    public function isTextPart()
    {
        return ($this->getCharset() !== null);
    }
    
    
    public function getContentType($default = 'text/plain')
    {
        return trim(strtolower($this->getHeaderValue('Content-Type', $default)));
    }
    
    
    public function getCharset()
    {
        $charset = $this->getHeaderParameter('Content-Type', 'charset');
        if ($charset === null || strcasecmp($charset, 'binary') === 0) {
            $contentType = $this->getContentType();
            if ($contentType === 'text/plain' || $contentType === 'text/html') {
                return 'ISO-8859-1';
            }
            return null;
        }
        return trim(strtoupper($charset));
    }
    
    
    public function getContentDisposition($default = 'inline')
    {
        return strtolower($this->getHeaderValue('Content-Disposition', $default));
    }
    
    
    public function getContentTransferEncoding($default = '7bit')
    {
        static $translated = [
            'x-uue' => 'x-uuencode',
            'uue' => 'x-uuencode',
            'uuencode' => 'x-uuencode'
        ];
        $type = strtolower($this->getHeaderValue('Content-Transfer-Encoding', $default));
        if (isset($translated[$type])) {
            return $translated[$type];
        }
        return $type;
    }

    
    public function getContentId()
    {
        return $this->getHeaderValue('Content-ID');
    }

    
    public function getPartByContentId($contentId)
    {
        $sanitized = preg_replace('/^\s*<|>\s*$/', '', $contentId);
        $filter = $this->partFilterFactory->newFilterFromArray([
            'headers' => [
                PartFilter::FILTER_INCLUDE => [
                    'Content-ID' => $sanitized
                ]
            ]
        ]);
        return $this->getPart(0, $filter);
    }
}
