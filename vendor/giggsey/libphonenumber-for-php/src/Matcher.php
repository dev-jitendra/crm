<?php

namespace libphonenumber;


class Matcher
{
    
    protected $pattern;

    
    protected $subject = '';

    
    protected $groups = array();

    private $searchIndex = 0;

    
    public function __construct($pattern, $subject)
    {
        $this->pattern = str_replace('/', '\/', (string)$pattern);
        $this->subject = (string)$subject;
    }

    protected function doMatch($type = 'find', $offset = 0)
    {
        $final_pattern = '(?:' . $this->pattern . ')';
        switch ($type) {
            case 'matches':
                $final_pattern = '^' . $final_pattern . '$';
                break;
            case 'lookingAt':
                $final_pattern = '^' . $final_pattern;
                break;
            case 'find':
            default:
                
                break;
        }
        $final_pattern = '/' . $final_pattern . '/ui';

        $search = mb_substr($this->subject, $offset);

        $result = preg_match($final_pattern, $search, $groups, PREG_OFFSET_CAPTURE);

        if ($result === 1) {
            

            $positions = array();

            foreach ($groups as $group) {
                $positions[] = array(
                    $group[0],
                    $offset + mb_strlen(substr($search, 0, $group[1]))
                );
            }

            $this->groups = $positions;
        }

        return ($result === 1);
    }

    
    public function matches()
    {
        return $this->doMatch('matches');
    }

    
    public function lookingAt()
    {
        return $this->doMatch('lookingAt');
    }

    
    public function find($offset = null)
    {
        if ($offset === null) {
            $offset = $this->searchIndex;
        }

        
        $this->searchIndex++;
        return $this->doMatch('find', $offset);
    }

    
    public function groupCount()
    {
        if (empty($this->groups)) {
            return null;
        }

        return count($this->groups) - 1;
    }

    
    public function group($group = null)
    {
        if ($group === null) {
            $group = 0;
        }
        return isset($this->groups[$group][0]) ? $this->groups[$group][0] : null;
    }

    
    public function end($group = null)
    {
        if ($group === null) {
            $group = 0;
        }
        if (!isset($this->groups[$group])) {
            return null;
        }
        return $this->groups[$group][1] + mb_strlen($this->groups[$group][0]);
    }

    public function start($group = null)
    {
        if ($group === null) {
            $group = 0;
        }
        if (!isset($this->groups[$group])) {
            return null;
        }

        return $this->groups[$group][1];
    }

    
    public function replaceFirst($replacement)
    {
        return preg_replace('/' . $this->pattern . '/x', $replacement, $this->subject, 1);
    }

    
    public function replaceAll($replacement)
    {
        return preg_replace('/' . $this->pattern . '/x', $replacement, $this->subject);
    }

    
    public function reset($input = '')
    {
        $this->subject = $input;

        return $this;
    }
}
