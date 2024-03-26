<?php

namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\MbWrapper\MbWrapper;
use DateTime;
use Exception;


class DatePart extends LiteralPart
{
    
    protected $date;

    
    public function __construct(MbWrapper $charsetConverter, $token)
    {
        $dateToken = trim($token);
        
        parent::__construct($charsetConverter, $dateToken);

        
        
        if (preg_match('# [0-9]{4}$#', $dateToken)) {
            $dateToken = preg_replace('# ([0-9]{4})$#', ' +$1', $dateToken);
        
        } elseif (preg_match('#UT$#', $dateToken)) {
            $dateToken = $dateToken . 'C';
        }

        try {
            $this->date = new DateTime($dateToken);
        } catch (Exception $e) {
        }
    }

    
    public function getDateTime()
    {
        return $this->date;
    }
}
