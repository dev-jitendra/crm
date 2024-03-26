<?php

namespace ICal;

class Event
{
    

    const HTML_TEMPLATE = '<p>%s: %s</p>';

    
    public $summary;

    
    public $dtstart;

    
    public $dtend;

    
    public $duration;

    
    public $dtstamp;

    
    public $dtstart_tz;

    
    public $dtend_tz;

    
    public $uid;

    
    public $created;

    
    public $lastmodified;

    
    public $description;

    
    public $location;

    
    public $sequence;

    
    public $status;

    
    public $transp;

    
    public $organizer;

    
    public $attendee;

    
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            $variable = self::snakeCase($key);
            $this->{$variable} = self::prepareData($value);
        }
    }

    
    protected function prepareData($value)
    {
        if (is_string($value)) {
            return stripslashes(trim(str_replace('\n', "\n", $value)));
        } elseif (is_array($value)) {
            return array_map('self::prepareData', $value);
        }

        return $value;
    }

    
    public function printData($html = self::HTML_TEMPLATE)
    {
        $data = array(
            'SUMMARY'       => $this->summary,
            'DTSTART'       => $this->dtstart,
            'DTEND'         => $this->dtend,
            'DTSTART_TZ'    => $this->dtstart_tz,
            'DTEND_TZ'      => $this->dtend_tz,
            'DURATION'      => $this->duration,
            'DTSTAMP'       => $this->dtstamp,
            'UID'           => $this->uid,
            'CREATED'       => $this->created,
            'LAST-MODIFIED' => $this->lastmodified,
            'DESCRIPTION'   => $this->description,
            'LOCATION'      => $this->location,
            'SEQUENCE'      => $this->sequence,
            'STATUS'        => $this->status,
            'TRANSP'        => $this->transp,
            'ORGANISER'     => $this->organizer,
            'ATTENDEE(S)'   => $this->attendee,
        );

        
        $data = array_filter($data);

        $output = '';

        foreach ($data as $key => $value) {
            $output .= sprintf($html, $key, $value);
        }

        return $output;
    }

    
    protected static function snakeCase($input, $glue = '_', $separator = '-')
    {
        $input = preg_split('/(?<=[a-z])(?=[A-Z])/x', $input);
        $input = implode($glue, $input);
        $input = str_replace($separator, $glue, $input);

        return strtolower($input);
    }
}
