<?php


class HTMLPurifier_HTMLModule_Ruby extends HTMLPurifier_HTMLModule
{

    
    public $name = 'Ruby';

    
    public function setup($config)
    {
        $this->addElement(
            'ruby',
            'Inline',
            'Custom: ((rb, (rt | (rp, rt, rp))) | (rbc, rtc, rtc?))',
            'Common'
        );
        $this->addElement('rbc', false, 'Required: rb', 'Common');
        $this->addElement('rtc', false, 'Required: rt', 'Common');
        $rb = $this->addElement('rb', false, 'Inline', 'Common');
        $rb->excludes = array('ruby' => true);
        $rt = $this->addElement('rt', false, 'Inline', 'Common', array('rbspan' => 'Number'));
        $rt->excludes = array('ruby' => true);
        $this->addElement('rp', false, 'Optional: #PCDATA', 'Common');
    }
}


