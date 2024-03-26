<?php

namespace Dompdf\Css;

use DOMElement;
use DOMXPath;
use Dompdf\Dompdf;
use Dompdf\Helpers;
use Dompdf\Exception;
use Dompdf\FontMetrics;
use Dompdf\Frame\FrameTree;


class Stylesheet
{
    
    const DEFAULT_STYLESHEET = "/lib/res/html.css";

    
    const ORIG_UA = 1;

    
    const ORIG_USER = 2;

    
    const ORIG_AUTHOR = 3;

    
    private static $_stylesheet_origins = [
        self::ORIG_UA => 0x00000000, 
        self::ORIG_USER => 0x10000000, 
        self::ORIG_AUTHOR => 0x30000000, 
    ];

    
    const SPEC_NON_CSS = 0x20000000;

    
    private $_dompdf;

    
    private $_styles;

    
    private $_protocol = "";

    
    private $_base_host = "";

    
    private $_base_path = "";

    
    private $_page_styles;

    
    private $_loaded_files;

    
    private $_current_origin = self::ORIG_UA;

    
    static $ACCEPTED_DEFAULT_MEDIA_TYPE = "print";
    static $ACCEPTED_GENERIC_MEDIA_TYPES = ["all", "static", "visual", "bitmap", "paged", "dompdf"];
    static $VALID_MEDIA_TYPES = ["all", "aural", "bitmap", "braille", "dompdf", "embossed", "handheld", "paged", "print", "projection", "screen", "speech", "static", "tty", "tv", "visual"];

    
    private $fontMetrics;

    
    function __construct(Dompdf $dompdf)
    {
        $this->_dompdf = $dompdf;
        $this->setFontMetrics($dompdf->getFontMetrics());
        $this->_styles = [];
        $this->_loaded_files = [];
        $script = __FILE__;
        if (isset($_SERVER["SCRIPT_FILENAME"])) {
            $script = $_SERVER["SCRIPT_FILENAME"];
        }
        list($this->_protocol, $this->_base_host, $this->_base_path) = Helpers::explode_url($script);
        $this->_page_styles = ["base" => new Style($this)];
    }

    
    function set_protocol(string $protocol)
    {
        $this->_protocol = $protocol;
    }

    
    function set_host(string $host)
    {
        $this->_base_host = $host;
    }

    
    function set_base_path(string $path)
    {
        $this->_base_path = $path;
    }

    
    function get_dompdf()
    {
        return $this->_dompdf;
    }

    
    function get_protocol()
    {
        return $this->_protocol;
    }

    
    function get_host()
    {
        return $this->_base_host;
    }

    
    function get_base_path()
    {
        return $this->_base_path;
    }

    
    function get_page_styles()
    {
        return $this->_page_styles;
    }

    
    function create_style(): Style
    {
        return new Style($this, $this->_current_origin);
    }

    
    function add_style(string $key, Style $style): void
    {
        if (!isset($this->_styles[$key])) {
            $this->_styles[$key] = [];
        }

        $style->set_origin($this->_current_origin);
        $this->_styles[$key][] = $style;
    }

    
    function load_css(&$css, $origin = self::ORIG_AUTHOR)
    {
        if ($origin) {
            $this->_current_origin = $origin;
        }
        $this->_parse_css($css);
    }

    
    function load_css_file($file, $origin = self::ORIG_AUTHOR)
    {
        if ($origin) {
            $this->_current_origin = $origin;
        }

        
        if (isset($this->_loaded_files[$file])) {
            return;
        }

        $this->_loaded_files[$file] = true;

        if (strpos($file, "data:") === 0) {
            $parsed = Helpers::parse_data_uri($file);
            $css = $parsed["data"];
        } else {
            $options = $this->_dompdf->getOptions();

            $parsed_url = Helpers::explode_url($file);
            $protocol = $parsed_url["protocol"];

            if ($file !== $this->getDefaultStylesheet()) {
                $allowed_protocols = $options->getAllowedProtocols();
                if (!array_key_exists($protocol, $allowed_protocols)) {
                    Helpers::record_warnings(E_USER_WARNING, "Permission denied on $file. The communication protocol is not supported.", __FILE__, __LINE__);
                    return;
                }
                foreach ($allowed_protocols[$protocol]["rules"] as $rule) {
                    [$result, $message] = $rule($file);
                    if (!$result) {
                        Helpers::record_warnings(E_USER_WARNING, "Error loading $file: $message", __FILE__, __LINE__);
                        return;
                    }
                }
            }

            [$css, $http_response_header] = Helpers::getFileContent($file, $this->_dompdf->getHttpContext());

            $good_mime_type = true;

            
            if (isset($http_response_header) && !$this->_dompdf->getQuirksmode()) {
                foreach ($http_response_header as $_header) {
                    if (preg_match("@Content-Type:\s*([\w/]+)@i", $_header, $matches) &&
                        ($matches[1] !== "text/css")
                    ) {
                        $good_mime_type = false;
                    }
                }
            }
            if (!$good_mime_type || $css === null) {
                Helpers::record_warnings(E_USER_WARNING, "Unable to load css file $file", __FILE__, __LINE__);
                return;
            }

            [$this->_protocol, $this->_base_host, $this->_base_path] = $parsed_url;
        }

        $this->_parse_css($css);
    }

    
    protected function specificity(string $selector, int $origin = self::ORIG_AUTHOR): int
    {
        $a = ($selector === "!attr") ? 1 : 0;

        $b = min(mb_substr_count($selector, "#"), 255);

        $c = min(mb_substr_count($selector, ".") +
            mb_substr_count($selector, "[") +
            mb_substr_count($selector, ":") -
            2 * mb_substr_count($selector, "::"), 255);

        $d = min(mb_substr_count($selector, " ") +
            mb_substr_count($selector, ">") +
            mb_substr_count($selector, "+") +
            mb_substr_count($selector, "~") -
            mb_substr_count($selector, "~=") +
            mb_substr_count($selector, "::"), 255);

        
        
        
        
        

        if (!in_array($selector[0], [" ", ">", ".", "#", "+", "~", ":", "["], true) && $selector !== "*") {
            $d++;
        }

        if ($this->_dompdf->getOptions()->getDebugCss()) {
            
            print "<pre>\n";
            
            printf("specificity(): 0x%08x \"%s\"\n", self::$_stylesheet_origins[$origin] + (($a << 24) | ($b << 16) | ($c << 8) | ($d)), $selector);
            
            print "</pre>";
        }

        return self::$_stylesheet_origins[$origin] + (($a << 24) | ($b << 16) | ($c << 8) | ($d));
    }

    
    protected function selectorToXpath(string $selector, bool $firstPass = false): ?array
    {
        
        
        
        

        
        $query = "/";

        
        $pseudo_elements = [];

        
        

        $delimiters = [" ", ">", ".", "#", "+", "~", ":", "[", "("];

        
        
        if (!in_array($selector[0], $delimiters, true)) {
            $selector = " $selector";
        }

        $name = "*";
        $len = mb_strlen($selector);
        $i = 0;

        while ($i < $len) {

            $s = $selector[$i];
            $i++;

            
            $tok = "";
            $in_attr = false;
            $in_func = false;

            while ($i < $len) {
                $c = $selector[$i];
                $c_prev = $selector[$i - 1];

                if (!$in_func && !$in_attr && in_array($c, $delimiters, true) && !($c === $c_prev && $c === ":")) {
                    break;
                }

                if ($c_prev === "[") {
                    $in_attr = true;
                }
                if ($c_prev === "(") {
                    $in_func = true;
                }

                $tok .= $selector[$i++];

                if ($in_attr && $c === "]") {
                    $in_attr = false;
                    break;
                }
                if ($in_func && $c === ")") {
                    $in_func = false;
                    break;
                }
            }

            switch ($s) {

                case " ":
                case ">":
                    
                    
                    
                    
                    $expr = $s === " " ? "descendant" : "child";

                    
                    $name = $tok === "" ? "*" : strtolower($tok);
                    $query .= "/$expr::$name";
                    break;

                case "+":
                    
                    

                    
                    $name = $tok === "" ? "*" : strtolower($tok);
                    $query .= "/following-sibling::*[1]";

                    if ($name !== "*") {
                        $query .= "[name() = '$name']";
                    }
                    break;

                case "~":
                    
                    

                    
                    $name = $tok === "" ? "*" : strtolower($tok);
                    $query .= "/following-sibling::$name";
                    break;

                case "#":
                    
                    
                    
                    if ($query === "/") {
                        $query .= "
                        case "nth-last-child":
                            $last = true;
                        case "nth-child":
                            $p = $i + 1;
                            $nth = trim(mb_substr($selector, $p, strpos($selector, ")", $i) - $p));
                            $position = $last
                                ? "(count(following-sibling::*) + 1)"
                                : "(count(preceding-sibling::*) + 1)";

                            $condition = $this->selectorAnPlusB($nth, $position);
                            $query .= "[$condition]";
                            break;

                        
                        
                        
                        
                        case "first-of-type":
                            $query .= "[not(preceding-sibling::$name)]";
                            break;

                        case "last-of-type":
                            $query .= "[not(following-sibling::$name)]";
                            break;

                        case "only-of-type":
                            $query .= "[not(preceding-sibling::$name) and not(following-sibling::$name)]";
                            break;

                        
                        
                        case "nth-last-of-type":
                            $last = true;
                        case "nth-of-type":
                            $p = $i + 1;
                            $nth = trim(mb_substr($selector, $p, strpos($selector, ")", $i) - $p));
                            $position = $last
                                ? "(count(following-sibling::$name) + 1)"
                                : "(count(preceding-sibling::$name) + 1)";

                            $condition = $this->selectorAnPlusB($nth, $position);
                            $query .= "[$condition]";
                            break;

                        
                        case "empty":
                            $query .= "[not(*) and not(normalize-space())]";
                            break;

                        
                        case "matches":
                            $p = $i + 1;
                            $matchList = trim(mb_substr($selector, $p, strpos($selector, ")", $i) - $p));

                            
                            $elements = array_map("trim", explode(",", strtolower($matchList)));
                            foreach ($elements as &$element) {
                                $element = "name() = '$element'";
                            }

                            $query .= "[" . implode(" or ", $elements) . "]";
                            break;

                        
                        case "disabled":
                        case "checked":
                            $query .= "[@$tok]";
                            break;

                        case "enabled":
                            $query .= "[not(@disabled)]";
                            break;

                        
                        
                        case "link":
                        case "any-link":
                            $query .= "[@href]";
                            break;

                        
                        case "visited":
                        case "hover":
                        case "active":
                        case "focus":
                        case "focus-visible":
                        case "focus-within":
                            $query .= "[false()]";
                            break;

                        
                        
                        case "first-line":
                        case ":first-line":
                        case "first-letter":
                        case ":first-letter":
                            
                            $el = ltrim($tok, ":");
                            $pseudo_elements[$el] = true;
                            break;

                        
                        case "before":
                        case ":before":
                        case "after":
                        case ":after":
                            $pos = ltrim($tok, ":");
                            $pseudo_elements[$pos] = true;
                            if (!$firstPass) {
                                $query .= "
    protected function selectorAnPlusB(string $expr, string $position): string
    {
        
        if ($expr === "odd") {
            return "($position mod 2) = 1";
        } 
        elseif ($expr === "even") {
            return "($position mod 2) = 0";
        } 
        elseif (preg_match("/^\d+$/", $expr)) {
            return "$position = $expr";
        }

        
        
        $expr = preg_replace("/\s/", "", $expr);
        if (!preg_match("/^(?P<a>-?[0-9]*)?n(?P<b>[-+]?[0-9]+)?$/", $expr, $matches)) {
            return "false()";
        }

        $a = (isset($matches["a"]) && $matches["a"] !== "") ? ($matches["a"] !== "-" ? intval($matches["a"]) : -1) : 1;
        $b = (isset($matches["b"]) && $matches["b"] !== "") ? intval($matches["b"]) : 0;

        if ($b === 0) {
            return "($position mod $a) = 0";
        } else {
            $compare = ($a < 0) ? "<=" : ">=";
            $b2 = -$b;
            if ($b2 >= 0) {
                $b2 = "+$b2";
            }
            return "($position $compare $b) and ((($position $b2) mod " . abs($a) . ") = 0)";
        }
    }

    
    function apply_styles(FrameTree $tree)
    {
        
        
        
        
        

        
        
        

        

        $styles = [];
        $xp = new DOMXPath($tree->get_dom());
        $DEBUGCSS = $this->_dompdf->getOptions()->getDebugCss();

        
        foreach ($this->_styles as $selector => $selector_styles) {
            if (strpos($selector, ":before") === false && strpos($selector, ":after") === false) {
                continue;
            }

            $query = $this->selectorToXpath($selector, true);
            if ($query === null) {
                Helpers::record_warnings(E_USER_WARNING, "The CSS selector '$selector' is not valid", __FILE__, __LINE__);
                continue;
            }

            
            
            $nodes = @$xp->query('.' . $query["query"]);
            if ($nodes === false) {
                Helpers::record_warnings(E_USER_WARNING, "The CSS selector '$selector' is not valid", __FILE__, __LINE__);
                continue;
            }

            foreach ($selector_styles as $style) {
                foreach ($nodes as $node) {
                    
                    if (!($node instanceof DOMElement)) {
                        continue;
                    }

                    foreach (array_keys($query["pseudo_elements"], true, true) as $pos) {
                        
                        if ($node->hasAttribute("dompdf_{$pos}_frame_id")) {
                            continue;
                        }

                        $content = $style->get_specified("content");

                        
                        
                        
                        if ($content === "normal" || $content === "none") {
                            continue;
                        }

                        if (($src = $this->resolve_url($content)) !== "none") {
                            $new_node = $node->ownerDocument->createElement("img_generated");
                            $new_node->setAttribute("src", $src);
                        } else {
                            $new_node = $node->ownerDocument->createElement("dompdf_generated");
                        }

                        $new_node->setAttribute($pos, $pos);
                        $new_frame_id = $tree->insert_node($node, $new_node, $pos);
                        $node->setAttribute("dompdf_{$pos}_frame_id", $new_frame_id);
                    }
                }
            }
        }

        
        foreach ($this->_styles as $selector => $selector_styles) {
            $query = $this->selectorToXpath($selector);
            if ($query === null) {
                Helpers::record_warnings(E_USER_WARNING, "The CSS selector '$selector' is not valid", __FILE__, __LINE__);
                continue;
            }

            
            $nodes = @$xp->query($query["query"]);
            if ($nodes === false) {
                Helpers::record_warnings(E_USER_WARNING, "The CSS selector '$selector' is not valid", __FILE__, __LINE__);
                continue;
            }

            foreach ($selector_styles as $style) {
                $spec = $this->specificity($selector, $style->get_origin());

                foreach ($nodes as $node) {
                    
                    if (!($node instanceof DOMElement)) {
                        continue;
                    }

                    $id = $node->getAttribute("frame_id");

                    
                    $styles[$id][$spec][] = $style;
                }
            }
        }

        
        $canvas = $this->_dompdf->getCanvas();
        $paper_width = $canvas->get_width();
        $paper_height = $canvas->get_height();
        $paper_orientation = ($paper_width > $paper_height ? "landscape" : "portrait");

        if ($this->_page_styles["base"] && is_array($this->_page_styles["base"]->size)) {
            $paper_width = $this->_page_styles['base']->size[0];
            $paper_height = $this->_page_styles['base']->size[1];
            $paper_orientation = ($paper_width > $paper_height ? "landscape" : "portrait");
        }

        
        
        $root_flg = false;
        foreach ($tree as $frame) {
            
            if (!$root_flg && $this->_page_styles["base"]) {
                $style = $this->_page_styles["base"];
            } else {
                $style = $this->create_style();
            }

            
            $p = $frame;
            while ($p = $p->get_parent()) {
                if ($p->get_node()->nodeType === XML_ELEMENT_NODE) {
                    break;
                }
            }

            
            
            if ($frame->get_node()->nodeType !== XML_ELEMENT_NODE) {
                $style->inherit($p ? $p->get_style() : null);
                $frame->set_style($style);
                continue;
            }

            $id = $frame->get_id();

            
            AttributeTranslator::translate_attributes($frame);
            if (($str = $frame->get_node()->getAttribute(AttributeTranslator::$_style_attr)) !== "") {
                $styles[$id][self::SPEC_NON_CSS][] = $this->_parse_properties($str);
            }

            
            if (($str = $frame->get_node()->getAttribute("style")) !== "") {
                
                $str = preg_replace("'/\*.*?\*/'si", "", $str);

                $spec = $this->specificity("!attr", self::ORIG_AUTHOR);
                $styles[$id][$spec][] = $this->_parse_properties($str);
            }

            
            if (isset($styles[$id])) {

                
                $applied_styles = $styles[$id];

                
                ksort($applied_styles);

                if ($DEBUGCSS) {
                    $debug_nodename = $frame->get_node()->nodeName;
                    print "<pre>\n$debug_nodename [\n";
                    foreach ($applied_styles as $spec => $arr) {
                        printf("  specificity 0x%08x\n", $spec);
                        
                        foreach ($arr as $s) {
                            print "  [\n";
                            $s->debug_print();
                            print "  ]\n";
                        }
                    }
                }

                
                $acceptedmedia = self::$ACCEPTED_GENERIC_MEDIA_TYPES;
                $acceptedmedia[] = $this->_dompdf->getOptions()->getDefaultMediaType();
                foreach ($applied_styles as $arr) {
                    
                    foreach ($arr as $s) {
                        $media_queries = $s->get_media_queries();
                        foreach ($media_queries as $media_query) {
                            list($media_query_feature, $media_query_value) = $media_query;
                            
                            
                            if (in_array($media_query_feature, self::$VALID_MEDIA_TYPES)) {
                                if ((strlen($media_query_feature) === 0 && !in_array($media_query, $acceptedmedia)) || (in_array($media_query, $acceptedmedia) && $media_query_value == "not")) {
                                    continue (3);
                                }
                            } else {
                                switch ($media_query_feature) {
                                    case "height":
                                        if ($paper_height !== (float)$style->length_in_pt($media_query_value)) {
                                            continue (3);
                                        }
                                        break;
                                    case "min-height":
                                        if ($paper_height < (float)$style->length_in_pt($media_query_value)) {
                                            continue (3);
                                        }
                                        break;
                                    case "max-height":
                                        if ($paper_height > (float)$style->length_in_pt($media_query_value)) {
                                            continue (3);
                                        }
                                        break;
                                    case "width":
                                        if ($paper_width !== (float)$style->length_in_pt($media_query_value)) {
                                            continue (3);
                                        }
                                        break;
                                    case "min-width":
                                        
                                        if ($paper_width < (float)$style->length_in_pt($media_query_value)) {
                                            continue (3);
                                        }
                                        break;
                                    case "max-width":
                                        
                                        if ($paper_width > (float)$style->length_in_pt($media_query_value)) {
                                            continue (3);
                                        }
                                        break;
                                    case "orientation":
                                        if ($paper_orientation !== $media_query_value) {
                                            continue (3);
                                        }
                                        break;
                                    default:
                                        Helpers::record_warnings(E_USER_WARNING, "Unknown media query: $media_query_feature", __FILE__, __LINE__);
                                        break;
                                }
                            }
                        }

                        $style->merge($s);
                    }
                }
            }

            
            if ($p && $DEBUGCSS) {
                print "  inherit [\n";
                $p->get_style()->debug_print();
                print "  ]\n";
            }

            $style->inherit($p ? $p->get_style() : null);

            if ($DEBUGCSS) {
                print "  DomElementStyle [\n";
                $style->debug_print();
                print "  ]\n";
                print "]\n</pre>";
            }

            $style->clear_important();
            $frame->set_style($style);

            if (!$root_flg && $this->_page_styles["base"]) {
                $root_flg = true;

                
                if ($style->size !== "auto") {
                    list($paper_width, $paper_height) = $style->size;
                }
                $paper_width = $paper_width - (float)$style->length_in_pt($style->margin_left) - (float)$style->length_in_pt($style->margin_right);
                $paper_height = $paper_height - (float)$style->length_in_pt($style->margin_top) - (float)$style->length_in_pt($style->margin_bottom);
                $paper_orientation = ($paper_width > $paper_height ? "landscape" : "portrait");
            }
        }

        
        
        foreach (array_keys($this->_styles) as $key) {
            $this->_styles[$key] = null;
            unset($this->_styles[$key]);
        }
    }

    
    private function _parse_css($str)
    {
        $str = trim($str);

        
        $css = preg_replace([
            "'/\*.*?\*/'si",
            "/^<!--/",
            "/-->$/"
        ], "", $str);

        

        
        $re =
            "/\s*                                   # Skip leading whitespace                             \n" .
            "( @([^\s{]+)\s*([^{;]*) (?:;|({)) )?   # Match @rules followed by ';' or '{'                 \n" .
            "(?(1)                                  # Only parse sub-sections if we're in an @rule...     \n" .
            "  (?(4)                                # ...and if there was a leading '{'                   \n" .
            "    \s*( (?:(?>[^{}]+) ({)?            # Parse rulesets and individual @page rules           \n" .
            "            (?(6) (?>[^}]*) }) \s*)+?                                                        \n" .
            "       )                                                                                     \n" .
            "   })                                  # Balancing '}'                                       \n" .
            "|                                      # Branch to match regular rules (not preceded by '@') \n" .
            "([^{]*{[^}]*}))                        # Parse normal rulesets                               \n" .
            "/xs";

        if (preg_match_all($re, $css, $matches, PREG_SET_ORDER) === false) {
            
            throw new Exception("Error parsing css file: preg_match_all() failed.");
        }

        
        
        
        
        
        
        
        
        
        
        

        $media_query_regex = "/(?:((only|not)?\s*(" . implode("|", self::$VALID_MEDIA_TYPES) . "))|(\s*\(\s*((?:(min|max)-)?([\w\-]+))\s*(?:\:\s*(.*?)\s*)?\)))/isx";

        
        foreach ($matches as $match) {
            $match[2] = trim($match[2]);

            if ($match[2] !== "") {
                
                switch ($match[2]) {

                    case "import":
                        $this->_parse_import($match[3]);
                        break;

                    case "media":
                        $acceptedmedia = self::$ACCEPTED_GENERIC_MEDIA_TYPES;
                        $acceptedmedia[] = $this->_dompdf->getOptions()->getDefaultMediaType();

                        $media_queries = preg_split("/\s*,\s*/", mb_strtolower(trim($match[3])));
                        foreach ($media_queries as $media_query) {
                            if (in_array($media_query, $acceptedmedia)) {
                                
                                $this->_parse_sections($match[5]);
                                break;
                            } elseif (!in_array($media_query, self::$VALID_MEDIA_TYPES)) {
                                
                                if (preg_match_all($media_query_regex, $media_query, $media_query_matches, PREG_SET_ORDER) !== false) {
                                    $mq = [];
                                    foreach ($media_query_matches as $media_query_match) {
                                        if (empty($media_query_match[1]) === false) {
                                            $media_query_feature = strtolower($media_query_match[3]);
                                            $media_query_value = strtolower($media_query_match[2]);
                                            $mq[] = [$media_query_feature, $media_query_value];
                                        } elseif (empty($media_query_match[4]) === false) {
                                            $media_query_feature = strtolower($media_query_match[5]);
                                            $media_query_value = (array_key_exists(8, $media_query_match) ? strtolower($media_query_match[8]) : null);
                                            $mq[] = [$media_query_feature, $media_query_value];
                                        }
                                    }
                                    $this->_parse_sections($match[5], $mq);
                                    break;
                                }
                            }
                        }
                        break;

                    case "page":
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        

                        
                        $page_selector = trim($match[3]);

                        $key = null;
                        switch ($page_selector) {
                            case "":
                                $key = "base";
                                break;

                            case ":left":
                            case ":right":
                            case ":odd":
                            case ":even":
                            
                            case ":first":
                                $key = $page_selector;
                                break;

                            default:
                                break 2;
                        }

                        
                        if (empty($this->_page_styles[$key])) {
                            $this->_page_styles[$key] = $this->_parse_properties($match[5]);
                        } else {
                            $this->_page_styles[$key]->merge($this->_parse_properties($match[5]));
                        }
                        break;

                    case "font-face":
                        $this->_parse_font_face($match[5]);
                        break;

                    default:
                        
                        break;
                }

                continue;
            }

            if ($match[7] !== "") {
                $this->_parse_sections($match[7]);
            }
        }
    }

    
    public function resolve_url($val): string
    {
        $DEBUGCSS = $this->_dompdf->getOptions()->getDebugCss();
        $parsed_url = "none";

        if (empty($val) || $val === "none") {
            $path = "none";
        } elseif (mb_strpos($val, "url") === false) {
            $path = "none"; 
        } else {
            $val = preg_replace("/url\(\s*['\"]?([^'\")]+)['\"]?\s*\)/", "\\1", trim($val));

            
            $path = Helpers::build_url($this->_protocol,
                $this->_base_host,
                $this->_base_path,
                $val);
            if ($path === null) {
                $path = "none";
            }
        }
        if ($DEBUGCSS) {
            $parsed_url = Helpers::explode_url($path);
            print "<pre>[_image\n";
            print_r($parsed_url);
            print $this->_protocol . "\n" . $this->_base_path . "\n" . $path . "\n";
            print "_image]</pre>";
        }
        return $path;
    }

    
    private function _parse_import($url)
    {
        $arr = preg_split("/[\s\n,]/", $url, -1, PREG_SPLIT_NO_EMPTY);
        $url = array_shift($arr);
        $accept = false;

        if (count($arr) > 0) {
            $acceptedmedia = self::$ACCEPTED_GENERIC_MEDIA_TYPES;
            $acceptedmedia[] = $this->_dompdf->getOptions()->getDefaultMediaType();

            
            foreach ($arr as $type) {
                if (in_array(mb_strtolower(trim($type)), $acceptedmedia)) {
                    $accept = true;
                    break;
                }
            }

        } else {
            
            $accept = true;
        }

        if ($accept) {
            
            $protocol = $this->_protocol;
            $host = $this->_base_host;
            $path = $this->_base_path;

            
            
            
            
            

            if (($url = $this->resolve_url($url)) !== "none") {
                $this->load_css_file($url);
            }

            
            $this->_protocol = $protocol;
            $this->_base_host = $host;
            $this->_base_path = $path;
        }
    }

    
    private function _parse_font_face($str)
    {
        $descriptors = $this->_parse_properties($str);

        preg_match_all("/(url|local)\s*\(\s*[\"\']?([^\"\'\)]+)[\"\']?\s*\)\s*(format\s*\(\s*[\"\']?([^\"\'\)]+)[\"\']?\s*\))?/i", $descriptors->src, $src);

        $valid_sources = [];
        foreach ($src[0] as $i => $value) {
            $source = [
                "local" => strtolower($src[1][$i]) === "local",
                "uri" => $src[2][$i],
                "format" => strtolower($src[4][$i]),
                "path" => Helpers::build_url($this->_protocol, $this->_base_host, $this->_base_path, $src[2][$i]),
            ];

            if (!$source["local"] && in_array($source["format"], ["", "truetype"]) && $source["path"] !== null) {
                $valid_sources[] = $source;
            }
        }

        
        if (empty($valid_sources)) {
            return;
        }

        $style = [
            "family" => $descriptors->get_font_family_raw(),
            "weight" => $descriptors->font_weight,
            "style" => $descriptors->font_style,
        ];

        $this->getFontMetrics()->registerFont($style, $valid_sources[0]["path"], $this->_dompdf->getHttpContext());
    }

    
    private function _parse_properties($str)
    {
        $properties = preg_split("/;(?=(?:[^\(]*\([^\)]*\))*(?![^\)]*\)))/", $str);
        $DEBUGCSS = $this->_dompdf->getOptions()->getDebugCss();

        if ($DEBUGCSS) {
            print '[_parse_properties';
        }

        
        $style = new Style($this, Stylesheet::ORIG_AUTHOR);

        foreach ($properties as $prop) {
            
            
            

            
            

            
            if ($DEBUGCSS) print '(';

            $important = false;
            $prop = trim($prop);

            if (substr($prop, -9) === 'important') {
                $prop_tmp = rtrim(substr($prop, 0, -9));

                if (substr($prop_tmp, -1) === '!') {
                    $prop = rtrim(substr($prop_tmp, 0, -1));
                    $important = true;
                }
            }

            if ($prop === "") {
                if ($DEBUGCSS) print 'empty)';
                continue;
            }

            $i = mb_strpos($prop, ":");
            if ($i === false) {
                if ($DEBUGCSS) print 'novalue' . $prop . ')';
                continue;
            }

            $prop_name = rtrim(mb_strtolower(mb_substr($prop, 0, $i)));
            $value = ltrim(mb_substr($prop, $i + 1));

            if ($DEBUGCSS) print $prop_name . ':=' . $value . ($important ? '!IMPORTANT' : '') . ')';

            $style->set_prop($prop_name, $value, $important, false);
        }
        if ($DEBUGCSS) print '_parse_properties]';

        return $style;
    }

    
    private function _parse_sections($str, $media_queries = [])
    {
        
        
        $patterns = ["/\s+/", "/\s+([>.:+~#])\s+/"];
        $replacements = [" ", "\\1"];
        $DEBUGCSS = $this->_dompdf->getOptions()->getDebugCss();

        $sections = explode("}", $str);
        if ($DEBUGCSS) print '[_parse_sections';
        foreach ($sections as $sect) {
            $i = mb_strpos($sect, "{");
            if ($i === false) { continue; }

            if ($DEBUGCSS) print '[section';

            $selector_str = preg_replace($patterns, $replacements, mb_substr($sect, 0, $i));
            $selectors = preg_split("/,(?![^\(]*\))/", $selector_str, 0, PREG_SPLIT_NO_EMPTY);
            $style = $this->_parse_properties(trim(mb_substr($sect, $i + 1)));

            
            foreach ($selectors as $selector) {
                $selector = trim($selector);

                if ($selector === "") {
                    if ($DEBUGCSS) print '#empty#';
                    continue;
                }
                if ($DEBUGCSS) print '#' . $selector . '#';
                

                
                if (count($media_queries) > 0) {
                    $style->set_media_queries($media_queries);
                }
                $this->add_style($selector, $style);
            }

            if ($DEBUGCSS) {
                print 'section]';
            }
        }

        if ($DEBUGCSS) {
            print "_parse_sections]\n";
        }
    }

    
    public function getDefaultStylesheet()
    {
        $options = $this->_dompdf->getOptions();
        $rootDir = realpath($options->getRootDir());
        return Helpers::build_url("file:
    }

    
    public function setFontMetrics(FontMetrics $fontMetrics)
    {
        $this->fontMetrics = $fontMetrics;
        return $this;
    }

    
    public function getFontMetrics()
    {
        return $this->fontMetrics;
    }

    
    function __toString()
    {
        $str = "";
        foreach ($this->_styles as $selector => $selector_styles) {
            foreach ($selector_styles as $style) {
                $str .= "$selector => " . $style->__toString() . "\n";
            }
        }

        return $str;
    }
}
