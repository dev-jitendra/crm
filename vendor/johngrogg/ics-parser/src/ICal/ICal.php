<?php



namespace ICal;

class ICal
{
    

    const DATE_TIME_FORMAT        = 'Ymd\THis';
    const DATE_TIME_FORMAT_PRETTY = 'F Y H:i:s';
    const ICAL_DATE_TIME_TEMPLATE = 'TZID=%s:';
    const ISO_8601_WEEK_START     = 'MO';
    const RECURRENCE_EVENT        = 'Generated recurrence event';
    const SECONDS_IN_A_WEEK       = 604800;
    const TIME_FORMAT             = 'His';
    const TIME_ZONE_UTC           = 'UTC';
    const UNIX_FORMAT             = 'U';
    const UNIX_MIN_YEAR           = 1970;

    
    public $alarmCount = 0;

    
    public $eventCount = 0;

    
    public $freeBusyCount = 0;

    
    public $todoCount = 0;

    
    public $defaultSpan = 2;

    
    public $defaultTimeZone;

    
    public $defaultWeekStart = self::ISO_8601_WEEK_START;

    
    public $skipRecurrence = false;

    
    public $disableCharacterReplacement = false;

    
    public $filterDaysBefore;

    
    public $filterDaysAfter;

    
    public $cal = array();

    
    protected $freeBusyIndex = 0;

    
    protected $lastKeyword;

    
    protected $validIanaTimeZones = array();

    
    protected $alteredRecurrenceInstances = array();

    
    protected $weekdays = array(
        'MO' => 'monday',
        'TU' => 'tuesday',
        'WE' => 'wednesday',
        'TH' => 'thursday',
        'FR' => 'friday',
        'SA' => 'saturday',
        'SU' => 'sunday',
    );

    
    protected $frequencyConversion = array(
        'DAILY'   => 'day',
        'WEEKLY'  => 'week',
        'MONTHLY' => 'month',
        'YEARLY'  => 'year',
    );

    
    protected $httpBasicAuth = array();

    
    protected $httpUserAgent;

    
    protected $httpAcceptLanguage;

    
    private static $configurableOptions = array(
        'defaultSpan',
        'defaultTimeZone',
        'defaultWeekStart',
        'disableCharacterReplacement',
        'filterDaysAfter',
        'filterDaysBefore',
        'skipRecurrence',
    );

    
    private static $cldrTimeZonesMap = array(
        '(UTC-12:00) International Date Line West'                      => 'Etc/GMT+12',
        '(UTC-11:00) Coordinated Universal Time-11'                     => 'Etc/GMT+11',
        '(UTC-10:00) Hawaii'                                            => 'Pacific/Honolulu',
        '(UTC-09:00) Alaska'                                            => 'America/Anchorage',
        '(UTC-08:00) Pacific Time (US & Canada)'                        => 'America/Los_Angeles',
        '(UTC-07:00) Arizona'                                           => 'America/Phoenix',
        '(UTC-07:00) Chihuahua, La Paz, Mazatlan'                       => 'America/Chihuahua',
        '(UTC-07:00) Mountain Time (US & Canada)'                       => 'America/Denver',
        '(UTC-06:00) Central America'                                   => 'America/Guatemala',
        '(UTC-06:00) Central Time (US & Canada)'                        => 'America/Chicago',
        '(UTC-06:00) Guadalajara, Mexico City, Monterrey'               => 'America/Mexico_City',
        '(UTC-06:00) Saskatchewan'                                      => 'America/Regina',
        '(UTC-05:00) Bogota, Lima, Quito, Rio Branco'                   => 'America/Bogota',
        '(UTC-05:00) Chetumal'                                          => 'America/Cancun',
        '(UTC-05:00) Eastern Time (US & Canada)'                        => 'America/New_York',
        '(UTC-05:00) Indiana (East)'                                    => 'America/Indianapolis',
        '(UTC-04:00) Asuncion'                                          => 'America/Asuncion',
        '(UTC-04:00) Atlantic Time (Canada)'                            => 'America/Halifax',
        '(UTC-04:00) Caracas'                                           => 'America/Caracas',
        '(UTC-04:00) Cuiaba'                                            => 'America/Cuiaba',
        '(UTC-04:00) Georgetown, La Paz, Manaus, San Juan'              => 'America/La_Paz',
        '(UTC-04:00) Santiago'                                          => 'America/Santiago',
        '(UTC-03:30) Newfoundland'                                      => 'America/St_Johns',
        '(UTC-03:00) Brasilia'                                          => 'America/Sao_Paulo',
        '(UTC-03:00) Cayenne, Fortaleza'                                => 'America/Cayenne',
        '(UTC-03:00) City of Buenos Aires'                              => 'America/Buenos_Aires',
        '(UTC-03:00) Greenland'                                         => 'America/Godthab',
        '(UTC-03:00) Montevideo'                                        => 'America/Montevideo',
        '(UTC-03:00) Salvador'                                          => 'America/Bahia',
        '(UTC-02:00) Coordinated Universal Time-02'                     => 'Etc/GMT+2',
        '(UTC-01:00) Azores'                                            => 'Atlantic/Azores',
        '(UTC-01:00) Cabo Verde Is.'                                    => 'Atlantic/Cape_Verde',
        '(UTC) Coordinated Universal Time'                              => 'Etc/GMT',
        '(UTC+00:00) Casablanca'                                        => 'Africa/Casablanca',
        '(UTC+00:00) Dublin, Edinburgh, Lisbon, London'                 => 'Europe/London',
        '(UTC+00:00) Monrovia, Reykjavik'                               => 'Atlantic/Reykjavik',
        '(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna'  => 'Europe/Berlin',
        '(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague' => 'Europe/Budapest',
        '(UTC+01:00) Brussels, Copenhagen, Madrid, Paris'               => 'Europe/Paris',
        '(UTC+01:00) Sarajevo, Skopje, Warsaw, Zagreb'                  => 'Europe/Warsaw',
        '(UTC+01:00) West Central Africa'                               => 'Africa/Lagos',
        '(UTC+02:00) Amman'                                             => 'Asia/Amman',
        '(UTC+02:00) Athens, Bucharest'                                 => 'Europe/Bucharest',
        '(UTC+02:00) Beirut'                                            => 'Asia/Beirut',
        '(UTC+02:00) Cairo'                                             => 'Africa/Cairo',
        '(UTC+02:00) Chisinau'                                          => 'Europe/Chisinau',
        '(UTC+02:00) Damascus'                                          => 'Asia/Damascus',
        '(UTC+02:00) Harare, Pretoria'                                  => 'Africa/Johannesburg',
        '(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius'     => 'Europe/Kiev',
        '(UTC+02:00) Jerusalem'                                         => 'Asia/Jerusalem',
        '(UTC+02:00) Kaliningrad'                                       => 'Europe/Kaliningrad',
        '(UTC+02:00) Tripoli'                                           => 'Africa/Tripoli',
        '(UTC+02:00) Windhoek'                                          => 'Africa/Windhoek',
        '(UTC+03:00) Baghdad'                                           => 'Asia/Baghdad',
        '(UTC+03:00) Istanbul'                                          => 'Europe/Istanbul',
        '(UTC+03:00) Kuwait, Riyadh'                                    => 'Asia/Riyadh',
        '(UTC+03:00) Minsk'                                             => 'Europe/Minsk',
        '(UTC+03:00) Moscow, St. Petersburg, Volgograd'                 => 'Europe/Moscow',
        '(UTC+03:00) Nairobi'                                           => 'Africa/Nairobi',
        '(UTC+03:30) Tehran'                                            => 'Asia/Tehran',
        '(UTC+04:00) Abu Dhabi, Muscat'                                 => 'Asia/Dubai',
        '(UTC+04:00) Baku'                                              => 'Asia/Baku',
        '(UTC+04:00) Izhevsk, Samara'                                   => 'Europe/Samara',
        '(UTC+04:00) Port Louis'                                        => 'Indian/Mauritius',
        '(UTC+04:00) Tbilisi'                                           => 'Asia/Tbilisi',
        '(UTC+04:00) Yerevan'                                           => 'Asia/Yerevan',
        '(UTC+04:30) Kabul'                                             => 'Asia/Kabul',
        '(UTC+05:00) Ashgabat, Tashkent'                                => 'Asia/Tashkent',
        '(UTC+05:00) Ekaterinburg'                                      => 'Asia/Yekaterinburg',
        '(UTC+05:00) Islamabad, Karachi'                                => 'Asia/Karachi',
        '(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi'               => 'Asia/Calcutta',
        '(UTC+05:30) Sri Jayawardenepura'                               => 'Asia/Colombo',
        '(UTC+05:45) Kathmandu'                                         => 'Asia/Katmandu',
        '(UTC+06:00) Astana'                                            => 'Asia/Almaty',
        '(UTC+06:00) Dhaka'                                             => 'Asia/Dhaka',
        '(UTC+06:30) Yangon (Rangoon)'                                  => 'Asia/Rangoon',
        '(UTC+07:00) Bangkok, Hanoi, Jakarta'                           => 'Asia/Bangkok',
        '(UTC+07:00) Krasnoyarsk'                                       => 'Asia/Krasnoyarsk',
        '(UTC+07:00) Novosibirsk'                                       => 'Asia/Novosibirsk',
        '(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi'             => 'Asia/Shanghai',
        '(UTC+08:00) Irkutsk'                                           => 'Asia/Irkutsk',
        '(UTC+08:00) Kuala Lumpur, Singapore'                           => 'Asia/Singapore',
        '(UTC+08:00) Perth'                                             => 'Australia/Perth',
        '(UTC+08:00) Taipei'                                            => 'Asia/Taipei',
        '(UTC+08:00) Ulaanbaatar'                                       => 'Asia/Ulaanbaatar',
        '(UTC+09:00) Osaka, Sapporo, Tokyo'                             => 'Asia/Tokyo',
        '(UTC+09:00) Pyongyang'                                         => 'Asia/Pyongyang',
        '(UTC+09:00) Seoul'                                             => 'Asia/Seoul',
        '(UTC+09:00) Yakutsk'                                           => 'Asia/Yakutsk',
        '(UTC+09:30) Adelaide'                                          => 'Australia/Adelaide',
        '(UTC+09:30) Darwin'                                            => 'Australia/Darwin',
        '(UTC+10:00) Brisbane'                                          => 'Australia/Brisbane',
        '(UTC+10:00) Canberra, Melbourne, Sydney'                       => 'Australia/Sydney',
        '(UTC+10:00) Guam, Port Moresby'                                => 'Pacific/Port_Moresby',
        '(UTC+10:00) Hobart'                                            => 'Australia/Hobart',
        '(UTC+10:00) Vladivostok'                                       => 'Asia/Vladivostok',
        '(UTC+11:00) Chokurdakh'                                        => 'Asia/Srednekolymsk',
        '(UTC+11:00) Magadan'                                           => 'Asia/Magadan',
        '(UTC+11:00) Solomon Is., New Caledonia'                        => 'Pacific/Guadalcanal',
        '(UTC+12:00) Anadyr, Petropavlovsk-Kamchatsky'                  => 'Asia/Kamchatka',
        '(UTC+12:00) Auckland, Wellington'                              => 'Pacific/Auckland',
        '(UTC+12:00) Coordinated Universal Time+12'                     => 'Etc/GMT-12',
        '(UTC+12:00) Fiji'                                              => 'Pacific/Fiji',
        "(UTC+13:00) Nuku'alofa"                                        => 'Pacific/Tongatapu',
        '(UTC+13:00) Samoa'                                             => 'Pacific/Apia',
        '(UTC+14:00) Kiritimati Island'                                 => 'Pacific/Kiritimati',
    );

    
    private static $windowsTimeZonesMap = array(
        'AUS Central Standard Time'       => 'Australia/Darwin',
        'AUS Eastern Standard Time'       => 'Australia/Sydney',
        'Afghanistan Standard Time'       => 'Asia/Kabul',
        'Alaskan Standard Time'           => 'America/Anchorage',
        'Aleutian Standard Time'          => 'America/Adak',
        'Altai Standard Time'             => 'Asia/Barnaul',
        'Arab Standard Time'              => 'Asia/Riyadh',
        'Arabian Standard Time'           => 'Asia/Dubai',
        'Arabic Standard Time'            => 'Asia/Baghdad',
        'Argentina Standard Time'         => 'America/Buenos_Aires',
        'Astrakhan Standard Time'         => 'Europe/Astrakhan',
        'Atlantic Standard Time'          => 'America/Halifax',
        'Aus Central W. Standard Time'    => 'Australia/Eucla',
        'Azerbaijan Standard Time'        => 'Asia/Baku',
        'Azores Standard Time'            => 'Atlantic/Azores',
        'Bahia Standard Time'             => 'America/Bahia',
        'Bangladesh Standard Time'        => 'Asia/Dhaka',
        'Belarus Standard Time'           => 'Europe/Minsk',
        'Bougainville Standard Time'      => 'Pacific/Bougainville',
        'Canada Central Standard Time'    => 'America/Regina',
        'Cape Verde Standard Time'        => 'Atlantic/Cape_Verde',
        'Caucasus Standard Time'          => 'Asia/Yerevan',
        'Cen. Australia Standard Time'    => 'Australia/Adelaide',
        'Central America Standard Time'   => 'America/Guatemala',
        'Central Asia Standard Time'      => 'Asia/Almaty',
        'Central Brazilian Standard Time' => 'America/Cuiaba',
        'Central Europe Standard Time'    => 'Europe/Budapest',
        'Central European Standard Time'  => 'Europe/Warsaw',
        'Central Pacific Standard Time'   => 'Pacific/Guadalcanal',
        'Central Standard Time (Mexico)'  => 'America/Mexico_City',
        'Central Standard Time'           => 'America/Chicago',
        'Chatham Islands Standard Time'   => 'Pacific/Chatham',
        'China Standard Time'             => 'Asia/Shanghai',
        'Cuba Standard Time'              => 'America/Havana',
        'Dateline Standard Time'          => 'Etc/GMT+12',
        'E. Africa Standard Time'         => 'Africa/Nairobi',
        'E. Australia Standard Time'      => 'Australia/Brisbane',
        'E. Europe Standard Time'         => 'Europe/Chisinau',
        'E. South America Standard Time'  => 'America/Sao_Paulo',
        'Easter Island Standard Time'     => 'Pacific/Easter',
        'Eastern Standard Time (Mexico)'  => 'America/Cancun',
        'Eastern Standard Time'           => 'America/New_York',
        'Egypt Standard Time'             => 'Africa/Cairo',
        'Ekaterinburg Standard Time'      => 'Asia/Yekaterinburg',
        'FLE Standard Time'               => 'Europe/Kiev',
        'Fiji Standard Time'              => 'Pacific/Fiji',
        'GMT Standard Time'               => 'Europe/London',
        'GTB Standard Time'               => 'Europe/Bucharest',
        'Georgian Standard Time'          => 'Asia/Tbilisi',
        'Greenland Standard Time'         => 'America/Godthab',
        'Greenwich Standard Time'         => 'Atlantic/Reykjavik',
        'Haiti Standard Time'             => 'America/Port-au-Prince',
        'Hawaiian Standard Time'          => 'Pacific/Honolulu',
        'India Standard Time'             => 'Asia/Calcutta',
        'Iran Standard Time'              => 'Asia/Tehran',
        'Israel Standard Time'            => 'Asia/Jerusalem',
        'Jordan Standard Time'            => 'Asia/Amman',
        'Kaliningrad Standard Time'       => 'Europe/Kaliningrad',
        'Korea Standard Time'             => 'Asia/Seoul',
        'Libya Standard Time'             => 'Africa/Tripoli',
        'Line Islands Standard Time'      => 'Pacific/Kiritimati',
        'Lord Howe Standard Time'         => 'Australia/Lord_Howe',
        'Magadan Standard Time'           => 'Asia/Magadan',
        'Magallanes Standard Time'        => 'America/Punta_Arenas',
        'Marquesas Standard Time'         => 'Pacific/Marquesas',
        'Mauritius Standard Time'         => 'Indian/Mauritius',
        'Middle East Standard Time'       => 'Asia/Beirut',
        'Montevideo Standard Time'        => 'America/Montevideo',
        'Morocco Standard Time'           => 'Africa/Casablanca',
        'Mountain Standard Time (Mexico)' => 'America/Chihuahua',
        'Mountain Standard Time'          => 'America/Denver',
        'Myanmar Standard Time'           => 'Asia/Rangoon',
        'N. Central Asia Standard Time'   => 'Asia/Novosibirsk',
        'Namibia Standard Time'           => 'Africa/Windhoek',
        'Nepal Standard Time'             => 'Asia/Katmandu',
        'New Zealand Standard Time'       => 'Pacific/Auckland',
        'Newfoundland Standard Time'      => 'America/St_Johns',
        'Norfolk Standard Time'           => 'Pacific/Norfolk',
        'North Asia East Standard Time'   => 'Asia/Irkutsk',
        'North Asia Standard Time'        => 'Asia/Krasnoyarsk',
        'North Korea Standard Time'       => 'Asia/Pyongyang',
        'Omsk Standard Time'              => 'Asia/Omsk',
        'Pacific SA Standard Time'        => 'America/Santiago',
        'Pacific Standard Time (Mexico)'  => 'America/Tijuana',
        'Pacific Standard Time'           => 'America/Los_Angeles',
        'Pakistan Standard Time'          => 'Asia/Karachi',
        'Paraguay Standard Time'          => 'America/Asuncion',
        'Romance Standard Time'           => 'Europe/Paris',
        'Russia Time Zone 10'             => 'Asia/Srednekolymsk',
        'Russia Time Zone 11'             => 'Asia/Kamchatka',
        'Russia Time Zone 3'              => 'Europe/Samara',
        'Russian Standard Time'           => 'Europe/Moscow',
        'SA Eastern Standard Time'        => 'America/Cayenne',
        'SA Pacific Standard Time'        => 'America/Bogota',
        'SA Western Standard Time'        => 'America/La_Paz',
        'SE Asia Standard Time'           => 'Asia/Bangkok',
        'Saint Pierre Standard Time'      => 'America/Miquelon',
        'Sakhalin Standard Time'          => 'Asia/Sakhalin',
        'Samoa Standard Time'             => 'Pacific/Apia',
        'Sao Tome Standard Time'          => 'Africa/Sao_Tome',
        'Saratov Standard Time'           => 'Europe/Saratov',
        'Singapore Standard Time'         => 'Asia/Singapore',
        'South Africa Standard Time'      => 'Africa/Johannesburg',
        'Sri Lanka Standard Time'         => 'Asia/Colombo',
        'Sudan Standard Time'             => 'Africa/Tripoli',
        'Syria Standard Time'             => 'Asia/Damascus',
        'Taipei Standard Time'            => 'Asia/Taipei',
        'Tasmania Standard Time'          => 'Australia/Hobart',
        'Tocantins Standard Time'         => 'America/Araguaina',
        'Tokyo Standard Time'             => 'Asia/Tokyo',
        'Tomsk Standard Time'             => 'Asia/Tomsk',
        'Tonga Standard Time'             => 'Pacific/Tongatapu',
        'Transbaikal Standard Time'       => 'Asia/Chita',
        'Turkey Standard Time'            => 'Europe/Istanbul',
        'Turks And Caicos Standard Time'  => 'America/Grand_Turk',
        'US Eastern Standard Time'        => 'America/Indianapolis',
        'US Mountain Standard Time'       => 'America/Phoenix',
        'UTC'                             => 'Etc/GMT',
        'UTC+12'                          => 'Etc/GMT-12',
        'UTC+13'                          => 'Etc/GMT-13',
        'UTC-02'                          => 'Etc/GMT+2',
        'UTC-08'                          => 'Etc/GMT+8',
        'UTC-09'                          => 'Etc/GMT+9',
        'UTC-11'                          => 'Etc/GMT+11',
        'Ulaanbaatar Standard Time'       => 'Asia/Ulaanbaatar',
        'Venezuela Standard Time'         => 'America/Caracas',
        'Vladivostok Standard Time'       => 'Asia/Vladivostok',
        'W. Australia Standard Time'      => 'Australia/Perth',
        'W. Central Africa Standard Time' => 'Africa/Lagos',
        'W. Europe Standard Time'         => 'Europe/Berlin',
        'W. Mongolia Standard Time'       => 'Asia/Hovd',
        'West Asia Standard Time'         => 'Asia/Tashkent',
        'West Bank Standard Time'         => 'Asia/Hebron',
        'West Pacific Standard Time'      => 'Pacific/Port_Moresby',
        'Yakutsk Standard Time'           => 'Asia/Yakutsk',
    );

    
    private $windowMinTimestamp;

    
    private $windowMaxTimestamp;

    
    private $shouldFilterByWindow = false;

    
    public function __construct($files = false, array $options = array())
    {
        ini_set('auto_detect_line_endings', '1');

        foreach ($options as $option => $value) {
            if (in_array($option, self::$configurableOptions)) {
                $this->{$option} = $value;
            }
        }

        
        if (!isset($this->defaultTimeZone) || !$this->isValidTimeZoneId($this->defaultTimeZone)) {
            $this->defaultTimeZone = date_default_timezone_get();
        }

        
        $php_int_min = -2147483648;

        $this->windowMinTimestamp = is_null($this->filterDaysBefore) ? $php_int_min : (new \DateTime('now'))->sub(new \DateInterval('P' . $this->filterDaysBefore . 'D'))->getTimestamp();
        $this->windowMaxTimestamp = is_null($this->filterDaysAfter) ? PHP_INT_MAX : (new \DateTime('now'))->add(new \DateInterval('P' . $this->filterDaysAfter . 'D'))->getTimestamp();

        $this->shouldFilterByWindow = !is_null($this->filterDaysBefore) || !is_null($this->filterDaysAfter);

        if ($files !== false) {
            $files = is_array($files) ? $files : array($files);

            foreach ($files as $file) {
                if (!is_array($file) && $this->isFileOrUrl($file)) {
                    $lines = $this->fileOrUrl($file);
                } else {
                    $lines = is_array($file) ? $file : array($file);
                }

                $this->initLines($lines);
            }
        }
    }

    
    public function initString($string)
    {
        $string = str_replace(array("\r\n", "\n\r", "\r"), "\n", $string);

        if (empty($this->cal)) {
            $lines = explode("\n", $string);

            $this->initLines($lines);
        } else {
            trigger_error('ICal::initString: Calendar already initialised in constructor', E_USER_NOTICE);
        }

        return $this;
    }

    
    public function initFile($file)
    {
        if (empty($this->cal)) {
            $lines = $this->fileOrUrl($file);

            $this->initLines($lines);
        } else {
            trigger_error('ICal::initFile: Calendar already initialised in constructor', E_USER_NOTICE);
        }

        return $this;
    }

    
    public function initUrl($url, $username = null, $password = null, $userAgent = null, $acceptLanguage = null)
    {
        if (!is_null($username) && !is_null($password)) {
            $this->httpBasicAuth['username'] = $username;
            $this->httpBasicAuth['password'] = $password;
        }

        if (!is_null($userAgent)) {
            $this->httpUserAgent = $userAgent;
        }

        if (!is_null($acceptLanguage)) {
            $this->httpAcceptLanguage = $acceptLanguage;
        }

        $this->initFile($url);

        return $this;
    }

    
    protected function initLines(array $lines)
    {
        $lines = $this->unfold($lines);

        if (stristr($lines[0], 'BEGIN:VCALENDAR') !== false) {
            $component = '';
            foreach ($lines as $line) {
                $line = rtrim($line); 
                $line = $this->removeUnprintableChars($line);

                if (empty($line)) {
                    continue;
                }

                if (!$this->disableCharacterReplacement) {
                    $line = $this->cleanData($line);
                }

                $add = $this->keyValueFromString($line);

                if ($add === false) {
                    continue;
                }

                $keyword = $add[0];
                $values  = $add[1]; 

                if (!is_array($values)) {
                    if (!empty($values)) {
                        $values     = array($values); 
                        $blankArray = array(); 
                        $values[]   = $blankArray;
                    } else {
                        $values = array(); 
                    }
                } elseif (empty($values[0])) {
                    $values = array(); 
                }

                
                $values = array_reverse($values);

                foreach ($values as $value) {
                    switch ($line) {
                        
                        case 'BEGIN:VTODO':
                            if (!is_array($value)) {
                                $this->todoCount++;
                            }

                            $component = 'VTODO';

                            break;

                        
                        case 'BEGIN:VEVENT':
                            if (!is_array($value)) {
                                $this->eventCount++;
                            }

                            $component = 'VEVENT';

                            break;

                        
                        case 'BEGIN:VFREEBUSY':
                            if (!is_array($value)) {
                                $this->freeBusyIndex++;
                            }

                            $component = 'VFREEBUSY';

                            break;

                        case 'BEGIN:VALARM':
                            if (!is_array($value)) {
                                $this->alarmCount++;
                            }

                            $component = 'VALARM';

                            break;

                        case 'END:VALARM':
                            $component = 'VEVENT';

                            break;

                        case 'BEGIN:DAYLIGHT':
                        case 'BEGIN:STANDARD':
                        case 'BEGIN:VCALENDAR':
                        case 'BEGIN:VTIMEZONE':
                            $component = $value;

                            break;

                        case 'END:DAYLIGHT':
                        case 'END:STANDARD':
                        case 'END:VCALENDAR':
                        case 'END:VFREEBUSY':
                        case 'END:VTIMEZONE':
                        case 'END:VTODO':
                            $component = 'VCALENDAR';

                            break;

                        case 'END:VEVENT':
                            if ($this->shouldFilterByWindow) {
                                $this->removeLastEventIfOutsideWindowAndNonRecurring();
                            }

                            $component = 'VCALENDAR';

                            break;

                        default:
                            $this->addCalendarComponentWithKeyAndValue($component, $keyword, $value);

                            break;
                    }
                }
            }

            $this->processEvents();

            if (!$this->skipRecurrence) {
                $this->processRecurrences();

                
                if (!empty($this->alteredRecurrenceInstances)) {
                    $events = $this->cal['VEVENT'];

                    foreach ($this->alteredRecurrenceInstances as $alteredRecurrenceInstance) {
                        if (isset($alteredRecurrenceInstance['altered-event'])) {
                            $alteredEvent = $alteredRecurrenceInstance['altered-event'];
                            $key          = key($alteredEvent);
                            $events[$key] = $alteredEvent[$key];
                        }
                    }

                    $this->cal['VEVENT'] = $events;
                }
            }

            if ($this->shouldFilterByWindow) {
                $this->reduceEventsToMinMaxRange();
            }

            $this->processDateConversions();
        }
    }

    
    protected function removeLastEventIfOutsideWindowAndNonRecurring()
    {
        $events = $this->cal['VEVENT'];

        if (!empty($events)) {
            $lastIndex = count($events) - 1;
            $lastEvent = $events[$lastIndex];

            if ((!isset($lastEvent['RRULE']) || $lastEvent['RRULE'] === '') && $this->doesEventStartOutsideWindow($lastEvent)) {
                $this->eventCount--;

                unset($events[$lastIndex]);
            }

            $this->cal['VEVENT'] = $events;
        }
    }

    
    protected function reduceEventsToMinMaxRange()
    {
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        if (!empty($events)) {
            foreach ($events as $key => $anEvent) {
                if ($anEvent === null) {
                    unset($events[$key]);

                    continue;
                }

                if ($this->doesEventStartOutsideWindow($anEvent)) {
                    $this->eventCount--;

                    unset($events[$key]);

                    continue;
                }
            }

            $this->cal['VEVENT'] = $events;
        }
    }

    
    protected function doesEventStartOutsideWindow(array $event)
    {
        return !$this->isValidDate($event['DTSTART']) || $this->isOutOfRange($event['DTSTART'], $this->windowMinTimestamp, $this->windowMaxTimestamp);
    }

    
    protected function isOutOfRange($calendarDate, $minTimestamp, $maxTimestamp)
    {
        $timestamp = strtotime(explode('T', $calendarDate)[0]);

        return $timestamp < $minTimestamp || $timestamp > $maxTimestamp;
    }

    
    protected function unfold(array $lines)
    {
        $string = implode(PHP_EOL, $lines);
        $string = preg_replace('/' . PHP_EOL . '[ \t]/', '', $string);

        $lines = explode(PHP_EOL, $string);

        return $lines;
    }

    
    protected function addCalendarComponentWithKeyAndValue($component, $keyword, $value)
    {
        if ($keyword == false) {
            $keyword = $this->lastKeyword;
        }

        switch ($component) {
            case 'VALARM':
                $key1 = 'VEVENT';
                $key2 = ($this->eventCount - 1);
                $key3 = $component;

                if (!isset($this->cal[$key1][$key2][$key3]["{$keyword}_array"])) {
                    $this->cal[$key1][$key2][$key3]["{$keyword}_array"] = array();
                }

                if (is_array($value)) {
                    
                    $this->cal[$key1][$key2][$key3]["{$keyword}_array"][] = $value;
                } else {
                    if (!isset($this->cal[$key1][$key2][$key3][$keyword])) {
                        $this->cal[$key1][$key2][$key3][$keyword] = $value;
                    }

                    if ($this->cal[$key1][$key2][$key3][$keyword] !== $value) {
                        $this->cal[$key1][$key2][$key3][$keyword] .= ',' . $value;
                    }
                }
                break;

            case 'VEVENT':
                $key1 = $component;
                $key2 = ($this->eventCount - 1);

                if (!isset($this->cal[$key1][$key2]["{$keyword}_array"])) {
                    $this->cal[$key1][$key2]["{$keyword}_array"] = array();
                }

                if (is_array($value)) {
                    
                    $this->cal[$key1][$key2]["{$keyword}_array"][] = $value;
                } else {
                    if (!isset($this->cal[$key1][$key2][$keyword])) {
                        $this->cal[$key1][$key2][$keyword] = $value;
                    }

                    if ($keyword === 'EXDATE') {
                        if (trim($value) === $value) {
                            $array = array_filter(explode(',', $value));
                            $this->cal[$key1][$key2]["{$keyword}_array"][] = $array;
                        } else {
                            $value = explode(',', implode(',', $this->cal[$key1][$key2]["{$keyword}_array"][1]) . trim($value));
                            $this->cal[$key1][$key2]["{$keyword}_array"][1] = $value;
                        }
                    } else {
                        $this->cal[$key1][$key2]["{$keyword}_array"][] = $value;

                        if ($keyword === 'DURATION') {
                            $duration = new \DateInterval($value);
                            $this->cal[$key1][$key2]["{$keyword}_array"][] = $duration;
                        }
                    }

                    if ($this->cal[$key1][$key2][$keyword] !== $value) {
                        $this->cal[$key1][$key2][$keyword] .= ',' . $value;
                    }
                }
                break;

            case 'VFREEBUSY':
                $key1 = $component;
                $key2 = ($this->freeBusyIndex - 1);
                $key3 = $keyword;

                if ($keyword === 'FREEBUSY') {
                    if (is_array($value)) {
                        $this->cal[$key1][$key2][$key3][][] = $value;
                    } else {
                        $this->freeBusyCount++;

                        end($this->cal[$key1][$key2][$key3]);
                        $key = key($this->cal[$key1][$key2][$key3]);

                        $value = explode('/', $value);
                        $this->cal[$key1][$key2][$key3][$key][] = $value;
                    }
                } else {
                    $this->cal[$key1][$key2][$key3][] = $value;
                }
                break;

            case 'VTODO':
                $this->cal[$component][$this->todoCount - 1][$keyword] = $value;

                break;

            default:
                $this->cal[$component][$keyword] = $value;

                break;
        }

        $this->lastKeyword = $keyword;
    }

    
    protected function keyValueFromString($text)
    {
        $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');

        $colon = strpos($text, ':');
        $quote = strpos($text, '"');
        if ($colon === false) {
            $matches = array();
        } elseif ($quote === false || $colon < $quote) {
            list($before, $after) = explode(':', $text, 2);
            $matches              = array($text, $before, $after);
        } else {
            list($before, $text) = explode('"', $text, 2);
            $text                = '"' . $text;
            $matches             = str_getcsv($text, ':');
            $combinedValue       = '';

            foreach (array_keys($matches) as $key) {
                if ($key === 0) {
                    if (!empty($before)) {
                        $matches[$key] = $before . '"' . $matches[$key] . '"';
                    }
                } else {
                    if ($key > 1) {
                        $combinedValue .= ':';
                    }

                    $combinedValue .= $matches[$key];
                }
            }

            $matches    = array_slice($matches, 0, 2);
            $matches[1] = $combinedValue;
            array_unshift($matches, $before . $text);
        }

        if (count($matches) === 0) {
            return false;
        }

        if (preg_match('/^([A-Z-]+)([;][\w\W]*)?$/', $matches[1])) {
            $matches = array_splice($matches, 1, 2); 

            
            if (preg_match('/([A-Z-]+)[;]([\w\W]*)/', $matches[0], $properties)) {
                
                array_shift($properties);
                
                $matches[0] = $properties[0];
                array_shift($properties); 

                $formatted = array();
                foreach ($properties as $property) {
                    
                    preg_match_all('~[^' . PHP_EOL . '";]+(?:"[^"\\\]*(?:\\\.[^"\\\]*)*"[^' . PHP_EOL . '";]*)*~', $property, $attributes);
                    
                    $attributes = (count($attributes) === 0) ? array($property) : reset($attributes);

                    if (is_array($attributes)) {
                        foreach ($attributes as $attribute) {
                            
                            preg_match_all(
                                '~[^' . PHP_EOL . '"=]+(?:"[^"\\\]*(?:\\\.[^"\\\]*)*"[^' . PHP_EOL . '"=]*)*~',
                                $attribute,
                                $values
                            );
                            
                            $value = (count($values) === 0) ? null : reset($values);

                            if (is_array($value) && isset($value[1])) {
                                
                                $formatted[$value[0]] = trim($value[1], '"');
                            }
                        }
                    }
                }

                
                $properties[0] = $formatted;

                
                array_unshift($properties, $matches[1]);
                $matches[1] = $properties;
            }

            return $matches;
        } else {
            return false; 
        }
    }

    
    public function iCalDateToDateTime($icalDate)
    {
        
        $pattern  = '/^(?:TZID=)?([^:]*|".*")'; 
        $pattern .= ':?';                       
        $pattern .= '([0-9]{8})';               
        $pattern .= 'T?';                       
        $pattern .= '(?(?<=T)([0-9]{6}))';      
        $pattern .= '(Z?)/';                    

        preg_match($pattern, $icalDate, $date);

        if (empty($date)) {
            throw new \Exception('Invalid iCal date format.');
        }

        
        
        

        if ($date[4] === 'Z') {
            $dateTimeZone = new \DateTimeZone(self::TIME_ZONE_UTC);
        } elseif (!empty($date[1])) {
            $dateTimeZone = $this->timeZoneStringToDateTimeZone($date[1]);
        } else {
            $dateTimeZone = new \DateTimeZone($this->defaultTimeZone);
        }

        
        
        
        $dateFormat = '!Ymd';
        $dateBasic  = $date[2];
        if (!empty($date[3])) {
            $dateBasic  .= "T{$date[3]}";
            $dateFormat .= '\THis';
        }

        return \DateTime::createFromFormat($dateFormat, $dateBasic, $dateTimeZone);
    }

    
    public function iCalDateToUnixTimestamp($icalDate)
    {
        return $this->iCalDateToDateTime($icalDate)->getTimestamp();
    }

    
    public function iCalDateWithTimeZone(array $event, $key, $format = self::DATE_TIME_FORMAT)
    {
        if (!isset($event["{$key}_array"]) || !isset($event[$key])) {
            return false;
        }

        $dateArray = $event["{$key}_array"];

        if ($key === 'DURATION') {
            $dateTime = $this->parseDuration($event['DTSTART'], $dateArray[2], null);
        } else {
            
            $dateTime = new \DateTime("@{$dateArray[2]}");
        }

        
        $dateTime->setTimezone(new \DateTimeZone($this->calendarTimeZone()));

        if (is_null($format)) {
            return $dateTime;
        }

        return $dateTime->format($format);
    }

    
    protected function processEvents()
    {
        $checks = null;
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        if (!empty($events)) {
            foreach ($events as $key => $anEvent) {
                foreach (array('DTSTART', 'DTEND', 'RECURRENCE-ID') as $type) {
                    if (isset($anEvent[$type])) {
                        $date = $anEvent["{$type}_array"][1];

                        if (isset($anEvent["{$type}_array"][0]['TZID'])) {
                            $timeZone = $this->escapeParamText($anEvent["{$type}_array"][0]['TZID']);
                            $date     = sprintf(self::ICAL_DATE_TIME_TEMPLATE, $timeZone) . $date;
                        }

                        $anEvent["{$type}_array"][2] = $this->iCalDateToUnixTimestamp($date);
                        $anEvent["{$type}_array"][3] = $date;
                    }
                }

                if (isset($anEvent['RECURRENCE-ID'])) {
                    $uid = $anEvent['UID'];

                    if (!isset($this->alteredRecurrenceInstances[$uid])) {
                        $this->alteredRecurrenceInstances[$uid] = array();
                    }

                    $recurrenceDateUtc = $this->iCalDateToUnixTimestamp($anEvent['RECURRENCE-ID_array'][3]);
                    $this->alteredRecurrenceInstances[$uid][$key] = $recurrenceDateUtc;
                }

                $events[$key] = $anEvent;
            }

            $eventKeysToRemove = array();

            foreach ($events as $key => $event) {
                $checks[] = !isset($event['RECURRENCE-ID']);
                $checks[] = isset($event['UID']);
                $checks[] = isset($event['UID']) && isset($this->alteredRecurrenceInstances[$event['UID']]);

                if ((bool) array_product($checks)) {
                    $eventDtstartUnix = $this->iCalDateToUnixTimestamp($event['DTSTART_array'][3]);

                    
                    if (($alteredEventKey = array_search($eventDtstartUnix, $this->alteredRecurrenceInstances[$event['UID']])) !== false) {
                        $eventKeysToRemove[] = $alteredEventKey;

                        $alteredEvent = array_replace_recursive($events[$key], $events[$alteredEventKey]);
                        $this->alteredRecurrenceInstances[$event['UID']]['altered-event'] = array($key => $alteredEvent);
                    }
                }

                unset($checks);
            }

            foreach ($eventKeysToRemove as $eventKeyToRemove) {
                $events[$eventKeyToRemove] = null;
            }

            $this->cal['VEVENT'] = $events;
        }
    }

    
    protected function processRecurrences()
    {
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        
        if (empty($events)) {
            return;
        }

        $allEventRecurrences = array();
        $eventKeysToRemove = array();

        foreach ($events as $key => $anEvent) {
            if (!isset($anEvent['RRULE']) || $anEvent['RRULE'] === '') {
                continue;
            }

            
            $anEvent['RRULE_array'][2] = self::RECURRENCE_EVENT;

            
            $initialEventDate = $this->icalDateToDateTime($anEvent['DTSTART_array'][3]);

            
            $rrules = array();
            foreach (explode(';', $anEvent['RRULE']) as $s) {
                list($k, $v) = explode('=', $s);
                if (in_array($k, array('BYSETPOS', 'BYDAY', 'BYMONTHDAY', 'BYMONTH', 'BYYEARDAY', 'BYWEEKNO'))) {
                    $rrules[$k] = explode(',', $v);
                } else {
                    $rrules[$k] = $v;
                }
            }

            
            $frequency = $rrules['FREQ'];

            
            
            
            
            
            
            if (isset($rrules['BYDAY'])) {
                $checkByDays = function ($carry, $weekday) {
                    return $carry && substr($weekday, -2) === $weekday;
                };
                if (!in_array($frequency, array('MONTHLY', 'YEARLY'))) {
                    if (!array_reduce($rrules['BYDAY'], $checkByDays, true)) {
                        error_log("ICal::ProcessRecurrences: A {$frequency} RRULE may not contain BYDAY values with numeric prefixes");

                        continue;
                    }
                } elseif ($frequency === 'YEARLY' && !empty($rrules['BYWEEKNO'])) {
                    if (!array_reduce($rrules['BYDAY'], $checkByDays, true)) {
                        error_log('ICal::ProcessRecurrences: A YEARLY RRULE with a BYWEEKNO part may not contain BYDAY values with numeric prefixes');

                        continue;
                    }
                }
            }

            
            $interval = (empty($rrules['INTERVAL'])) ? 1 : $rrules['INTERVAL'];

            
            if (!is_int($this->defaultSpan)) {
                trigger_error('ICal::defaultSpan: User defined value is not an integer', E_USER_NOTICE);
            }

            
            $exdates = $this->parseExdates($anEvent);

            
            $initialDateIsExdate = array_reduce($exdates, function ($carry, $exdate) use ($initialEventDate) {
                return $carry || $exdate->getTimestamp() == $initialEventDate->getTimestamp();
            }, false);

            if ($initialDateIsExdate) {
                $eventKeysToRemove[] = $key;
            }

            
            $count      = 1;
            $countLimit = (isset($rrules['COUNT'])) ? intval($rrules['COUNT']) : 0;
            $until      = date_create()->modify("{$this->defaultSpan} years")->setTime(23, 59, 59)->getTimestamp();

            if (isset($rrules['UNTIL'])) {
                $until = min($until, $this->iCalDateToUnixTimestamp($rrules['UNTIL']));
            }

            $eventRecurrences = array();

            $frequencyRecurringDateTime = clone $initialEventDate;
            while ($frequencyRecurringDateTime->getTimestamp() <= $until) {
                $candidateDateTimes = array();

                
                switch ($frequency) {
                    case 'DAILY':
                        if (!empty($rrules['BYMONTHDAY'])) {
                            if (!isset($monthDays)) {
                                
                                $monthDays = $this->getDaysOfMonthMatchingByMonthDayRRule($rrules['BYMONTHDAY'], $frequencyRecurringDateTime);
                            }

                            if (!in_array($frequencyRecurringDateTime->format('j'), $monthDays)) {
                                break;
                            }
                        }

                        $candidateDateTimes[] = clone $frequencyRecurringDateTime;

                        break;

                    case 'WEEKLY':
                        $initialDayOfWeek = $frequencyRecurringDateTime->format('N');
                        $matchingDays     = array($initialDayOfWeek);

                        if (!empty($rrules['BYDAY'])) {
                            
                            
                            
                            $wkstTransition = 7;

                            if (empty($rrules['WKST'])) {
                                if ($this->defaultWeekStart !== self::ISO_8601_WEEK_START) {
                                    $wkstTransition = array_search($this->defaultWeekStart, array_keys($this->weekdays));
                                }
                            } elseif ($rrules['WKST'] !== self::ISO_8601_WEEK_START) {
                                $wkstTransition = array_search($rrules['WKST'], array_keys($this->weekdays));
                            }

                            $matchingDays = array_map(
                                function ($weekday) use ($initialDayOfWeek, $wkstTransition, $interval) {
                                    $day = array_search($weekday, array_keys($this->weekdays));

                                    if ($day < $initialDayOfWeek) {
                                        $day += 7;
                                    }

                                    if ($day >= $wkstTransition) {
                                        $day += 7 * ($interval - 1);
                                    }

                                    
                                    
                                    
                                    
                                    $day++;

                                    return $day;
                                },
                                $rrules['BYDAY']
                            );
                        }

                        sort($matchingDays);

                        foreach ($matchingDays as $day) {
                            $clonedDateTime = clone $frequencyRecurringDateTime;
                            $candidateDateTimes[] = $clonedDateTime->setISODate(
                                $frequencyRecurringDateTime->format('o'),
                                $frequencyRecurringDateTime->format('W'),
                                $day
                            );
                        }
                        break;

                    case 'MONTHLY':
                        $matchingDays = array();

                        if (!empty($rrules['BYMONTHDAY'])) {
                            $matchingDays = $this->getDaysOfMonthMatchingByMonthDayRRule($rrules['BYMONTHDAY'], $frequencyRecurringDateTime);
                            if (!empty($rrules['BYDAY'])) {
                                $matchingDays = array_filter(
                                    $this->getDaysOfMonthMatchingByDayRRule($rrules['BYDAY'], $frequencyRecurringDateTime),
                                    function ($monthDay) use ($matchingDays) {
                                        return in_array($monthDay, $matchingDays);
                                    }
                                );
                            }
                        } elseif (!empty($rrules['BYDAY'])) {
                            $matchingDays = $this->getDaysOfMonthMatchingByDayRRule($rrules['BYDAY'], $frequencyRecurringDateTime);
                        }

                        if (!empty($rrules['BYSETPOS'])) {
                            $matchingDays = $this->filterValuesUsingBySetPosRRule($rrules['BYSETPOS'], $matchingDays);
                        }

                        foreach ($matchingDays as $day) {
                            
                            if ($day > $frequencyRecurringDateTime->format('t')) {
                                continue;
                            }

                            $clonedDateTime = clone $frequencyRecurringDateTime;
                            $candidateDateTimes[] = $clonedDateTime->setDate(
                                $frequencyRecurringDateTime->format('Y'),
                                $frequencyRecurringDateTime->format('m'),
                                $day
                            );
                        }
                        break;

                    case 'YEARLY':
                        $matchingDays = array();

                        if (!empty($rrules['BYMONTH'])) {
                            $bymonthRecurringDatetime = clone $frequencyRecurringDateTime;
                            foreach ($rrules['BYMONTH'] as $byMonth) {
                                $bymonthRecurringDatetime->setDate(
                                    $frequencyRecurringDateTime->format('Y'),
                                    $byMonth,
                                    $frequencyRecurringDateTime->format('d')
                                );

                                
                                
                                $monthDays = array();
                                if (!empty($rrules['BYMONTHDAY'])) {
                                    $monthDays = $this->getDaysOfMonthMatchingByMonthDayRRule($rrules['BYMONTHDAY'], $bymonthRecurringDatetime);
                                } elseif (!empty($rrules['BYDAY'])) {
                                    $monthDays = $this->getDaysOfMonthMatchingByDayRRule($rrules['BYDAY'], $bymonthRecurringDatetime);
                                } else {
                                    $monthDays[] = $bymonthRecurringDatetime->format('d');
                                }

                                
                                foreach ($monthDays as $day) {
                                    $matchingDays[] = $bymonthRecurringDatetime->setDate(
                                        $frequencyRecurringDateTime->format('Y'),
                                        $bymonthRecurringDatetime->format('m'),
                                        $day
                                    )->format('z') + 1;
                                }
                            }
                        } elseif (!empty($rrules['BYWEEKNO'])) {
                            $matchingDays = $this->getDaysOfYearMatchingByWeekNoRRule($rrules['BYWEEKNO'], $frequencyRecurringDateTime);
                        } elseif (!empty($rrules['BYYEARDAY'])) {
                            $matchingDays = $this->getDaysOfYearMatchingByYearDayRRule($rrules['BYYEARDAY'], $frequencyRecurringDateTime);
                        } elseif (!empty($rrules['BYMONTHDAY'])) {
                            $matchingDays = $this->getDaysOfYearMatchingByMonthDayRRule($rrules['BYMONTHDAY'], $frequencyRecurringDateTime);
                        }

                        if (!empty($rrules['BYDAY'])) {
                            if (!empty($rrules['BYYEARDAY']) || !empty($rrules['BYMONTHDAY']) || !empty($rrules['BYWEEKNO'])) {
                                $matchingDays = array_filter(
                                    $this->getDaysOfYearMatchingByDayRRule($rrules['BYDAY'], $frequencyRecurringDateTime),
                                    function ($yearDay) use ($matchingDays) {
                                        return in_array($yearDay, $matchingDays);
                                    }
                                );
                            } elseif (count($matchingDays) === 0) {
                                $matchingDays = $this->getDaysOfYearMatchingByDayRRule($rrules['BYDAY'], $frequencyRecurringDateTime);
                            }
                        }

                        if (count($matchingDays) === 0) {
                            $matchingDays = array($frequencyRecurringDateTime->format('z') + 1);
                        } else {
                            sort($matchingDays);
                        }

                        if (!empty($rrules['BYSETPOS'])) {
                            $matchingDays = $this->filterValuesUsingBySetPosRRule($rrules['BYSETPOS'], $matchingDays);
                        }

                        foreach ($matchingDays as $day) {
                            $clonedDateTime = clone $frequencyRecurringDateTime;
                            $candidateDateTimes[] = $clonedDateTime->setDate(
                                $frequencyRecurringDateTime->format('Y'),
                                1,
                                $day
                            );
                        }
                        break;
                }

                foreach ($candidateDateTimes as $candidate) {
                    $timestamp = $candidate->getTimestamp();
                    if ($timestamp <= $initialEventDate->getTimestamp()) {
                        continue;
                    }

                    if ($timestamp > $until) {
                        break;
                    }

                    
                    $isExcluded = array_filter($exdates, function ($exdate) use ($timestamp) {
                        return $exdate->getTimestamp() == $timestamp;
                    });

                    if (isset($this->alteredRecurrenceInstances[$anEvent['UID']])) {
                        if (in_array($timestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                            $isExcluded = true;
                        }
                    }

                    if (!$isExcluded) {
                        $eventRecurrences[] = $candidate;
                        $this->eventCount++;
                    }

                    
                    if (isset($rrules['COUNT'])) {
                        $count++;

                        
                        if ($count >= $countLimit) {
                            break 2;
                        }
                    }
                }

                
                $monthPreMove = $frequencyRecurringDateTime->format('m');
                $frequencyRecurringDateTime->modify("{$interval} {$this->frequencyConversion[$frequency]}");

                
                
                
                
                if ($frequency === 'MONTHLY') {
                    $monthDiff = $frequencyRecurringDateTime->format('m') - $monthPreMove;

                    if (($monthDiff > 0 && $monthDiff > $interval) || ($monthDiff < 0 && $monthDiff > $interval - 12)) {
                        $frequencyRecurringDateTime->modify('-1 month');
                    }
                }

                
                
                
                if (isset($monthDays) && $frequencyRecurringDateTime->format('m') !== $monthPreMove) {
                    unset($monthDays);
                }
            }

            unset($monthDays); 

            
            $eventLength = 0;
            if (isset($anEvent['DURATION'])) {
                $clonedDateTime = clone $initialEventDate;
                $endDate        = $clonedDateTime->add($anEvent['DURATION_array'][2]);
                $eventLength    = $endDate->getTimestamp() - $anEvent['DTSTART_array'][2];
            } elseif (isset($anEvent['DTEND_array'])) {
                $eventLength = $anEvent['DTEND_array'][2] - $anEvent['DTSTART_array'][2];
            }

            
            $initialDateWasUTC = substr($anEvent['DTSTART'], -1) === 'Z';

            
            $dateParamArray = array();
            if (
                !$initialDateWasUTC
                && isset($anEvent['DTSTART_array'][0]['TZID'])
                && $this->isValidTimeZoneId($anEvent['DTSTART_array'][0]['TZID'])
            ) {
                $dateParamArray['TZID'] = $anEvent['DTSTART_array'][0]['TZID'];
            }

            
            $eventRecurrences = array_map(
                function ($recurringDatetime) use ($anEvent, $eventLength, $initialDateWasUTC, $dateParamArray) {
                    $tzidPrefix = (isset($dateParamArray['TZID'])) ? 'TZID=' . $this->escapeParamText($dateParamArray['TZID']) . ':' : '';

                    foreach (array('DTSTART', 'DTEND') as $dtkey) {
                        $anEvent[$dtkey] = $recurringDatetime->format(self::DATE_TIME_FORMAT) . (($initialDateWasUTC) ? 'Z' : '');

                        $anEvent["{$dtkey}_array"] = array(
                            $dateParamArray,                    
                            $anEvent[$dtkey],                   
                            $recurringDatetime->getTimestamp(), 
                            "{$tzidPrefix}{$anEvent[$dtkey]}",  
                        );

                        if ($dtkey !== 'DTEND') {
                            $recurringDatetime->modify("{$eventLength} seconds");
                        }
                    }

                    return $anEvent;
                },
                $eventRecurrences
            );

            $allEventRecurrences = array_merge($allEventRecurrences, $eventRecurrences);
        }

        
        foreach ($eventKeysToRemove as $eventKeyToRemove) {
            $events[$eventKeyToRemove] = null;
        }

        $events = array_merge($events, $allEventRecurrences);

        $this->cal['VEVENT'] = $events;
    }

    
    protected function resolveIndicesOfRange(array $indexes, $limit)
    {
        $matching = array();
        foreach ($indexes as $index) {
            if ($index > 0 && $index <= $limit) {
                $matching[] = $index;
            } elseif ($index < 0 && -$index <= $limit) {
                $matching[] = $index + $limit + 1;
            }
        }

        sort($matching);

        return $matching;
    }

    
    protected function getDaysOfMonthMatchingByDayRRule(array $byDays, $initialDateTime)
    {
        $matchingDays = array();

        foreach ($byDays as $weekday) {
            $bydayDateTime = clone $initialDateTime;

            $ordwk = intval(substr($weekday, 0, -2));

            
            
            $bydayDateTime->modify(
                (($ordwk < 0) ? 'Last' : 'First') .
                ' ' .
                $this->weekdays[substr($weekday, -2)] . 
                ' of ' .
                $initialDateTime->format('F') 
            );

            if ($ordwk < 0) { 
                $bydayDateTime->modify((++$ordwk) . ' week');
                $matchingDays[] = $bydayDateTime->format('j');
            } elseif ($ordwk > 0) { 
                $bydayDateTime->modify((--$ordwk) . ' week');
                $matchingDays[] = $bydayDateTime->format('j');
            } else { 
                while ($bydayDateTime->format('n') === $initialDateTime->format('n')) {
                    $matchingDays[] = $bydayDateTime->format('j');
                    $bydayDateTime->modify('+1 week');
                }
            }
        }

        
        sort($matchingDays);

        return $matchingDays;
    }

    
    protected function getDaysOfMonthMatchingByMonthDayRRule(array $byMonthDays, $initialDateTime)
    {
        return $this->resolveIndicesOfRange($byMonthDays, $initialDateTime->format('t'));
    }

    
    protected function getDaysOfYearMatchingByDayRRule(array $byDays, $initialDateTime)
    {
        $matchingDays = array();

        foreach ($byDays as $weekday) {
            $bydayDateTime = clone $initialDateTime;

            $ordwk = intval(substr($weekday, 0, -2));

            
            
            $bydayDateTime->modify(
                (($ordwk < 0) ? 'Last' : 'First') .
                ' ' .
                $this->weekdays[substr($weekday, -2)] . 
                ' of ' . (($ordwk < 0) ? 'December' : 'January') .
                ' ' . $initialDateTime->format('Y') 
            );

            if ($ordwk < 0) { 
                $bydayDateTime->modify((++$ordwk) . ' week');
                $matchingDays[] = $bydayDateTime->format('z') + 1;
            } elseif ($ordwk > 0) { 
                $bydayDateTime->modify((--$ordwk) . ' week');
                $matchingDays[] = $bydayDateTime->format('z') + 1;
            } else { 
                while ($bydayDateTime->format('Y') === $initialDateTime->format('Y')) {
                    $matchingDays[] = $bydayDateTime->format('z') + 1;
                    $bydayDateTime->modify('+1 week');
                }
            }
        }

        
        sort($matchingDays);

        return $matchingDays;
    }

    
    protected function getDaysOfYearMatchingByYearDayRRule(array $byYearDays, $initialDateTime)
    {
        
        $daysInThisYear = $initialDateTime->format('L') ? 366 : 365;

        return $this->resolveIndicesOfRange($byYearDays, $daysInThisYear);
    }

    
    protected function getDaysOfYearMatchingByWeekNoRRule(array $byWeekNums, $initialDateTime)
    {
        
        $isLeapYear = $initialDateTime->format('L');
        $firstDayOfTheYear = date_create("first day of January {$initialDateTime->format('Y')}")->format('D');
        $weeksInThisYear = ($firstDayOfTheYear === 'Thu' || $isLeapYear && $firstDayOfTheYear === 'Wed') ? 53 : 52;

        $matchingWeeks = $this->resolveIndicesOfRange($byWeekNums, $weeksInThisYear);
        $matchingDays = array();
        $byweekDateTime = clone $initialDateTime;
        foreach ($matchingWeeks as $weekNum) {
            $dayNum = $byweekDateTime->setISODate(
                $initialDateTime->format('Y'),
                $weekNum,
                1
            )->format('z') + 1;
            for ($x = 0; $x < 7; ++$x) {
                $matchingDays[] = $x + $dayNum;
            }
        }

        sort($matchingDays);

        return $matchingDays;
    }

    
    protected function getDaysOfYearMatchingByMonthDayRRule(array $byMonthDays, $initialDateTime)
    {
        $matchingDays = array();
        $monthDateTime = clone $initialDateTime;
        for ($month = 1; $month < 13; $month++) {
            $monthDateTime->setDate(
                $initialDateTime->format('Y'),
                $month,
                1
            );

            $monthDays = $this->getDaysOfMonthMatchingByMonthDayRRule($byMonthDays, $monthDateTime);
            foreach ($monthDays as $day) {
                $matchingDays[] = $monthDateTime->setDate(
                    $initialDateTime->format('Y'),
                    $monthDateTime->format('m'),
                    $day
                )->format('z') + 1;
            }
        }

        return $matchingDays;
    }

    
    protected function filterValuesUsingBySetPosRRule(array $bySetPos, array $valuesList)
    {
        $filteredMatches = array();

        foreach ($bySetPos as $setPosition) {
            if ($setPosition < 0) {
                $setPosition = count($valuesList) + ++$setPosition;
            }

            
            if (isset($valuesList[$setPosition - 1])) {
                $filteredMatches[] = $valuesList[$setPosition - 1];
            }
        }

        return $filteredMatches;
    }

    
    protected function processDateConversions()
    {
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        if (!empty($events)) {
            foreach ($events as $key => $anEvent) {
                if (is_null($anEvent) || !$this->isValidDate($anEvent['DTSTART'])) {
                    unset($events[$key]);
                    $this->eventCount--;

                    continue;
                }

                $events[$key]['DTSTART_tz'] = $this->iCalDateWithTimeZone($anEvent, 'DTSTART');

                if ($this->iCalDateWithTimeZone($anEvent, 'DTEND')) {
                    $events[$key]['DTEND_tz'] = $this->iCalDateWithTimeZone($anEvent, 'DTEND');
                } elseif ($this->iCalDateWithTimeZone($anEvent, 'DURATION')) {
                    $events[$key]['DTEND_tz'] = $this->iCalDateWithTimeZone($anEvent, 'DURATION');
                } else {
                    $events[$key]['DTEND_tz'] = $events[$key]['DTSTART_tz'];
                }
            }

            $this->cal['VEVENT'] = $events;
        }
    }

    
    public function events()
    {
        $array = $this->cal;
        $array = isset($array['VEVENT']) ? $array['VEVENT'] : array();

        $events = array();

        if (!empty($array)) {
            foreach ($array as $event) {
                $events[] = new Event($event);
            }
        }

        return $events;
    }

    
    public function calendarName()
    {
        return isset($this->cal['VCALENDAR']['X-WR-CALNAME']) ? $this->cal['VCALENDAR']['X-WR-CALNAME'] : '';
    }

    
    public function calendarDescription()
    {
        return isset($this->cal['VCALENDAR']['X-WR-CALDESC']) ? $this->cal['VCALENDAR']['X-WR-CALDESC'] : '';
    }

    
    public function calendarTimeZone($ignoreUtc = false)
    {
        if (isset($this->cal['VCALENDAR']['X-WR-TIMEZONE'])) {
            $timeZone = $this->cal['VCALENDAR']['X-WR-TIMEZONE'];
        } elseif (isset($this->cal['VTIMEZONE']['TZID'])) {
            $timeZone = $this->cal['VTIMEZONE']['TZID'];
        } else {
            $timeZone = $this->defaultTimeZone;
        }

        
        $timeZone = $this->timeZoneStringToDateTimeZone($timeZone)->getName();

        if ($ignoreUtc && strtoupper($timeZone) === self::TIME_ZONE_UTC) {
            return null;
        }

        return $timeZone;
    }

    
    public function freeBusyEvents()
    {
        $array = $this->cal;

        return isset($array['VFREEBUSY']) ? $array['VFREEBUSY'] : array();
    }

    
    public function hasEvents()
    {
        return (count($this->events()) > 0) ?: false;
    }

    
    public function eventsFromRange($rangeStart = null, $rangeEnd = null)
    {
        
        $events = $this->sortEventsWithOrder($this->events());

        if (empty($events)) {
            return array();
        }

        $extendedEvents = array();

        if (!is_null($rangeStart)) {
            try {
                $rangeStart = new \DateTime($rangeStart, new \DateTimeZone($this->defaultTimeZone));
            } catch (\Exception $exception) {
                error_log("ICal::eventsFromRange: Invalid date passed ({$rangeStart})");
                $rangeStart = false;
            }
        } else {
            $rangeStart = new \DateTime('now', new \DateTimeZone($this->defaultTimeZone));
        }

        if (!is_null($rangeEnd)) {
            try {
                $rangeEnd = new \DateTime($rangeEnd, new \DateTimeZone($this->defaultTimeZone));
            } catch (\Exception $exception) {
                error_log("ICal::eventsFromRange: Invalid date passed ({$rangeEnd})");
                $rangeEnd = false;
            }
        } else {
            $rangeEnd = new \DateTime('now', new \DateTimeZone($this->defaultTimeZone));
            $rangeEnd->modify('+20 years');
        }

        
        if ($rangeEnd->format('His') == 0 && $rangeStart->getTimestamp() === $rangeEnd->getTimestamp()) {
            $rangeEnd->modify('+1 day');
        }

        $rangeStart = $rangeStart->getTimestamp();
        $rangeEnd   = $rangeEnd->getTimestamp();

        foreach ($events as $anEvent) {
            $eventStart = $anEvent->dtstart_array[2];
            $eventEnd   = (isset($anEvent->dtend_array[2])) ? $anEvent->dtend_array[2] : null;

            if (
                ($eventStart >= $rangeStart && $eventStart < $rangeEnd)         
                || ($eventEnd !== null
                    && (
                        ($eventEnd > $rangeStart && $eventEnd <= $rangeEnd)     
                        || ($eventStart < $rangeStart && $eventEnd > $rangeEnd) 
                    )
                )
            ) {
                $extendedEvents[] = $anEvent;
            }
        }

        if (empty($extendedEvents)) {
            return array();
        }

        return $extendedEvents;
    }

    
    public function eventsFromInterval($interval)
    {
        $rangeStart = new \DateTime('now', new \DateTimeZone($this->defaultTimeZone));
        $rangeEnd   = new \DateTime('now', new \DateTimeZone($this->defaultTimeZone));

        $dateInterval = \DateInterval::createFromDateString($interval);
        $rangeEnd->add($dateInterval);

        return $this->eventsFromRange($rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d'));
    }

    
    public function sortEventsWithOrder(array $events, $sortOrder = SORT_ASC)
    {
        $extendedEvents = array();
        $timestamp      = array();

        foreach ($events as $key => $anEvent) {
            $extendedEvents[] = $anEvent;
            $timestamp[$key]  = $anEvent->dtstart_array[2];
        }

        array_multisort($timestamp, $sortOrder, $extendedEvents);

        return $extendedEvents;
    }

    
    protected function isValidTimeZoneId($timeZone)
    {
        return $this->isValidIanaTimeZoneId($timeZone) !== false
            || $this->isValidCldrTimeZoneId($timeZone) !== false
            || $this->isValidWindowsTimeZoneId($timeZone) !== false;
    }

    
    protected function isValidIanaTimeZoneId($timeZone)
    {
        if (in_array($timeZone, $this->validIanaTimeZones)) {
            return true;
        }

        $valid = array();
        $tza   = timezone_abbreviations_list();

        foreach ($tza as $zone) {
            foreach ($zone as $item) {
                $valid[$item['timezone_id']] = true;
            }
        }

        unset($valid['']);

        if (isset($valid[$timeZone]) || in_array($timeZone, timezone_identifiers_list(\DateTimeZone::ALL_WITH_BC))) {
            $this->validIanaTimeZones[] = $timeZone;

            return true;
        }

        return false;
    }

    
    public function isValidCldrTimeZoneId($timeZone)
    {
        return array_key_exists(html_entity_decode($timeZone), self::$cldrTimeZonesMap);
    }

    
    public function isValidWindowsTimeZoneId($timeZone)
    {
        return array_key_exists(html_entity_decode($timeZone), self::$windowsTimeZonesMap);
    }

    
    protected function parseDuration($date, $duration, $format = self::UNIX_FORMAT)
    {
        $dateTime = date_create($date);
        $dateTime->modify("{$duration->y} year");
        $dateTime->modify("{$duration->m} month");
        $dateTime->modify("{$duration->d} day");
        $dateTime->modify("{$duration->h} hour");
        $dateTime->modify("{$duration->i} minute");
        $dateTime->modify("{$duration->s} second");

        if (is_null($format)) {
            $output = $dateTime;
        } elseif ($format === self::UNIX_FORMAT) {
            $output = $dateTime->getTimestamp();
        } else {
            $output = $dateTime->format($format);
        }

        return $output;
    }

    
    protected function removeUnprintableChars($data)
    {
        return preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $data);
    }

    
    protected function mb_chr($code) 
    {
        if (function_exists('mb_chr')) {
            return mb_chr($code);
        } else {
            if (($code %= 0x200000) < 0x80) {
                $s = chr($code);
            } elseif ($code < 0x800) {
                $s = chr(0xc0 | $code >> 6) . chr(0x80 | $code & 0x3f);
            } elseif ($code < 0x10000) {
                $s = chr(0xe0 | $code >> 12) . chr(0x80 | $code >> 6 & 0x3f) . chr(0x80 | $code & 0x3f);
            } else {
                $s = chr(0xf0 | $code >> 18) . chr(0x80 | $code >> 12 & 0x3f) . chr(0x80 | $code >> 6 & 0x3f) . chr(0x80 | $code & 0x3f);
            }

            return $s;
        }
    }

    
    protected static function mb_str_replace($search, $replace, $subject, $encoding = null, &$count = 0) 
    {
        if (is_array($subject)) {
            
            foreach ($subject as $key => $value) {
                $subject[$key] = self::mb_str_replace($search, $replace, $value, $encoding, $count);
            }
        } else {
            
            $searches     = is_array($search) ? array_values($search) : array($search);
            $replacements = is_array($replace) ? array_values($replace) : array($replace);
            $replacements = array_pad($replacements, count($searches), '');

            foreach ($searches as $key => $search) {
                if (is_null($encoding)) {
                    $encoding = mb_detect_encoding($search, 'UTF-8', true);
                }

                $replace   = $replacements[$key];
                $searchLen = mb_strlen($search, $encoding);

                $sb = array();
                while (($offset = mb_strpos($subject, $search, 0, $encoding)) !== false) {
                    $sb[]    = mb_substr($subject, 0, $offset, $encoding);
                    $subject = mb_substr($subject, $offset + $searchLen, null, $encoding);
                    ++$count;
                }

                $sb[]    = $subject;
                $subject = implode($replace, $sb);
            }
        }

        return $subject;
    }

    
    protected function escapeParamText($candidateText)
    {
        if (strpbrk($candidateText, ':;,') !== false) {
            return '"' . $candidateText . '"';
        }

        return $candidateText;
    }

    
    protected function cleanData($data)
    {
        $replacementChars = array(
            "\xe2\x80\x98" => "'",   
            "\xe2\x80\x99" => "'",   
            "\xe2\x80\x9a" => "'",   
            "\xe2\x80\x9b" => "'",   
            "\xe2\x80\x9c" => '"',   
            "\xe2\x80\x9d" => '"',   
            "\xe2\x80\x9e" => '"',   
            "\xe2\x80\x9f" => '"',   
            "\xe2\x80\x93" => '-',   
            "\xe2\x80\x94" => '--',  
            "\xe2\x80\xa6" => '...', 
            "\xc2\xa0"     => ' ',
        );
        
        $cleanedData = strtr($data, $replacementChars);

        
        $charsToReplace = array_map(function ($code) {
            return $this->mb_chr($code);
        }, array(133, 145, 146, 147, 148, 150, 151, 194));
        $cleanedData = $this->mb_str_replace($charsToReplace, $replacementChars, $cleanedData);

        return $cleanedData;
    }

    
    public function parseExdates(array $event)
    {
        if (empty($event['EXDATE_array'])) {
            return array();
        } else {
            $exdates = $event['EXDATE_array'];
        }

        $output          = array();
        $currentTimeZone = new \DateTimeZone($this->defaultTimeZone);

        foreach ($exdates as $subArray) {
            end($subArray);
            $finalKey = key($subArray);

            foreach (array_keys($subArray) as $key) {
                if ($key === 'TZID') {
                    $currentTimeZone = $this->timeZoneStringToDateTimeZone($subArray[$key]);
                } elseif (is_numeric($key)) {
                    $icalDate = $subArray[$key];

                    if (substr($icalDate, -1) === 'Z') {
                        $currentTimeZone = new \DateTimeZone(self::TIME_ZONE_UTC);
                    }

                    $output[] = new \DateTime($icalDate, $currentTimeZone);

                    if ($key === $finalKey) {
                        
                        $currentTimeZone = new \DateTimeZone($this->defaultTimeZone);
                    }
                }
            }
        }

        return $output;
    }

    
    public function isValidDate($value)
    {
        if (!$value) {
            return false;
        }

        try {
            new \DateTime($value);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    
    protected function isFileOrUrl($filename)
    {
        return (file_exists($filename) || filter_var($filename, FILTER_VALIDATE_URL)) ?: false;
    }

    
    protected function fileOrUrl($filename)
    {
        $options                   = array();
        $options['http']           = array();
        $options['http']['header'] = array();

        if (!empty($this->httpBasicAuth) || !empty($this->httpUserAgent) || !empty($this->httpAcceptLanguage)) {
            if (!empty($this->httpBasicAuth)) {
                $username  = $this->httpBasicAuth['username'];
                $password  = $this->httpBasicAuth['password'];
                $basicAuth = base64_encode("{$username}:{$password}");

                $options['http']['header'][] = "Authorization: Basic {$basicAuth}";
            }

            if (!empty($this->httpUserAgent)) {
                $options['http']['header'][] = "User-Agent: {$this->httpUserAgent}";
            }

            if (!empty($this->httpAcceptLanguage)) {
                $options['http']['header'][] = "Accept-language: {$this->httpAcceptLanguage}";
            }
        }

        $options['http']['protocol_version'] = '1.1';

        $options['http']['header'][] = 'Connection: close';

        $context = stream_context_create($options);

        
        if (($lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES, $context)) === false) {
            throw new \Exception("The file path or URL '{$filename}' does not exist.");
        }

        return $lines;
    }

    
    public function timeZoneStringToDateTimeZone($timeZoneString)
    {
        
        
        
        $timeZoneString = trim($timeZoneString, '"');
        $timeZoneString = html_entity_decode($timeZoneString);

        if ($this->isValidIanaTimeZoneId($timeZoneString)) {
            return new \DateTimeZone($timeZoneString);
        }

        if ($this->isValidCldrTimeZoneId($timeZoneString)) {
            return new \DateTimeZone(self::$cldrTimeZonesMap[$timeZoneString]);
        }

        if ($this->isValidWindowsTimeZoneId($timeZoneString)) {
            return new \DateTimeZone(self::$windowsTimeZonesMap[$timeZoneString]);
        }

        return new \DateTimeZone($this->defaultTimeZone);
    }
}
