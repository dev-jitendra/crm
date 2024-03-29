<?php

class HTMLPurifier_Filter_YouTube extends HTMLPurifier_Filter
{

    
    public $name = 'YouTube';

    
    public function preFilter($html, $config, $context)
    {
        $pre_regex = '#<object[^>]+>.+?' .
            '(?:http:)?
        $pre_replace = '<span class="youtube-embed">\1</span>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }

    
    public function postFilter($html, $config, $context)
    {
        $post_regex = '#<span class="youtube-embed">((?:v|cp)/[A-Za-z0-9\-_=]+)</span>#';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    
    protected function armorUrl($url)
    {
        return str_replace('--', '-&#45;', $url);
    }

    
    protected function postFilterCallback($matches)
    {
        $url = $this->armorUrl($matches[1]);
        return '<object width="425" height="350" type="application/x-shockwave-flash" ' .
        'data="
        '<param name="movie" value="
        '<!--[if IE]>' .
        '<embed src="
        'type="application/x-shockwave-flash"' .
        'wmode="transparent" width="425" height="350" />' .
        '<![endif]-->' .
        '</object>';
    }
}


