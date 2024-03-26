<?php

namespace RobThree\Auth\Providers\Time;


class HttpTimeProvider implements ITimeProvider
{
    public $url;
    public $options;
    public $expectedtimeformat;

    function __construct($url = 'https:
    {
        $this->url = $url;
        $this->expectedtimeformat = $expectedtimeformat;
        $this->options = $options;
        if ($this->options === null) {
            $this->options = array(
                'http' => array(
                    'method' => 'HEAD',
                    'follow_location' => false,
                    'ignore_errors' => true,
                    'max_redirects' => 0,
                    'request_fulluri' => true,
                    'header' => array(
                        'Connection: close',
                        'User-agent: TwoFactorAuth HttpTimeProvider (https:
                        'Cache-Control: no-cache'
                    )
                )
            );
        }
    }

    public function getTime() {
        try {
            $context  = stream_context_create($this->options);
            $fd = fopen($this->url, 'rb', false, $context);
            $headers = stream_get_meta_data($fd);
            fclose($fd);

            foreach ($headers['wrapper_data'] as $h) {
                if (strcasecmp(substr($h, 0, 5), 'Date:') === 0)
                    return \DateTime::createFromFormat($this->expectedtimeformat, trim(substr($h,5)))->getTimestamp();
            }
            throw new \TimeException(sprintf('Unable to retrieve time from %s (Invalid or no "Date:" header found)', $this->url));
        }
        catch (Exception $ex) {
            throw new \TimeException(sprintf('Unable to retrieve time from %s (%s)', $this->url, $ex->getMessage()));
        }
    }
}