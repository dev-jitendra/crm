<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;


class rfc5545RecurrenceExamplesTest extends TestCase
{
    
    
    
    

    private $originalTimeZone = null;

    public function setUp()
    {
        $this->originalTimeZone = date_default_timezone_get();
    }

    public function tearDown()
    {
        date_default_timezone_set($this->originalTimeZone);
    }

    
    public function test_page123_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970904T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;COUNT=10',
            ),
            10,
            $checks
        );
    }

    
    public function test_page123_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970904T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;UNTIL=19971224T000000Z',
            ),
            113,
            $checks
        );
    }

    
    
    
    public function test_page124_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970906T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;INTERVAL=2;UNTIL=19971201Z',
            ),
            45,
            $checks
        );
    }

    
    public function test_page124_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970912T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970922T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;INTERVAL=10;COUNT=5',
            ),
            5,
            $checks
        );
    }

    
    public function test_page124_test3a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19980101T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980102T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19980103T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19980101T090000',
                'RRULE:FREQ=YEARLY;UNTIL=20000131T140000Z;BYMONTH=1;BYDAY=SU,MO,TU,WE,TH,FR,SA',
            ),
            93,
            $checks
        );
    }



    
    public function test_page124_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970909T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;COUNT=10',
            ),
            10,
            $checks
        );
    }

    
    public function test_page125_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970909T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
            array('index' => 16, 'dateString' => '19971223T090000', 'message' => 'last occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;UNTIL=19971224T000000Z',
            ),
            17,
            $checks
        );
    }

    
    
    
    public function test_page125_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970916T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970930T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971014T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19971028T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19971111T090000', 'message' => '6th occurrence: '),
            array('index' => 6, 'dateString' => '19971125T090000', 'message' => '7th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;WKST=SU;UNTIL=19971201Z',
            ),
            7,
            $checks
        );
    }

    
    public function test_page125_test3a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970909T090000', 'message' => '3rd occurrence: '),
            array('index' => 9, 'dateString' => '19971002T090000', 'message' => 'final occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;UNTIL=19971007T000000Z;WKST=SU;BYDAY=TU,TH',
            ),
            10,
            $checks
        );
    }

    
    public function test_page125_test3b()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970909T090000', 'message' => '3rd occurrence: '),
            array('index' => 9, 'dateString' => '19971002T090000', 'message' => 'final occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;COUNT=10;WKST=SU;BYDAY=TU,TH',
            ),
            10,
            $checks
        );
    }

    
    public function test_page125_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970901T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970905T090000', 'message' => '3rd occurrence: '),
            array('index' => 24, 'dateString' => '19971222T090000', 'message' => 'final occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970901T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;UNTIL=19971224T000000Z;WKST=SU;BYDAY=MO,WE,FR',
            ),
            25,
            $checks
        );
    }

    
    public function test_page126_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;COUNT=8;WKST=SU;BYDAY=TU,TH',
            ),
            8,
            $checks
        );
    }

    
    public function test_page126_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970905T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971003T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971107T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970905T090000',
                'RRULE:FREQ=MONTHLY;COUNT=10;BYDAY=1FR',
            ),
            10,
            $checks
        );
    }

    
    public function test_page126_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970905T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971003T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971107T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970905T090000',
                'RRULE:FREQ=MONTHLY;UNTIL=19971224T000000Z;BYDAY=1FR',
            ),
            4,
            $checks
        );
    }

    
    public function test_page126_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970907T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970928T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971102T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971130T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19980104T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19980125T090000', 'message' => '6th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970907T090000',
                'RRULE:FREQ=MONTHLY;INTERVAL=2;COUNT=10;BYDAY=1SU,-1SU',
            ),
            10,
            $checks
        );
    }

    
    public function test_page126_test5()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970922T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971020T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971117T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970922T090000',
                'RRULE:FREQ=MONTHLY;COUNT=6;BYDAY=-2MO',
            ),
            6,
            $checks
        );
    }

    
    
    
    public function test_page127_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970928T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971029T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971128T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971229T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19980129T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19980226T090000', 'message' => '6th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970928T090000',
                'RRULE:FREQ=MONTHLY;BYMONTHDAY=-3;UNTIL=19980401',
            ),
            7,
            $checks
        );
    }

    
    public function test_page127_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970915T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971002T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971015T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MONTHLY;COUNT=10;BYMONTHDAY=2,15',
            ),
            10,
            $checks
        );
    }

    
    public function test_page127_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970930T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971001T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971031T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971101T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19971130T090000', 'message' => '5th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970930T090000',
                'RRULE:FREQ=MONTHLY;COUNT=10;BYMONTHDAY=1,-1',
            ),
            10,
            $checks
        );
    }

    
    public function test_page127_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970910T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970911T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970912T090000', 'message' => '3rd occurrence: '),
            array('index' => 6, 'dateString' => '19990310T090000', 'message' => '7th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970910T090000',
                'RRULE:FREQ=MONTHLY;INTERVAL=18;COUNT=10;BYMONTHDAY=10,11,12,13,14,15',
            ),
            10,
            $checks
        );
    }

    
    
    
    public function test_page127_test5()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970909T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MONTHLY;INTERVAL=2;BYDAY=TU;UNTIL=19980101',
            ),
            9,
            $checks
        );
    }

    
    public function test_page128_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970610T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970710T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19980610T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970610T090000',
                'RRULE:FREQ=YEARLY;COUNT=10;BYMONTH=6,7',
            ),
            10,
            $checks
        );
    }

    
    public function test_page128_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970310T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19990110T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19990210T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970310T090000',
                'RRULE:FREQ=YEARLY;INTERVAL=2;COUNT=10;BYMONTH=1,2,3',
            ),
            10,
            $checks
        );
    }

    
    public function test_page128_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970101T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970410T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970719T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970101T090000',
                'RRULE:FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200',
            ),
            10,
            $checks
        );
    }

    
    
    
    public function test_page128_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970519T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980518T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19990517T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970519T090000',
                'RRULE:FREQ=YEARLY;BYDAY=20MO;COUNT=4',
            ),
            4,
            $checks
        );
    }

    
    
    
    public function test_page129_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970512T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980511T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19990517T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970512T090000',
                'RRULE:FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO;COUNT=4',
            ),
            4,
            $checks
        );
    }

    
    
    
    public function test_page129_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970313T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970320T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970327T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970313T090000',
                'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=TH;UNTIL=19990401Z',
            ),
            11,
            $checks
        );
    }

    
    
    
    public function test_page129_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970605T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970612T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970619T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970605T090000',
                'RRULE:FREQ=YEARLY;BYDAY=TH;BYMONTH=6,7,8;UNTIL=19970901Z',
            ),
            13,
            $checks
        );
    }



    
    
    
    public function test_page130_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970913T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971011T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971108T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970913T090000',
                'RRULE:FREQ=MONTHLY;BYDAY=SA;BYMONTHDAY=7,8,9,10,11,12,13;COUNT=7',
            ),
            7,
            $checks
        );
    }

    
    
    
    public function test_page130_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19961105T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '20001107T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '20041102T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19961105T090000',
                'RRULE:FREQ=YEARLY;INTERVAL=4;BYMONTH=11;BYDAY=TU;BYMONTHDAY=2,3,4,5,6,7,8;COUNT=4',
            ),
            4,
            $checks
        );
    }

    
    public function test_page130_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970904T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971007T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971106T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970904T090000',
                'RRULE:FREQ=MONTHLY;COUNT=3;BYDAY=TU,WE,TH;BYSETPOS=3',
            ),
            3,
            $checks
        );
    }

    
    
    
    public function test_page130_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970929T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971030T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971127T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971230T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970929T090000',
                'RRULE:FREQ=MONTHLY;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=-2;UNTIL=19980101',
            ),
            4,
            $checks
        );
    }











    
    public function test_page131_test5a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970805T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970810T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970819T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19970824T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970805T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;COUNT=4;BYDAY=TU,SU;WKST=MO',
            ),
            4,
            $checks
        );
    }

    
    public function test_page131_test5b()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970805T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970817T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970819T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19970831T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970805T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;COUNT=4;BYDAY=TU,SU;WKST=SU',
            ),
            4,
            $checks
        );
    }

    
    public function test_page132_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20070115T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '20070130T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '20070215T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '20070315T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '20070330T090000', 'message' => '5th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:20070115T090000',
                'RRULE:FREQ=MONTHLY;BYMONTHDAY=15,30;COUNT=5',
            ),
            5,
            $checks
        );
    }

    public function assertVEVENT($defaultTimezone, $veventParts, $count, $checks)
    {
        $options = $this->getOptions($defaultTimezone);

        $testIcal  = implode(PHP_EOL, $this->getIcalHeader());
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->formatIcalEvent($veventParts));
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->getIcalFooter());

        $ical = new ICal(false, $options);
        $ical->initString($testIcal);

        $events = $ical->events();

        $this->assertCount($count, $events);

        foreach ($checks as $check) {
            $this->assertEvent($events[$check['index']], $check['dateString'], $check['message'], isset($check['timezone']) ? $check['timezone'] : $defaultTimezone);
        }
    }

    public function assertEvent($event, $expectedDateString, $message, $timeZone = null)
    {
        if (!is_null($timeZone)) {
            date_default_timezone_set($timeZone);
        }

        $expectedTimeStamp = strtotime($expectedDateString);

        $this->assertEquals($expectedTimeStamp, $event->dtstart_array[2], $message . 'timestamp mismatch (expected ' . $expectedDateString . ' vs actual ' . $event->dtstart . ')');
        $this->assertAttributeEquals($expectedDateString, 'dtstart', $event, $message . 'dtstart mismatch (timestamp is okay)');
    }

    public function getOptions($defaultTimezone)
    {
        $options = array(
            'defaultSpan'                 => 2,                
            'defaultTimeZone'             => $defaultTimezone, 
            'defaultWeekStart'            => 'MO',             
            'disableCharacterReplacement' => false,            
            'filterDaysAfter'             => null,             
            'filterDaysBefore'            => null,             
            'skipRecurrence'              => false,            
        );

        return $options;
    }

    public function formatIcalEvent($veventParts)
    {
        return array_merge(
            array(
                'BEGIN:VEVENT',
                'CREATED:' . gmdate('Ymd\THis\Z'),
                'UID:RFC5545-examples-test',
            ),
            $veventParts,
            array(
                'SUMMARY:test',
                'LAST-MODIFIED:' . gmdate('Ymd\THis\Z', filemtime(__FILE__)),
                'END:VEVENT',
            )
        );
    }

    public function getIcalHeader()
    {
        return array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-
            'X-WR-CALNAME:Private',
            'X-APPLE-CALENDAR-COLOR:#FF2968',
            'X-WR-CALDESC:',
        );
    }

    public function getIcalFooter()
    {
        return array('END:VCALENDAR');
    }
}
