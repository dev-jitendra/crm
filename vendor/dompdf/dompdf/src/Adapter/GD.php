<?php

namespace Dompdf\Adapter;

use Dompdf\Canvas;
use Dompdf\Dompdf;
use Dompdf\Helpers;
use Dompdf\Image\Cache;


class GD implements Canvas
{
    
    protected $_dompdf;

    
    protected $_img;

    
    protected $_imgs;

    
    protected $_width;

    
    protected $_height;

    
    protected $_actual_width;

    
    protected $_actual_height;

    
    protected $_page_number;

    
    protected $_page_count;

    
    protected $_aa_factor;

    
    protected $_colors;

    
    protected $_bg_color;

    
    protected $_bg_color_array;

    
    protected $dpi;

    
    const FONT_SCALE = 0.75;

    
    public function __construct($paper = "letter", string $orientation = "portrait", ?Dompdf $dompdf = null, float $aa_factor = 1.0, array $bg_color = [1, 1, 1, 0])
    {
        if (is_array($paper)) {
            $size = array_map("floatval", $paper);
        } else {
            $paper = strtolower($paper);
            $size = CPDF::$PAPER_SIZES[$paper] ?? CPDF::$PAPER_SIZES["letter"];
        }

        if (strtolower($orientation) === "landscape") {
            [$size[2], $size[3]] = [$size[3], $size[2]];
        }

        if ($dompdf === null) {
            $this->_dompdf = new Dompdf();
        } else {
            $this->_dompdf = $dompdf;
        }

        $this->dpi = $this->get_dompdf()->getOptions()->getDpi();

        if ($aa_factor < 1) {
            $aa_factor = 1;
        }

        $this->_aa_factor = $aa_factor;

        $size[2] *= $aa_factor;
        $size[3] *= $aa_factor;

        $this->_width = $size[2] - $size[0];
        $this->_height = $size[3] - $size[1];

        $this->_actual_width = $this->_upscale($this->_width);
        $this->_actual_height = $this->_upscale($this->_height);

        $this->_page_number = $this->_page_count = 0;

        if (is_null($bg_color) || !is_array($bg_color)) {
            
            $bg_color = [1, 1, 1, 0];
        }

        $this->_bg_color_array = $bg_color;

        $this->new_page();
    }

    public function get_dompdf()
    {
        return $this->_dompdf;
    }

    
    public function get_image()
    {
        return $this->_img;
    }

    
    public function get_width()
    {
        return round($this->_width / $this->_aa_factor);
    }

    
    public function get_height()
    {
        return round($this->_height / $this->_aa_factor);
    }

    public function get_page_number()
    {
        return $this->_page_number;
    }

    public function get_page_count()
    {
        return $this->_page_count;
    }

    
    public function set_page_number($num)
    {
        $this->_page_number = $num;
    }

    public function set_page_count($count)
    {
        $this->_page_count = $count;
    }

    public function set_opacity(float $opacity, string $mode = "Normal"): void
    {
        
    }

    
    protected function _allocate_color($color)
    {
        $a = isset($color["alpha"]) ? $color["alpha"] : 1;

        if (isset($color["c"])) {
            $color = Helpers::cmyk_to_rgb($color);
        }

        list($r, $g, $b) = $color;

        $r = round($r * 255);
        $g = round($g * 255);
        $b = round($b * 255);
        $a = round(127 - ($a * 127));

        
        $r = $r > 255 ? 255 : $r;
        $g = $g > 255 ? 255 : $g;
        $b = $b > 255 ? 255 : $b;
        $a = $a > 127 ? 127 : $a;

        $r = $r < 0 ? 0 : $r;
        $g = $g < 0 ? 0 : $g;
        $b = $b < 0 ? 0 : $b;
        $a = $a < 0 ? 0 : $a;

        $key = sprintf("#%02X%02X%02X%02X", $r, $g, $b, $a);

        if (isset($this->_colors[$key])) {
            return $this->_colors[$key];
        }

        if ($a != 0) {
            $this->_colors[$key] = imagecolorallocatealpha($this->get_image(), $r, $g, $b, $a);
        } else {
            $this->_colors[$key] = imagecolorallocate($this->get_image(), $r, $g, $b);
        }

        return $this->_colors[$key];
    }

    
    protected function _upscale($length)
    {
        return round(($length * $this->dpi) / 72 * $this->_aa_factor);
    }

    
    protected function _downscale($length)
    {
        return round(($length / $this->dpi * 72) / $this->_aa_factor);
    }

    protected function convertStyle(array $style, int $color, int $width): array
    {
        $gdStyle = [];

        if (count($style) === 1) {
            $style[] = $style[0];
        }

        foreach ($style as $index => $s) {
            $d = $this->_upscale($s);

            for ($i = 0; $i < $d; $i++) {
                for ($j = 0; $j < $width; $j++) {
                    $gdStyle[] = $index % 2 === 0
                        ? $color
                        : IMG_COLOR_TRANSPARENT;
                }
            }
        }

        return $gdStyle;
    }

    public function line($x1, $y1, $x2, $y2, $color, $width, $style = [], $cap = "butt")
    {
        
        
        if ($cap === "round" || $cap === "square") {
            
            $w = $width / 2;
            $a = $x2 - $x1;
            $b = $y2 - $y1;
            $c = sqrt($a ** 2 + $b ** 2);
            $dx = $a * $w / $c;
            $dy = $b * $w / $c;

            $x1 -= $dx;
            $x2 -= $dx;
            $y1 -= $dy;
            $y2 -= $dy;

            
            if (is_array($style)) {
                foreach ($style as $index => &$s) {
                    $s = $index % 2 === 0 ? $s + $width : $s - $width;
                }
            }
        }

        
        $x1 = $this->_upscale($x1);
        $y1 = $this->_upscale($y1);
        $x2 = $this->_upscale($x2);
        $y2 = $this->_upscale($y2);
        $width = $this->_upscale($width);

        $c = $this->_allocate_color($color);

        
        if (is_array($style) && count($style) > 0) {
            $gd_style = $this->convertStyle($style, $c, $width);

            if (!empty($gd_style)) {
                imagesetstyle($this->get_image(), $gd_style);
                $c = IMG_COLOR_STYLED;
            }
        }

        imagesetthickness($this->get_image(), $width);

        imageline($this->get_image(), $x1, $y1, $x2, $y2, $c);
    }

    public function arc($x, $y, $r1, $r2, $astart, $aend, $color, $width, $style = [], $cap = "butt")
    {
        
        
        if ($cap === "round" || $cap === "square") {
            
            if (is_array($style)) {
                foreach ($style as $index => &$s) {
                    $s = $index % 2 === 0 ? $s + $width : $s - $width;
                }
            }
        }

        
        $x = $this->_upscale($x);
        $y = $this->_upscale($y);
        $w = $this->_upscale($r1 * 2);
        $h = $this->_upscale($r2 * 2);
        $width = $this->_upscale($width);

        
        $start = 360 - $aend;
        $end = 360 - $astart;

        $c = $this->_allocate_color($color);

        
        if (is_array($style) && count($style) > 0) {
            $gd_style = $this->convertStyle($style, $c, $width);

            if (!empty($gd_style)) {
                imagesetstyle($this->get_image(), $gd_style);
                $c = IMG_COLOR_STYLED;
            }
        }

        imagesetthickness($this->get_image(), $width);

        imagearc($this->get_image(), $x, $y, $w, $h, $start, $end, $c);
    }

    public function rectangle($x1, $y1, $w, $h, $color, $width, $style = [], $cap = "butt")
    {
        
        
        if ($cap === "round" || $cap === "square") {
            
            if (is_array($style)) {
                foreach ($style as $index => &$s) {
                    $s = $index % 2 === 0 ? $s + $width : $s - $width;
                }
            }
        }

        
        $x1 = $this->_upscale($x1);
        $y1 = $this->_upscale($y1);
        $w = $this->_upscale($w);
        $h = $this->_upscale($h);
        $width = $this->_upscale($width);

        $c = $this->_allocate_color($color);

        
        if (is_array($style) && count($style) > 0) {
            $gd_style = $this->convertStyle($style, $c, $width);

            if (!empty($gd_style)) {
                imagesetstyle($this->get_image(), $gd_style);
                $c = IMG_COLOR_STYLED;
            }
        }

        imagesetthickness($this->get_image(), $width);

        if ($c === IMG_COLOR_STYLED) {
            imagepolygon($this->get_image(), [
                $x1, $y1,
                $x1 + $w, $y1,
                $x1 + $w, $y1 + $h,
                $x1, $y1 + $h
            ], $c);
        } else {
            imagerectangle($this->get_image(), $x1, $y1, $x1 + $w, $y1 + $h, $c);
        }
    }

    public function filled_rectangle($x1, $y1, $w, $h, $color)
    {
        
        $x1 = $this->_upscale($x1);
        $y1 = $this->_upscale($y1);
        $w = $this->_upscale($w);
        $h = $this->_upscale($h);

        $c = $this->_allocate_color($color);

        imagefilledrectangle($this->get_image(), $x1, $y1, $x1 + $w, $y1 + $h, $c);
    }

    public function clipping_rectangle($x1, $y1, $w, $h)
    {
        
    }

    public function clipping_roundrectangle($x1, $y1, $w, $h, $rTL, $rTR, $rBR, $rBL)
    {
        
    }

    public function clipping_polygon(array $points): void
    {
        
    }

    public function clipping_end()
    {
        
    }

    public function save()
    {
        $this->get_dompdf()->getOptions()->setDpi(72);
    }

    public function restore()
    {
        $this->get_dompdf()->getOptions()->setDpi($this->dpi);
    }

    public function rotate($angle, $x, $y)
    {
        
    }

    public function skew($angle_x, $angle_y, $x, $y)
    {
        
    }

    public function scale($s_x, $s_y, $x, $y)
    {
        
    }

    public function translate($t_x, $t_y)
    {
        
    }

    public function transform($a, $b, $c, $d, $e, $f)
    {
        
    }

    public function polygon($points, $color, $width = null, $style = [], $fill = false)
    {
        
        foreach (array_keys($points) as $i) {
            $points[$i] = $this->_upscale($points[$i]);
        }

        $width = isset($width) ? $this->_upscale($width) : null;

        $c = $this->_allocate_color($color);

        
        if (is_array($style) && count($style) > 0 && isset($width) && !$fill) {
            $gd_style = $this->convertStyle($style, $c, $width);

            if (!empty($gd_style)) {
                imagesetstyle($this->get_image(), $gd_style);
                $c = IMG_COLOR_STYLED;
            }
        }

        imagesetthickness($this->get_image(), isset($width) ? $width : 0);

        if ($fill) {
            imagefilledpolygon($this->get_image(), $points, $c);
        } else {
            imagepolygon($this->get_image(), $points, $c);
        }
    }

    public function circle($x, $y, $r, $color, $width = null, $style = [], $fill = false)
    {
        
        $x = $this->_upscale($x);
        $y = $this->_upscale($y);
        $d = $this->_upscale(2 * $r);
        $width = isset($width) ? $this->_upscale($width) : null;

        $c = $this->_allocate_color($color);

        
        if (is_array($style) && count($style) > 0 && isset($width) && !$fill) {
            $gd_style = $this->convertStyle($style, $c, $width);

            if (!empty($gd_style)) {
                imagesetstyle($this->get_image(), $gd_style);
                $c = IMG_COLOR_STYLED;
            }
        }

        imagesetthickness($this->get_image(), isset($width) ? $width : 0);

        if ($fill) {
            imagefilledellipse($this->get_image(), $x, $y, $d, $d, $c);
        } else {
            imageellipse($this->get_image(), $x, $y, $d, $d, $c);
        }
    }

    
    public function image($img, $x, $y, $w, $h, $resolution = "normal")
    {
        $img_type = Cache::detect_type($img, $this->get_dompdf()->getHttpContext());

        if (!$img_type) {
            return;
        }

        $func_name = "imagecreatefrom$img_type";
        if (!function_exists($func_name)) {
            if (!method_exists(Helpers::class, $func_name)) {
                throw new \Exception("Function $func_name() not found.  Cannot convert $img_type image: $img.  Please install the image PHP extension.");
            }
            $func_name = [Helpers::class, $func_name];
        }
        $src = @call_user_func($func_name, $img);

        if (!$src) {
            return; 
        }

        
        $x = $this->_upscale($x);
        $y = $this->_upscale($y);

        $w = $this->_upscale($w);
        $h = $this->_upscale($h);

        $img_w = imagesx($src);
        $img_h = imagesy($src);

        imagecopyresampled($this->get_image(), $src, $x, $y, 0, 0, $w, $h, $img_w, $img_h);
    }

    public function text($x, $y, $text, $font, $size, $color = [0, 0, 0], $word_spacing = 0.0, $char_spacing = 0.0, $angle = 0.0)
    {
        
        $x = $this->_upscale($x);
        $y = $this->_upscale($y);
        $size = $this->_upscale($size) * self::FONT_SCALE;

        $h = round($this->get_font_height_actual($font, $size));
        $c = $this->_allocate_color($color);

        
        
        
        
        $text = preg_replace('/&(#(?:x[a-fA-F0-9]+|[0-9]+);)/', '&#38;\1', $text);

        $text = mb_encode_numericentity($text, [0x0080, 0xff, 0, 0xff], 'UTF-8');

        $font = $this->get_ttf_file($font);

        
        imagettftext($this->get_image(), $size, $angle, $x, $y + $h, $c, $font, $text);
    }

    public function javascript($code)
    {
        
    }

    public function add_named_dest($anchorname)
    {
        
    }

    public function add_link($url, $x, $y, $width, $height)
    {
        
    }

    public function add_info(string $label, string $value): void
    {
        
    }

    public function set_default_view($view, $options = [])
    {
        
    }

    public function get_text_width($text, $font, $size, $word_spacing = 0.0, $char_spacing = 0.0)
    {
        $font = $this->get_ttf_file($font);
        $size = $this->_upscale($size) * self::FONT_SCALE;

        
        
        
        
        $text = preg_replace('/&(#(?:x[a-fA-F0-9]+|[0-9]+);)/', '&#38;\1', $text);

        $text = mb_encode_numericentity($text, [0x0080, 0xffff, 0, 0xffff], 'UTF-8');

        
        list($x1, , $x2) = imagettfbbox($size, 0, $font, $text);

        
        return $this->_downscale($x2 - $x1) + 1;
    }

    
    public function get_ttf_file($font)
    {
        if ($font === null) {
            $font = "";
        }

        if ( stripos($font, ".ttf") === false ) {
            $font .= ".ttf";
        }

        if (!file_exists($font)) {
            $font_metrics = $this->_dompdf->getFontMetrics();
            $font = $font_metrics->getFont($this->_dompdf->getOptions()->getDefaultFont()) . ".ttf";
            if (!file_exists($font)) {
                if (strpos($font, "mono")) {
                    $font = $font_metrics->getFont("DejaVu Mono") . ".ttf";
                } elseif (strpos($font, "sans") !== false) {
                    $font = $font_metrics->getFont("DejaVu Sans") . ".ttf";
                } elseif (strpos($font, "serif")) {
                    $font = $font_metrics->getFont("DejaVu Serif") . ".ttf";
                } else {
                    $font = $font_metrics->getFont("DejaVu Sans") . ".ttf";
                }
            }
        }

        return $font;
    }

    public function get_font_height($font, $size)
    {
        $size = $this->_upscale($size) * self::FONT_SCALE;

        $height = $this->get_font_height_actual($font, $size);

        return $this->_downscale($height);
    }

    
    protected function get_font_height_actual($font, $size)
    {
        $font = $this->get_ttf_file($font);
        $ratio = $this->_dompdf->getOptions()->getFontHeightRatio();

        
        list(, $y2, , , , $y1) = imagettfbbox($size, 0, $font, "MXjpqytfhl"); 
        return ($y2 - $y1) * $ratio;
    }

    public function get_font_baseline($font, $size)
    {
        $ratio = $this->_dompdf->getOptions()->getFontHeightRatio();
        return $this->get_font_height($font, $size) / $ratio;
    }

    public function new_page()
    {
        $this->_page_number++;
        $this->_page_count++;

        $this->_img = imagecreatetruecolor($this->_actual_width, $this->_actual_height);

        $this->_bg_color = $this->_allocate_color($this->_bg_color_array);
        imagealphablending($this->_img, true);
        imagesavealpha($this->_img, true);
        imagefill($this->_img, 0, 0, $this->_bg_color);

        $this->_imgs[] = $this->_img;
    }

    public function open_object()
    {
        
    }

    public function close_object()
    {
        
    }

    public function add_object()
    {
        
    }

    public function page_script($callback): void
    {
        
    }

    public function page_text($x, $y, $text, $font, $size, $color = [0, 0, 0], $word_space = 0.0, $char_space = 0.0, $angle = 0.0)
    {
        
    }

    public function page_line($x1, $y1, $x2, $y2, $color, $width, $style = [])
    {
        
    }

    
    public function stream($filename, $options = [])
    {
        if (headers_sent()) {
            die("Unable to stream image: headers already sent");
        }

        if (!isset($options["type"])) $options["type"] = "png";
        if (!isset($options["Attachment"])) $options["Attachment"] = true;
        $type = strtolower($options["type"]);

        switch ($type) {
            case "jpg":
            case "jpeg":
                $contentType = "image/jpeg";
                $extension = ".jpg";
                break;
            case "png":
            default:
                $contentType = "image/png";
                $extension = ".png";
                break;
        }

        header("Cache-Control: private");
        header("Content-Type: $contentType");

        $filename = str_replace(["\n", "'"], "", basename($filename, ".$type")) . $extension;
        $attachment = $options["Attachment"] ? "attachment" : "inline";
        header(Helpers::buildContentDispositionHeader($attachment, $filename));

        $this->_output($options);
        flush();
    }

    
    public function output($options = [])
    {
        ob_start();

        $this->_output($options);

        return ob_get_clean();
    }

    
    protected function _output($options = [])
    {
        if (!isset($options["type"])) $options["type"] = "png";
        if (!isset($options["page"])) $options["page"] = 1;
        $type = strtolower($options["type"]);

        if (isset($this->_imgs[$options["page"] - 1])) {
            $img = $this->_imgs[$options["page"] - 1];
        } else {
            $img = $this->_imgs[0];
        }

        
        if ($this->_aa_factor != 1) {
            $dst_w = round($this->_actual_width / $this->_aa_factor);
            $dst_h = round($this->_actual_height / $this->_aa_factor);
            $dst = imagecreatetruecolor($dst_w, $dst_h);
            imagecopyresampled($dst, $img, 0, 0, 0, 0,
                $dst_w, $dst_h,
                $this->_actual_width, $this->_actual_height);
        } else {
            $dst = $img;
        }

        switch ($type) {
            case "jpg":
            case "jpeg":
                if (!isset($options["quality"])) {
                    $options["quality"] = 75;
                }

                imagejpeg($dst, null, $options["quality"]);
                break;
            case "png":
            default:
                imagepng($dst);
                break;
        }

        if ($this->_aa_factor != 1) {
            imagedestroy($dst);
        }
    }
}
