<?php

namespace PhpOffice\PhpSpreadsheet\Shared;




















use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\OLE\ChainedBlockStream;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;


$GLOBALS['_OLE_INSTANCES'] = [];


class OLE
{
    const OLE_PPS_TYPE_ROOT = 5;
    const OLE_PPS_TYPE_DIR = 1;
    const OLE_PPS_TYPE_FILE = 2;
    const OLE_DATA_SIZE_SMALL = 0x1000;
    const OLE_LONG_INT_SIZE = 4;
    const OLE_PPS_SIZE = 0x80;

    
    public $_file_handle;

    
    public $_list = [];

    
    public $root;

    
    public $bbat;

    
    public $sbat;

    
    public $bigBlockSize;

    
    public $smallBlockSize;

    
    public $bigBlockThreshold;

    
    public function read($file)
    {
        $fh = fopen($file, 'rb');
        if (!$fh) {
            throw new ReaderException("Can't open file $file");
        }
        $this->_file_handle = $fh;

        $signature = fread($fh, 8);
        if ("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" != $signature) {
            throw new ReaderException("File doesn't seem to be an OLE container.");
        }
        fseek($fh, 28);
        if (fread($fh, 2) != "\xFE\xFF") {
            
            throw new ReaderException('Only Little-Endian encoding is supported.');
        }
        
        $this->bigBlockSize = 2 ** self::readInt2($fh);
        $this->smallBlockSize = 2 ** self::readInt2($fh);

        
        fseek($fh, 44);
        
        $bbatBlockCount = self::readInt4($fh);

        
        $directoryFirstBlockId = self::readInt4($fh);

        
        fseek($fh, 56);
        
        $this->bigBlockThreshold = self::readInt4($fh);
        
        $sbatFirstBlockId = self::readInt4($fh);
        
        $sbbatBlockCount = self::readInt4($fh);
        
        $mbatFirstBlockId = self::readInt4($fh);
        
        $mbbatBlockCount = self::readInt4($fh);
        $this->bbat = [];

        
        
        $mbatBlocks = [];
        for ($i = 0; $i < 109; ++$i) {
            $mbatBlocks[] = self::readInt4($fh);
        }

        
        $pos = $this->getBlockOffset($mbatFirstBlockId);
        for ($i = 0; $i < $mbbatBlockCount; ++$i) {
            fseek($fh, $pos);
            for ($j = 0; $j < $this->bigBlockSize / 4 - 1; ++$j) {
                $mbatBlocks[] = self::readInt4($fh);
            }
            
            $pos = $this->getBlockOffset(self::readInt4($fh));
        }

        
        for ($i = 0; $i < $bbatBlockCount; ++$i) {
            $pos = $this->getBlockOffset($mbatBlocks[$i]);
            fseek($fh, $pos);
            for ($j = 0; $j < $this->bigBlockSize / 4; ++$j) {
                $this->bbat[] = self::readInt4($fh);
            }
        }

        
        $this->sbat = [];
        $shortBlockCount = $sbbatBlockCount * $this->bigBlockSize / 4;
        $sbatFh = $this->getStream($sbatFirstBlockId);
        for ($blockId = 0; $blockId < $shortBlockCount; ++$blockId) {
            $this->sbat[$blockId] = self::readInt4($sbatFh);
        }
        fclose($sbatFh);

        $this->readPpsWks($directoryFirstBlockId);

        return true;
    }

    
    public function getBlockOffset($blockId)
    {
        return 512 + $blockId * $this->bigBlockSize;
    }

    
    public function getStream($blockIdOrPps)
    {
        static $isRegistered = false;
        if (!$isRegistered) {
            stream_wrapper_register('ole-chainedblockstream', ChainedBlockStream::class);
            $isRegistered = true;
        }

        
        
        
        $GLOBALS['_OLE_INSTANCES'][] = $this;
        $instanceId = end(array_keys($GLOBALS['_OLE_INSTANCES']));

        $path = 'ole-chainedblockstream:
        if ($blockIdOrPps instanceof OLE\PPS) {
            $path .= '&blockId=' . $blockIdOrPps->startBlock;
            $path .= '&size=' . $blockIdOrPps->Size;
        } else {
            $path .= '&blockId=' . $blockIdOrPps;
        }

        return fopen($path, 'rb');
    }

    
    private static function readInt1($fh)
    {
        [, $tmp] = unpack('c', fread($fh, 1));

        return $tmp;
    }

    
    private static function readInt2($fh)
    {
        [, $tmp] = unpack('v', fread($fh, 2));

        return $tmp;
    }

    
    private static function readInt4($fh)
    {
        [, $tmp] = unpack('V', fread($fh, 4));

        return $tmp;
    }

    
    public function readPpsWks($blockId)
    {
        $fh = $this->getStream($blockId);
        for ($pos = 0; true; $pos += 128) {
            fseek($fh, $pos, SEEK_SET);
            $nameUtf16 = fread($fh, 64);
            $nameLength = self::readInt2($fh);
            $nameUtf16 = substr($nameUtf16, 0, $nameLength - 2);
            
            $name = str_replace("\x00", '', $nameUtf16);
            $type = self::readInt1($fh);
            switch ($type) {
                case self::OLE_PPS_TYPE_ROOT:
                    $pps = new OLE\PPS\Root(null, null, []);
                    $this->root = $pps;

                    break;
                case self::OLE_PPS_TYPE_DIR:
                    $pps = new OLE\PPS(null, null, null, null, null, null, null, null, null, []);

                    break;
                case self::OLE_PPS_TYPE_FILE:
                    $pps = new OLE\PPS\File($name);

                    break;
                default:
                    break;
            }
            fseek($fh, 1, SEEK_CUR);
            $pps->Type = $type;
            $pps->Name = $name;
            $pps->PrevPps = self::readInt4($fh);
            $pps->NextPps = self::readInt4($fh);
            $pps->DirPps = self::readInt4($fh);
            fseek($fh, 20, SEEK_CUR);
            $pps->Time1st = self::OLE2LocalDate(fread($fh, 8));
            $pps->Time2nd = self::OLE2LocalDate(fread($fh, 8));
            $pps->startBlock = self::readInt4($fh);
            $pps->Size = self::readInt4($fh);
            $pps->No = count($this->_list);
            $this->_list[] = $pps;

            
            if (isset($this->root) && $this->ppsTreeComplete($this->root->No)) {
                break;
            }
        }
        fclose($fh);

        
        foreach ($this->_list as $pps) {
            if ($pps->Type == self::OLE_PPS_TYPE_DIR || $pps->Type == self::OLE_PPS_TYPE_ROOT) {
                $nos = [$pps->DirPps];
                $pps->children = [];
                while ($nos) {
                    $no = array_pop($nos);
                    if ($no != -1) {
                        $childPps = $this->_list[$no];
                        $nos[] = $childPps->PrevPps;
                        $nos[] = $childPps->NextPps;
                        $pps->children[] = $childPps;
                    }
                }
            }
        }

        return true;
    }

    
    private function ppsTreeComplete($index)
    {
        return isset($this->_list[$index]) &&
            ($pps = $this->_list[$index]) &&
            ($pps->PrevPps == -1 ||
                $this->ppsTreeComplete($pps->PrevPps)) &&
            ($pps->NextPps == -1 ||
                $this->ppsTreeComplete($pps->NextPps)) &&
            ($pps->DirPps == -1 ||
                $this->ppsTreeComplete($pps->DirPps));
    }

    
    public function isFile($index)
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Type == self::OLE_PPS_TYPE_FILE;
        }

        return false;
    }

    
    public function isRoot($index)
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Type == self::OLE_PPS_TYPE_ROOT;
        }

        return false;
    }

    
    public function ppsTotal()
    {
        return count($this->_list);
    }

    
    public function getData($index, $position, $length)
    {
        
        if (!isset($this->_list[$index]) || ($position >= $this->_list[$index]->Size) || ($position < 0)) {
            return '';
        }
        $fh = $this->getStream($this->_list[$index]);
        $data = stream_get_contents($fh, $length, $position);
        fclose($fh);

        return $data;
    }

    
    public function getDataLength($index)
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Size;
        }

        return 0;
    }

    
    public static function ascToUcs($ascii)
    {
        $rawname = '';
        $iMax = strlen($ascii);
        for ($i = 0; $i < $iMax; ++$i) {
            $rawname .= $ascii[$i]
                . "\x00";
        }

        return $rawname;
    }

    
    public static function localDateToOLE($date)
    {
        if (!isset($date)) {
            return "\x00\x00\x00\x00\x00\x00\x00\x00";
        }

        
        $factor = 2 ** 32;

        
        $days = 134774;
        
        $big_date = $days * 24 * 3600 + mktime((int) date('H', $date), (int) date('i', $date), (int) date('s', $date), (int) date('m', $date), (int) date('d', $date), (int) date('Y', $date));
        
        $big_date *= 10000000;

        $high_part = floor($big_date / $factor);
        
        $low_part = floor((($big_date / $factor) - $high_part) * $factor);

        
        $res = '';

        for ($i = 0; $i < 4; ++$i) {
            $hex = $low_part % 0x100;
            $res .= pack('c', $hex);
            $low_part /= 0x100;
        }
        for ($i = 0; $i < 4; ++$i) {
            $hex = $high_part % 0x100;
            $res .= pack('c', $hex);
            $high_part /= 0x100;
        }

        return $res;
    }

    
    public static function OLE2LocalDate($oleTimestamp)
    {
        if (strlen($oleTimestamp) != 8) {
            throw new ReaderException('Expecting 8 byte string');
        }

        
        $unpackedTimestamp = unpack('v4', $oleTimestamp);
        $timestampHigh = (float) $unpackedTimestamp[4] * 65536 + (float) $unpackedTimestamp[3];
        $timestampLow = (float) $unpackedTimestamp[2] * 65536 + (float) $unpackedTimestamp[1];

        
        $timestampHigh /= 10000000;
        $timestampLow /= 10000000;

        
        $days = 134774;

        
        $unixTimestamp = floor(65536.0 * 65536.0 * $timestampHigh + $timestampLow - $days * 24 * 3600 + 0.5);

        $iTimestamp = (int) $unixTimestamp;

        
        return ($iTimestamp == $unixTimestamp) ? $iTimestamp : ($unixTimestamp >= 0.0 ? PHP_INT_MAX : PHP_INT_MIN);
    }
}
