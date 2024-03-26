<?php

namespace Laminas\Validator\Db;

use Laminas\Validator\Exception;


class NoRecordExists extends AbstractDb
{
    
    public function isValid($value)
    {
        
        if (null === $this->adapter) {
            throw new Exception\RuntimeException('No database adapter present');
        }

        $valid = true;
        $this->setValue($value);

        $result = $this->query($value);
        if ($result) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
