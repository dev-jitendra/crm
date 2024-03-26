<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Manager\Style\AbstractStyleRegistry as CommonStyleRegistry;


class StyleRegistry extends CommonStyleRegistry
{
    
    private const builtinNumFormatToIdMapping = [
        'General' => 0,
        '0' => 1,
        '0.00' => 2,
        '#,##0' => 3,
        '#,##0.00' => 4,
        '$#,##0,\-$#,##0' => 5,
        '$#,##0,[Red]\-$#,##0' => 6,
        '$#,##0.00,\-$#,##0.00' => 7,
        '$#,##0.00,[Red]\-$#,##0.00' => 8,
        '0%' => 9,
        '0.00%' => 10,
        '0.00E+00' => 11,
        '# ?/?' => 12,
        '# ??/??' => 13,
        'mm-dd-yy' => 14,
        'd-mmm-yy' => 15,
        'd-mmm' => 16,
        'mmm-yy' => 17,
        'h:mm AM/PM' => 18,
        'h:mm:ss AM/PM' => 19,
        'h:mm' => 20,
        'h:mm:ss' => 21,
        'm/d/yy h:mm' => 22,

        '#,##0 ,(#,##0)' => 37,
        '#,##0 ,[Red](#,##0)' => 38,
        '#,##0.00,(#,##0.00)' => 39,
        '#,##0.00,[Red](#,##0.00)' => 40,

        '_("$"* #,##0.00_),_("$"* \(#,##0.00\),_("$"* "-"??_),_(@_)' => 44,
        'mm:ss' => 45,
        '[h]:mm:ss' => 46,
        'mm:ss.0' => 47,

        '##0.0E+0' => 48,
        '@' => 49,

        '[$-404]e/m/d' => 27,
        'm/d/yy' => 30,
        't0' => 59,
        't0.00' => 60,
        't#,##0' => 61,
        't#,##0.00' => 62,
        't0%' => 67,
        't0.00%' => 68,
        't# ?/?' => 69,
        't# ??/??' => 70,
    ];

    
    private array $registeredFormats = [];

    
    private array $styleIdToFormatsMappingTable = [];

    
    private int $formatIndex = 164;

    
    private array $registeredFills = [];

    
    private array $styleIdToFillMappingTable = [];

    
    private int $fillIndex = 2;

    
    private array $registeredBorders = [];

    
    private array $styleIdToBorderMappingTable = [];

    
    public function registerStyle(Style $style): Style
    {
        if ($style->isRegistered()) {
            return $style;
        }

        $registeredStyle = parent::registerStyle($style);
        $this->registerFill($registeredStyle);
        $this->registerFormat($registeredStyle);
        $this->registerBorder($registeredStyle);

        return $registeredStyle;
    }

    
    public function getFormatIdForStyleId(int $styleId): ?int
    {
        return $this->styleIdToFormatsMappingTable[$styleId] ?? null;
    }

    
    public function getFillIdForStyleId(int $styleId): ?int
    {
        return $this->styleIdToFillMappingTable[$styleId] ?? null;
    }

    
    public function getBorderIdForStyleId(int $styleId): ?int
    {
        return $this->styleIdToBorderMappingTable[$styleId] ?? null;
    }

    
    public function getRegisteredFills(): array
    {
        return $this->registeredFills;
    }

    
    public function getRegisteredBorders(): array
    {
        return $this->registeredBorders;
    }

    
    public function getRegisteredFormats(): array
    {
        return $this->registeredFormats;
    }

    
    private function registerFormat(Style $style): void
    {
        $styleId = $style->getId();

        $format = $style->getFormat();
        if (null !== $format) {
            $isFormatRegistered = isset($this->registeredFormats[$format]);

            
            if ($isFormatRegistered) {
                $registeredStyleId = $this->registeredFormats[$format];
                $registeredFormatId = $this->styleIdToFormatsMappingTable[$registeredStyleId];
                $this->styleIdToFormatsMappingTable[$styleId] = $registeredFormatId;
            } else {
                $this->registeredFormats[$format] = $styleId;

                $id = self::builtinNumFormatToIdMapping[$format] ?? $this->formatIndex++;
                $this->styleIdToFormatsMappingTable[$styleId] = $id;
            }
        } else {
            
            
            $this->styleIdToFormatsMappingTable[$styleId] = 0;
        }
    }

    
    private function registerFill(Style $style): void
    {
        $styleId = $style->getId();

        
        
        $backgroundColor = $style->getBackgroundColor();

        if (null !== $backgroundColor) {
            $isBackgroundColorRegistered = isset($this->registeredFills[$backgroundColor]);

            
            if ($isBackgroundColorRegistered) {
                $registeredStyleId = $this->registeredFills[$backgroundColor];
                $registeredFillId = $this->styleIdToFillMappingTable[$registeredStyleId];
                $this->styleIdToFillMappingTable[$styleId] = $registeredFillId;
            } else {
                $this->registeredFills[$backgroundColor] = $styleId;
                $this->styleIdToFillMappingTable[$styleId] = $this->fillIndex++;
            }
        } else {
            
            
            $this->styleIdToFillMappingTable[$styleId] = 0;
        }
    }

    
    private function registerBorder(Style $style): void
    {
        $styleId = $style->getId();

        if (null !== ($border = $style->getBorder())) {
            $serializedBorder = serialize($border);

            $isBorderAlreadyRegistered = isset($this->registeredBorders[$serializedBorder]);

            if ($isBorderAlreadyRegistered) {
                $registeredStyleId = $this->registeredBorders[$serializedBorder];
                $registeredBorderId = $this->styleIdToBorderMappingTable[$registeredStyleId];
                $this->styleIdToBorderMappingTable[$styleId] = $registeredBorderId;
            } else {
                $this->registeredBorders[$serializedBorder] = $styleId;
                $this->styleIdToBorderMappingTable[$styleId] = \count($this->registeredBorders);
            }
        } else {
            
            $this->styleIdToBorderMappingTable[$styleId] = 0;
        }
    }
}
