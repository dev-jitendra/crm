<?php


namespace Espo\Tools\Email;

class Util
{
    static public function parseFromName(?string $string): string
    {
        $fromName = '';

        if ($string && stripos($string, '<') !== false) {
            
            $replacedString = preg_replace('/(<.*>)/', '', $string);

            $fromName = trim($replacedString, '" ');
        }

        return $fromName;
    }

    static public function parseFromAddress(?string $string): string
    {
        $fromAddress = '';

        if ($string) {
            if (stripos($string, '<') !== false) {
                if (preg_match('/<(.*)>/', $string, $matches)) {
                    $fromAddress = trim($matches[1]);
                }
            } else {
                $fromAddress = $string;
            }
        }

        return $fromAddress;
    }
}
