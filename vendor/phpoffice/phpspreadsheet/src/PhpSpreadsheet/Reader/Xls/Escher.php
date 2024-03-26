<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer\SpContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE\Blip;

class Escher
{
    const DGGCONTAINER = 0xF000;
    const BSTORECONTAINER = 0xF001;
    const DGCONTAINER = 0xF002;
    const SPGRCONTAINER = 0xF003;
    const SPCONTAINER = 0xF004;
    const DGG = 0xF006;
    const BSE = 0xF007;
    const DG = 0xF008;
    const SPGR = 0xF009;
    const SP = 0xF00A;
    const OPT = 0xF00B;
    const CLIENTTEXTBOX = 0xF00D;
    const CLIENTANCHOR = 0xF010;
    const CLIENTDATA = 0xF011;
    const BLIPJPEG = 0xF01D;
    const BLIPPNG = 0xF01E;
    const SPLITMENUCOLORS = 0xF11E;
    const TERTIARYOPT = 0xF122;

    
    private $data;

    
    private $dataSize;

    
    private $pos;

    
    private $object;

    
    public function __construct($object)
    {
        $this->object = $object;
    }

    
    public function load($data)
    {
        $this->data = $data;

        
        $this->dataSize = strlen($this->data);

        $this->pos = 0;

        
        while ($this->pos < $this->dataSize) {
            
            $fbt = Xls::getUInt2d($this->data, $this->pos + 2);

            switch ($fbt) {
                case self::DGGCONTAINER:
                    $this->readDggContainer();

                    break;
                case self::DGG:
                    $this->readDgg();

                    break;
                case self::BSTORECONTAINER:
                    $this->readBstoreContainer();

                    break;
                case self::BSE:
                    $this->readBSE();

                    break;
                case self::BLIPJPEG:
                    $this->readBlipJPEG();

                    break;
                case self::BLIPPNG:
                    $this->readBlipPNG();

                    break;
                case self::OPT:
                    $this->readOPT();

                    break;
                case self::TERTIARYOPT:
                    $this->readTertiaryOPT();

                    break;
                case self::SPLITMENUCOLORS:
                    $this->readSplitMenuColors();

                    break;
                case self::DGCONTAINER:
                    $this->readDgContainer();

                    break;
                case self::DG:
                    $this->readDg();

                    break;
                case self::SPGRCONTAINER:
                    $this->readSpgrContainer();

                    break;
                case self::SPCONTAINER:
                    $this->readSpContainer();

                    break;
                case self::SPGR:
                    $this->readSpgr();

                    break;
                case self::SP:
                    $this->readSp();

                    break;
                case self::CLIENTTEXTBOX:
                    $this->readClientTextbox();

                    break;
                case self::CLIENTANCHOR:
                    $this->readClientAnchor();

                    break;
                case self::CLIENTDATA:
                    $this->readClientData();

                    break;
                default:
                    $this->readDefault();

                    break;
            }
        }

        return $this->object;
    }

    
    private function readDefault(): void
    {
        
        $verInstance = Xls::getUInt2d($this->data, $this->pos);

        
        $fbt = Xls::getUInt2d($this->data, $this->pos + 2);

        
        $recVer = (0x000F & $verInstance) >> 0;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readDggContainer(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        
        $dggContainer = new DggContainer();
        $this->object->setDggContainer($dggContainer);
        $reader = new self($dggContainer);
        $reader->load($recordData);
    }

    
    private function readDgg(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readBstoreContainer(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        
        $bstoreContainer = new BstoreContainer();
        $this->object->setBstoreContainer($bstoreContainer);
        $reader = new self($bstoreContainer);
        $reader->load($recordData);
    }

    
    private function readBSE(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        
        $BSE = new BSE();
        $this->object->addBSE($BSE);

        $BSE->setBLIPType($recInstance);

        
        $btWin32 = ord($recordData[0]);

        
        $btMacOS = ord($recordData[1]);

        
        $rgbUid = substr($recordData, 2, 16);

        
        $tag = Xls::getUInt2d($recordData, 18);

        
        $size = Xls::getInt4d($recordData, 20);

        
        $cRef = Xls::getInt4d($recordData, 24);

        
        $foDelay = Xls::getInt4d($recordData, 28);

        
        $unused1 = ord($recordData[32]);

        
        $cbName = ord($recordData[33]);

        
        $unused2 = ord($recordData[34]);

        
        $unused3 = ord($recordData[35]);

        
        $nameData = substr($recordData, 36, $cbName);

        
        $blipData = substr($recordData, 36 + $cbName);

        
        $reader = new self($BSE);
        $reader->load($blipData);
    }

    
    private function readBlipJPEG(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        $pos = 0;

        
        $rgbUid1 = substr($recordData, 0, 16);
        $pos += 16;

        
        if (in_array($recInstance, [0x046B, 0x06E3])) {
            $rgbUid2 = substr($recordData, 16, 16);
            $pos += 16;
        }

        
        $tag = ord($recordData[$pos]);
        ++$pos;

        
        $data = substr($recordData, $pos);

        $blip = new Blip();
        $blip->setData($data);

        $this->object->setBlip($blip);
    }

    
    private function readBlipPNG(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        $pos = 0;

        
        $rgbUid1 = substr($recordData, 0, 16);
        $pos += 16;

        
        if ($recInstance == 0x06E1) {
            $rgbUid2 = substr($recordData, 16, 16);
            $pos += 16;
        }

        
        $tag = ord($recordData[$pos]);
        ++$pos;

        
        $data = substr($recordData, $pos);

        $blip = new Blip();
        $blip->setData($data);

        $this->object->setBlip($blip);
    }

    
    private function readOPT(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        $this->readOfficeArtRGFOPTE($recordData, $recInstance);
    }

    
    private function readTertiaryOPT(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readSplitMenuColors(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readDgContainer(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        
        $dgContainer = new DgContainer();
        $this->object->setDgContainer($dgContainer);
        $reader = new self($dgContainer);
        $escher = $reader->load($recordData);
    }

    
    private function readDg(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readSpgrContainer(): void
    {
        

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        
        $spgrContainer = new SpgrContainer();

        if ($this->object instanceof DgContainer) {
            
            $this->object->setSpgrContainer($spgrContainer);
        } else {
            
            $this->object->addChild($spgrContainer);
        }

        $reader = new self($spgrContainer);
        $escher = $reader->load($recordData);
    }

    
    private function readSpContainer(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $spContainer = new SpContainer();
        $this->object->addChild($spContainer);

        
        $this->pos += 8 + $length;

        
        $reader = new self($spContainer);
        $escher = $reader->load($recordData);
    }

    
    private function readSpgr(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readSp(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readClientTextbox(): void
    {
        

        
        $recInstance = (0xFFF0 & Xls::getUInt2d($this->data, $this->pos)) >> 4;

        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readClientAnchor(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;

        
        $c1 = Xls::getUInt2d($recordData, 2);

        
        $startOffsetX = Xls::getUInt2d($recordData, 4);

        
        $r1 = Xls::getUInt2d($recordData, 6);

        
        $startOffsetY = Xls::getUInt2d($recordData, 8);

        
        $c2 = Xls::getUInt2d($recordData, 10);

        
        $endOffsetX = Xls::getUInt2d($recordData, 12);

        
        $r2 = Xls::getUInt2d($recordData, 14);

        
        $endOffsetY = Xls::getUInt2d($recordData, 16);

        
        $this->object->setStartCoordinates(Coordinate::stringFromColumnIndex($c1 + 1) . ($r1 + 1));

        
        $this->object->setStartOffsetX($startOffsetX);

        
        $this->object->setStartOffsetY($startOffsetY);

        
        $this->object->setEndCoordinates(Coordinate::stringFromColumnIndex($c2 + 1) . ($r2 + 1));

        
        $this->object->setEndOffsetX($endOffsetX);

        
        $this->object->setEndOffsetY($endOffsetY);
    }

    
    private function readClientData(): void
    {
        $length = Xls::getInt4d($this->data, $this->pos + 4);
        $recordData = substr($this->data, $this->pos + 8, $length);

        
        $this->pos += 8 + $length;
    }

    
    private function readOfficeArtRGFOPTE($data, $n): void
    {
        $splicedComplexData = substr($data, 6 * $n);

        
        for ($i = 0; $i < $n; ++$i) {
            
            $fopte = substr($data, 6 * $i, 6);

            
            $opid = Xls::getUInt2d($fopte, 0);

            
            $opidOpid = (0x3FFF & $opid) >> 0;

            
            $opidFBid = (0x4000 & $opid) >> 14;

            
            $opidFComplex = (0x8000 & $opid) >> 15;

            
            $op = Xls::getInt4d($fopte, 2);

            if ($opidFComplex) {
                $complexData = substr($splicedComplexData, 0, $op);
                $splicedComplexData = substr($splicedComplexData, $op);

                
                $value = $complexData;
            } else {
                
                $value = $op;
            }

            $this->object->setOPT($opidOpid, $value);
        }
    }
}
