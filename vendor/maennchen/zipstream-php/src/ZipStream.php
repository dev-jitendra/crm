<?php
declare(strict_types=1);

namespace ZipStream;

use Psr\Http\Message\StreamInterface;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive as ArchiveOptions;
use ZipStream\Option\File as FileOptions;
use ZipStream\Option\Version;


class ZipStream
{
    
    const ZIP_VERSION_MADE_BY = 0x603;

    
    const FILE_HEADER_SIGNATURE = 0x04034b50;
    const CDR_FILE_SIGNATURE = 0x02014b50;
    const CDR_EOF_SIGNATURE = 0x06054b50;
    const DATA_DESCRIPTOR_SIGNATURE = 0x08074b50;
    const ZIP64_CDR_EOF_SIGNATURE = 0x06064b50;
    const ZIP64_CDR_LOCATOR_SIGNATURE = 0x07064b50;

    
    public $opt;

    
    public $files = [];

    
    public $cdr_ofs;

    
    public $ofs;

    
    protected $need_headers;

    
    protected $output_name;

    
    public function __construct(?string $name = null, ?ArchiveOptions $opt = null)
    {
        $this->opt = $opt ?: new ArchiveOptions();

        $this->output_name = $name;
        $this->need_headers = $name && $this->opt->isSendHttpHeaders();

        $this->cdr_ofs = new Bigint();
        $this->ofs = new Bigint();
    }

    
    public function addFile(string $name, string $data, ?FileOptions $options = null): void
    {
        $options = $options ?: new FileOptions();
        $options->defaultTo($this->opt);

        $file = new File($this, $name, $options);
        $file->processData($data);
    }

    
    public function addFileFromPath(string $name, string $path, ?FileOptions $options = null): void
    {
        $options = $options ?: new FileOptions();
        $options->defaultTo($this->opt);

        $file = new File($this, $name, $options);
        $file->processPath($path);
    }

    
    public function addFileFromStream(string $name, $stream, ?FileOptions $options = null): void
    {
        $options = $options ?: new FileOptions();
        $options->defaultTo($this->opt);

        $file = new File($this, $name, $options);
        $file->processStream(new DeflateStream($stream));
    }

    
    public function addFileFromPsr7Stream(
        string $name,
        StreamInterface $stream,
        ?FileOptions $options = null
    ): void {
        $options = $options ?: new FileOptions();
        $options->defaultTo($this->opt);

        $file = new File($this, $name, $options);
        $file->processStream($stream);
    }

    
    public function finish(): void
    {
        
        foreach ($this->files as $cdrFile) {
            $this->send($cdrFile);
            $this->cdr_ofs = $this->cdr_ofs->add(Bigint::init(strlen($cdrFile)));
        }

        
        if (count($this->files) >= 0xFFFF ||
            $this->cdr_ofs->isOver32() ||
            $this->ofs->isOver32()) {
            if (!$this->opt->isEnableZip64()) {
                throw new OverflowException();
            }

            $this->addCdr64Eof();
            $this->addCdr64Locator();
        }

        
        $this->addCdrEof();

        
        $this->clear();
    }

    
    protected function addCdr64Eof(): void
    {
        $num_files = count($this->files);
        $cdr_length = $this->cdr_ofs;
        $cdr_offset = $this->ofs;

        $fields = [
            ['V', static::ZIP64_CDR_EOF_SIGNATURE],     
            ['P', 44],                                  
            ['v', static::ZIP_VERSION_MADE_BY],         
            ['v', Version::ZIP64],                      
            ['V', 0x00],                                
            ['V', 0x00],                                
            ['P', $num_files],                          
            ['P', $num_files],                          
            ['P', $cdr_length],                         
            ['P', $cdr_offset],                         
        ];

        $ret = static::packFields($fields);
        $this->send($ret);
    }

    
    public static function packFields(array $fields): string
    {
        $fmt = '';
        $args = [];

        
        foreach ($fields as [$format, $value]) {
            if ($format === 'P') {
                $fmt .= 'VV';
                if ($value instanceof Bigint) {
                    $args[] = $value->getLow32();
                    $args[] = $value->getHigh32();
                } else {
                    $args[] = $value;
                    $args[] = 0;
                }
            } else {
                if ($value instanceof Bigint) {
                    $value = $value->getLow32();
                }
                $fmt .= $format;
                $args[] = $value;
            }
        }

        
        array_unshift($args, $fmt);

        
        return pack(...$args);
    }

    
    public function send(string $str): void
    {
        if ($this->need_headers) {
            $this->sendHttpHeaders();
        }
        $this->need_headers = false;

        fwrite($this->opt->getOutputStream(), $str);

        if ($this->opt->isFlushOutput()) {
            
            $status = ob_get_status();
            if (isset($status['flags']) && ($status['flags'] & PHP_OUTPUT_HANDLER_FLUSHABLE)) {
                ob_flush();
            }

            
            flush();
        }
    }

    
    protected function sendHttpHeaders(): void
    {
        
        $disposition = $this->opt->getContentDisposition();

        if ($this->output_name) {
            
            $safe_output = trim(str_replace(['"', "'", '\\', ';', "\n", "\r"], '', $this->output_name));

            
            $urlencoded = rawurlencode($safe_output);
            $disposition .= "; filename*=UTF-8''{$urlencoded}";
        }

        $headers = array(
            'Content-Type' => $this->opt->getContentType(),
            'Content-Disposition' => $disposition,
            'Pragma' => 'public',
            'Cache-Control' => 'public, must-revalidate',
            'Content-Transfer-Encoding' => 'binary'
        );

        $call = $this->opt->getHttpHeaderCallback();
        foreach ($headers as $key => $val) {
            $call("$key: $val");
        }
    }

    
    protected function addCdr64Locator(): void
    {
        $cdr_offset = $this->ofs->add($this->cdr_ofs);

        $fields = [
            ['V', static::ZIP64_CDR_LOCATOR_SIGNATURE], 
            ['V', 0x00],                                
            ['P', $cdr_offset],                         
            ['V', 1],                                   
        ];

        $ret = static::packFields($fields);
        $this->send($ret);
    }

    
    protected function addCdrEof(): void
    {
        $num_files = count($this->files);
        $cdr_length = $this->cdr_ofs;
        $cdr_offset = $this->ofs;

        
        $comment = $this->opt->getComment();

        $fields = [
            ['V', static::CDR_EOF_SIGNATURE],   
            ['v', 0x00],                        
            ['v', 0x00],                        
            ['v', min($num_files, 0xFFFF)],     
            ['v', min($num_files, 0xFFFF)],     
            ['V', $cdr_length->getLowFF()],     
            ['V', $cdr_offset->getLowFF()],     
            ['v', strlen($comment)],            
        ];

        $ret = static::packFields($fields) . $comment;
        $this->send($ret);
    }

    
    protected function clear(): void
    {
        $this->files = [];
        $this->ofs = new Bigint();
        $this->cdr_ofs = new Bigint();
        $this->opt = new ArchiveOptions();
    }

    
    public function isLargeFile(string $path): bool
    {
        if (!$this->opt->isStatFiles()) {
            return false;
        }
        $stat = stat($path);
        return $stat['size'] > $this->opt->getLargeFileSize();
    }

    
    public function addToCdr(File $file): void
    {
        $file->ofs = $this->ofs;
        $this->ofs = $this->ofs->add($file->getTotalLength());
        $this->files[] = $file->getCdrFile();
    }
}
