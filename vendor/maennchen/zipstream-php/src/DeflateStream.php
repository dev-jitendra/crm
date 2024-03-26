<?php
declare(strict_types=1);

namespace ZipStream;

class DeflateStream extends Stream
{
    protected $filter;

    
    protected $options;

    
    public function rewind(): void
    {
        
        if ($this->filter) {
            $this->removeDeflateFilter();
            $this->seek(0);
            $this->addDeflateFilter($this->options);
        } else {
            rewind($this->stream);
        }
    }

    
    public function removeDeflateFilter(): void
    {
        if (!$this->filter) {
            return;
        }
        stream_filter_remove($this->filter);
        $this->filter = null;
    }

    
    public function addDeflateFilter(Option\File $options): void
    {
        $this->options = $options;
        
        
        $optionsArr = [
            'comment' => $options->getComment(),
            'method' => $options->getMethod(),
            'deflateLevel' => $options->getDeflateLevel(),
            'time' => $options->getTime()
        ];
        $this->filter = stream_filter_append(
            $this->stream,
            'zlib.deflate',
            STREAM_FILTER_READ,
            $optionsArr
        );
    }
}
