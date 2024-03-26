<?php
declare(strict_types=1);

namespace ZipStream;

use Psr\Http\Message\StreamInterface;
use ZipStream\Exception\EncodingException;
use ZipStream\Exception\FileNotFoundException;
use ZipStream\Exception\FileNotReadableException;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\File as FileOptions;
use ZipStream\Option\Method;
use ZipStream\Option\Version;

class File
{
    const HASH_ALGORITHM = 'crc32b';

    const BIT_ZERO_HEADER = 0x0008;
    const BIT_EFS_UTF8 = 0x0800;

    const COMPUTE = 1;
    const SEND = 2;

    private const CHUNKED_READ_BLOCK_SIZE = 1048576;

    
    public $name;

    
    public $opt;

    
    public $len;
    
    public $zlen;

    
    public $crc;

    
    public $hlen;

    
    public $ofs;

    
    public $bits;

    
    public $version;

    
    public $zip;

    
    private $deflate;
    
    private $hash;

    
    private $method;

    
    private $totalLength;

    public function __construct(ZipStream $zip, string $name, ?FileOptions $opt = null)
    {
        $this->zip = $zip;

        $this->name = $name;
        $this->opt = $opt ?: new FileOptions();
        $this->method = $this->opt->getMethod();
        $this->version = Version::STORE();
        $this->ofs = new Bigint();
    }

    public function processPath(string $path): void
    {
        if (!is_readable($path)) {
            if (!file_exists($path)) {
                throw new FileNotFoundException($path);
            }
            throw new FileNotReadableException($path);
        }
        if ($this->zip->isLargeFile($path) === false) {
            $data = file_get_contents($path);
            $this->processData($data);
        } else {
            $this->method = $this->zip->opt->getLargeFileMethod();

            $stream = new DeflateStream(fopen($path, 'rb'));
            $this->processStream($stream);
            $stream->close();
        }
    }

    public function processData(string $data): void
    {
        $this->len = new Bigint(strlen($data));
        $this->crc = crc32($data);

        
        if ($this->method->equals(Method::DEFLATE())) {
            $data = gzdeflate($data);
        }

        $this->zlen = new Bigint(strlen($data));
        $this->addFileHeader();
        $this->zip->send($data);
        $this->addFileFooter();
    }

    
    public function addFileHeader(): void
    {
        $name = static::filterFilename($this->name);

        
        $nameLength = strlen($name);

        
        $time = static::dosTime($this->opt->getTime()->getTimestamp());

        $comment = $this->opt->getComment();

        if (!mb_check_encoding($name, 'ASCII') ||
            !mb_check_encoding($comment, 'ASCII')) {
            
            
            
            if (!mb_check_encoding($name, 'UTF-8') ||
                !mb_check_encoding($comment, 'UTF-8')) {
                throw new EncodingException(
                    'File name and comment should use UTF-8 ' .
                    'if one of them does not fit into ASCII range.'
                );
            }
            $this->bits |= self::BIT_EFS_UTF8;
        }

        if ($this->method->equals(Method::DEFLATE())) {
            $this->version = Version::DEFLATE();
        }

        $force = (boolean)($this->bits & self::BIT_ZERO_HEADER) &&
            $this->zip->opt->isEnableZip64();

        $footer = $this->buildZip64ExtraBlock($force);

        
        
        
        if ($this->zip->ofs->isOver32()) {
            $this->version = Version::ZIP64();
        }

        $fields = [
            ['V', ZipStream::FILE_HEADER_SIGNATURE],
            ['v', $this->version->getValue()],      
            ['v', $this->bits],                     
            ['v', $this->method->getValue()],       
            ['V', $time],                           
            ['V', $this->crc],                      
            ['V', $this->zlen->getLowFF($force)],   
            ['V', $this->len->getLowFF($force)],    
            ['v', $nameLength],                     
            ['v', strlen($footer)],                 
        ];

        
        $header = ZipStream::packFields($fields);

        
        $data = $header . $name . $footer;
        $this->zip->send($data);

        
        $this->hlen = Bigint::init(strlen($data));
    }

    
    public static function filterFilename(string $filename): string
    {
        
        
        $filename = preg_replace('/^\\/+/', '', $filename);

        return str_replace(['\\', ':', '*', '?', '"', '<', '>', '|'], '_', $filename);
    }

    
    final protected static function dosTime(int $when): int
    {
        
        $d = getdate($when);

        
        if ($d['year'] < 1980) {
            $d = array(
                'year' => 1980,
                'mon' => 1,
                'mday' => 1,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0
            );
        }

        
        $d['year'] -= 1980;

        
        return
            ($d['year'] << 25) |
            ($d['mon'] << 21) |
            ($d['mday'] << 16) |
            ($d['hours'] << 11) |
            ($d['minutes'] << 5) |
            ($d['seconds'] >> 1);
    }

    protected function buildZip64ExtraBlock(bool $force = false): string
    {

        $fields = [];
        if ($this->len->isOver32($force)) {
            $fields[] = ['P', $this->len];          
        }

        if ($this->len->isOver32($force)) {
            $fields[] = ['P', $this->zlen];         
        }

        if ($this->ofs->isOver32()) {
            $fields[] = ['P', $this->ofs];          
        }

        if (!empty($fields)) {
            if (!$this->zip->opt->isEnableZip64()) {
                throw new OverflowException();
            }

            array_unshift(
                $fields,
                ['v', 0x0001],                      
                ['v', count($fields) * 8]             
            );
            $this->version = Version::ZIP64();
        }

        return ZipStream::packFields($fields);
    }

    

    public function addFileFooter(): void
    {

        if ($this->bits & self::BIT_ZERO_HEADER) {
            
            $sizeFormat = 'V';
            if ($this->zip->opt->isEnableZip64()) {
                $sizeFormat = 'P';
            }
            $fields = [
                ['V', ZipStream::DATA_DESCRIPTOR_SIGNATURE],
                ['V', $this->crc],              
                [$sizeFormat, $this->zlen],     
                [$sizeFormat, $this->len],      
            ];

            $footer = ZipStream::packFields($fields);
            $this->zip->send($footer);
        } else {
            $footer = '';
        }
        $this->totalLength = $this->hlen->add($this->zlen)->add(Bigint::init(strlen($footer)));
        $this->zip->addToCdr($this);
    }

    public function processStream(StreamInterface $stream): void
    {
        $this->zlen = new Bigint();
        $this->len = new Bigint();

        if ($this->zip->opt->isZeroHeader()) {
            $this->processStreamWithZeroHeader($stream);
        } else {
            $this->processStreamWithComputedHeader($stream);
        }
    }

    protected function processStreamWithZeroHeader(StreamInterface $stream): void
    {
        $this->bits |= self::BIT_ZERO_HEADER;
        $this->addFileHeader();
        $this->readStream($stream, self::COMPUTE | self::SEND);
        $this->addFileFooter();
    }

    protected function readStream(StreamInterface $stream, ?int $options = null): void
    {
        $this->deflateInit();
        $total = 0;
        $size = $this->opt->getSize();
        while (!$stream->eof() && ($size === 0 || $total < $size)) {
            $data = $stream->read(self::CHUNKED_READ_BLOCK_SIZE);
            $total += strlen($data);
            if ($size > 0 && $total > $size) {
                $data = substr($data, 0 , strlen($data)-($total - $size));
            }
            $this->deflateData($stream, $data, $options);
            if ($options & self::SEND) {
                $this->zip->send($data);
            }
        }
        $this->deflateFinish($options);
    }

    protected function deflateInit(): void
    {
        $this->hash = hash_init(self::HASH_ALGORITHM);
        if ($this->method->equals(Method::DEFLATE())) {
            $this->deflate = deflate_init(
                ZLIB_ENCODING_RAW,
                ['level' => $this->opt->getDeflateLevel()]
            );
        }
    }

    protected function deflateData(StreamInterface $stream, string &$data, ?int $options = null): void
    {
        if ($options & self::COMPUTE) {
            $this->len = $this->len->add(Bigint::init(strlen($data)));
            hash_update($this->hash, $data);
        }
        if ($this->deflate) {
            $data = deflate_add(
                $this->deflate,
                $data,
                $stream->eof()
                    ? ZLIB_FINISH
                    : ZLIB_NO_FLUSH
            );
        }
        if ($options & self::COMPUTE) {
            $this->zlen = $this->zlen->add(Bigint::init(strlen($data)));
        }
    }

    protected function deflateFinish(?int $options = null): void
    {
        if ($options & self::COMPUTE) {
            $this->crc = hexdec(hash_final($this->hash));
        }
    }

    protected function processStreamWithComputedHeader(StreamInterface $stream): void
    {
        $this->readStream($stream, self::COMPUTE);
        $stream->rewind();

        
        
        
        if (!$this->deflate && $stream instanceof DeflateStream && $this->method->equals(Method::DEFLATE())) {
            $stream->addDeflateFilter($this->opt);
            $this->zlen = new Bigint();
            while (!$stream->eof()) {
                $data = $stream->read(self::CHUNKED_READ_BLOCK_SIZE);
                $this->zlen = $this->zlen->add(Bigint::init(strlen($data)));
            }
            $stream->rewind();
        }

        $this->addFileHeader();
        $this->readStream($stream, self::SEND);
        $this->addFileFooter();
    }

    
    public function getCdrFile(): string
    {
        $name = static::filterFilename($this->name);

        
        $comment = $this->opt->getComment();

        
        $time = static::dosTime($this->opt->getTime()->getTimestamp());

        $footer = $this->buildZip64ExtraBlock();

        $fields = [
            ['V', ZipStream::CDR_FILE_SIGNATURE],   
            ['v', ZipStream::ZIP_VERSION_MADE_BY],  
            ['v', $this->version->getValue()],      
            ['v', $this->bits],                     
            ['v', $this->method->getValue()],       
            ['V', $time],                           
            ['V', $this->crc],                      
            ['V', $this->zlen->getLowFF()],         
            ['V', $this->len->getLowFF()],          
            ['v', strlen($name)],                   
            ['v', strlen($footer)],                 
            ['v', strlen($comment)],                
            ['v', 0],                               
            ['v', 0],                               
            ['V', 32],                              
            ['V', $this->ofs->getLowFF()]           
        ];

        
        $header = ZipStream::packFields($fields);

        return $header . $name . $footer . $comment;
    }

    
    public function getTotalLength(): Bigint
    {
        return $this->totalLength;
    }
}
