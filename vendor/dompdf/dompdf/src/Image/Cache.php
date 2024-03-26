<?php

namespace Dompdf\Image;

use Dompdf\Options;
use Dompdf\Helpers;
use Dompdf\Exception\ImageException;


class Cache
{
    
    protected static $_cache = [];

    
    protected static $tempImages = [];

    
    protected static $svgRefs = [];

    
    public static $broken_image = "data:image/svg+xml;charset=utf8,%3C?xml version='1.0'?%3E%3Csvg width='64' height='64' xmlns='http:

    public static $error_message = "Image not found or type unknown";
    
    
    static function resolve_url($url, $protocol, $host, $base_path, Options $options)
    {
        $tempfile = null;
        $resolved_url = null;
        $type = null;
        $message = null;
        
        try {
            $full_url = Helpers::build_url($protocol, $host, $base_path, $url);

            if ($full_url === null) {
                throw new ImageException("Unable to parse image URL $url.", E_WARNING);
            }

            $parsed_url = Helpers::explode_url($full_url);
            $protocol = strtolower($parsed_url["protocol"]);
            $is_data_uri = strpos($protocol, "data:") === 0;
            
            if (!$is_data_uri) {
                $allowed_protocols = $options->getAllowedProtocols();
                if (!array_key_exists($protocol, $allowed_protocols)) {
                    throw new ImageException("Permission denied on $url. The communication protocol is not supported.", E_WARNING);
                }
                foreach ($allowed_protocols[$protocol]["rules"] as $rule) {
                    [$result, $message] = $rule($full_url);
                    if (!$result) {
                        throw new ImageException("Error loading $url: $message", E_WARNING);
                    }
                }
            }

            if ($protocol === "file:
                $resolved_url = $full_url;
            } elseif (isset(self::$_cache[$full_url])) {
                $resolved_url = self::$_cache[$full_url];
            } else {
                $tmp_dir = $options->getTempDir();
                if (($resolved_url = @tempnam($tmp_dir, "ca_dompdf_img_")) === false) {
                    throw new ImageException("Unable to create temporary image in " . $tmp_dir, E_WARNING);
                }
                $tempfile = $resolved_url;

                $image = null;
                if ($is_data_uri) {
                    if (($parsed_data_uri = Helpers::parse_data_uri($url)) !== false) {
                        $image = $parsed_data_uri["data"];
                    }
                } else {
                    list($image, $http_response_header) = Helpers::getFileContent($full_url, $options->getHttpContext());
                }

                
                if ($image === null) {
                    $msg = ($is_data_uri ? "Data-URI could not be parsed" : "Image not found");
                    throw new ImageException($msg, E_WARNING);
                }

                if (@file_put_contents($resolved_url, $image) === false) {
                    throw new ImageException("Unable to create temporary image in " . $tmp_dir, E_WARNING);
                }

                self::$_cache[$full_url] = $resolved_url;
            }

            
            if (!is_readable($resolved_url) || !filesize($resolved_url)) {
                throw new ImageException("Image not readable or empty", E_WARNING);
            }

            list($width, $height, $type) = Helpers::dompdf_getimagesize($resolved_url, $options->getHttpContext());

            if (($width && $height && in_array($type, ["gif", "png", "jpeg", "bmp", "svg","webp"], true)) === false) {
                throw new ImageException("Image type unknown", E_WARNING);
            }

            if ($type === "svg") {
                $parser = xml_parser_create("utf-8");
                xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
                xml_set_element_handler(
                    $parser,
                    function ($parser, $name, $attributes) use ($options, $parsed_url, $full_url) {
                        if (strtolower($name) === "image") {
                            if (!\array_key_exists($full_url, self::$svgRefs)) {
                                self::$svgRefs[$full_url] = [];
                            }
                            $attributes = array_change_key_case($attributes, CASE_LOWER);
                            $urls = [];
                            $urls[] = $attributes["xlink:href"] ?? "";
                            $urls[] = $attributes["href"] ?? "";
                            foreach ($urls as $url) {
                                if (empty($url)) {
                                    continue;
                                }

                                $inner_full_url = Helpers::build_url($parsed_url["protocol"], $parsed_url["host"], $parsed_url["path"], $url);
                                if (empty($inner_full_url)) {
                                    continue;
                                }
                                
                                self::detectCircularRef($full_url, $inner_full_url);
                                self::$svgRefs[$full_url][] = $inner_full_url;
                                [$resolved_url, $type, $message] = self::resolve_url($url, $parsed_url["protocol"], $parsed_url["host"], $parsed_url["path"], $options);
                                if (!empty($message)) {
                                    throw new ImageException("This SVG document references a restricted resource. $message", E_WARNING);
                                }
                            }
                        }
                    },
                    false
                );
        
                if (($fp = fopen($resolved_url, "r")) !== false) {
                    while ($line = fread($fp, 8192)) {
                        xml_parse($parser, $line, false);
                    }
                    fclose($fp);
                    xml_parse($parser, "", true);
                }
                xml_parser_free($parser);
            }
        } catch (ImageException $e) {
            if ($tempfile) {
                unlink($tempfile);
            }
            $resolved_url = self::$broken_image;
            list($width, $height, $type) = Helpers::dompdf_getimagesize($resolved_url, $options->getHttpContext());
            $message = self::$error_message;
            Helpers::record_warnings($e->getCode(), $e->getMessage() . " \n $url", $e->getFile(), $e->getLine());
            self::$_cache[$full_url] = $resolved_url;
        }

        return [$resolved_url, $type, $message];
    }

    static function detectCircularRef(string $src, string $target)
    {
        if (!\array_key_exists($target, self::$svgRefs)) {
            return;
        }
        foreach (self::$svgRefs[$target] as $ref) {
            if ($ref === $src) {
                throw new ImageException("Circular external SVG image reference detected.", E_WARNING);
            }
            self::detectCircularRef($src, $ref);
        }
    }

    
    static function addTempImage(string $filePath, string $tempPath, string $key = "default"): void
    {
        if (!isset(self::$tempImages[$filePath])) {
            self::$tempImages[$filePath] = [];
        }

        self::$tempImages[$filePath][$key] = $tempPath;
    }

    
    static function getTempImage(string $filePath, string $key = "default"): ?string
    {
        return self::$tempImages[$filePath][$key] ?? null;
    }

    
    static function clear(bool $debugPng = false)
    {
        foreach (self::$_cache as $file) {
            if ($file === self::$broken_image) {
                continue;
            }
            if ($debugPng) {
                print "[clear unlink $file]";
            }
            if (file_exists($file)) {
                unlink($file);
            }
        }

        foreach (self::$tempImages as $versions) {
            foreach ($versions as $file) {
                if ($file === self::$broken_image) {
                    continue;
                }
                if ($debugPng) {
                    print "[unlink temp image $file]";
                }
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }

        self::$_cache = [];
        self::$tempImages = [];
        self::$svgRefs = [];
    }

    static function detect_type($file, $context = null)
    {
        list(, , $type) = Helpers::dompdf_getimagesize($file, $context);

        return $type;
    }

    static function is_broken($url)
    {
        return $url === self::$broken_image;
    }
}

if (file_exists(realpath(__DIR__ . "/../../lib/res/broken_image.svg"))) {
    Cache::$broken_image = realpath(__DIR__ . "/../../lib/res/broken_image.svg");
}
