<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class Legend
{
    
    const XL_LEGEND_POSITION_BOTTOM = -4107; 
    const XL_LEGEND_POSITION_CORNER = 2; 
    const XL_LEGEND_POSITION_CUSTOM = -4161; 
    const XL_LEGEND_POSITION_LEFT = -4131; 
    const XL_LEGEND_POSITION_RIGHT = -4152; 
    const XL_LEGEND_POSITION_TOP = -4160; 

    const POSITION_RIGHT = 'r';
    const POSITION_LEFT = 'l';
    const POSITION_BOTTOM = 'b';
    const POSITION_TOP = 't';
    const POSITION_TOPRIGHT = 'tr';

    private static $positionXLref = [
        self::XL_LEGEND_POSITION_BOTTOM => self::POSITION_BOTTOM,
        self::XL_LEGEND_POSITION_CORNER => self::POSITION_TOPRIGHT,
        self::XL_LEGEND_POSITION_CUSTOM => '??',
        self::XL_LEGEND_POSITION_LEFT => self::POSITION_LEFT,
        self::XL_LEGEND_POSITION_RIGHT => self::POSITION_RIGHT,
        self::XL_LEGEND_POSITION_TOP => self::POSITION_TOP,
    ];

    
    private $position = self::POSITION_RIGHT;

    
    private $overlay = true;

    
    private $layout;

    
    public function __construct($position = self::POSITION_RIGHT, ?Layout $layout = null, $overlay = false)
    {
        $this->setPosition($position);
        $this->layout = $layout;
        $this->setOverlay($overlay);
    }

    
    public function getPosition()
    {
        return $this->position;
    }

    
    public function setPosition($position)
    {
        if (!in_array($position, self::$positionXLref)) {
            return false;
        }

        $this->position = $position;

        return true;
    }

    
    public function getPositionXL()
    {
        return array_search($this->position, self::$positionXLref);
    }

    
    public function setPositionXL($positionXL)
    {
        if (!isset(self::$positionXLref[$positionXL])) {
            return false;
        }

        $this->position = self::$positionXLref[$positionXL];

        return true;
    }

    
    public function getOverlay()
    {
        return $this->overlay;
    }

    
    public function setOverlay($overlay)
    {
        if (!is_bool($overlay)) {
            return false;
        }

        $this->overlay = $overlay;

        return true;
    }

    
    public function getLayout()
    {
        return $this->layout;
    }
}
