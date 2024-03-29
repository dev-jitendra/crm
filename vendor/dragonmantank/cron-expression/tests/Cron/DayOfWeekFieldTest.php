<?php

declare(strict_types=1);

namespace Cron\Tests;

use Cron\DayOfWeekField;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;


class DayOfWeekFieldTest extends TestCase
{
    
    public function testValidatesField()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->validate('1'));
        $this->assertTrue($f->validate('01'));
        $this->assertTrue($f->validate('00'));
        $this->assertTrue($f->validate('*'));
        $this->assertTrue($f->validate('?'));
        $this->assertFalse($f->validate('*/3,1,1-12'));
        $this->assertTrue($f->validate('SUN-2'));
        $this->assertFalse($f->validate('1.'));
    }

    
    public function testChecksIfSatisfied()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->isSatisfiedBy(new DateTime(), '?'));
        $this->assertTrue($f->isSatisfiedBy(new DateTimeImmutable(), '?'));
    }

    
    public function testIncrementsDate()
    {
        $d = new DateTime('2011-03-15 11:15:00');
        $f = new DayOfWeekField();
        $f->increment($d);
        $this->assertSame('2011-03-16 00:00:00', $d->format('Y-m-d H:i:s'));

        $d = new DateTime('2011-03-15 11:15:00');
        $f->increment($d, true);
        $this->assertSame('2011-03-14 23:59:00', $d->format('Y-m-d H:i:s'));
    }

    
    public function testIncrementsDateTimeImmutable()
    {
        $d = new DateTimeImmutable('2011-03-15 11:15:00');
        $f = new DayOfWeekField();
        $f->increment($d);
        $this->assertSame('2011-03-16 00:00:00', $d->format('Y-m-d H:i:s'));
    }

    
    public function testValidatesHashValueWeekday()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Weekday must be a value between 0 and 7. 12 given');
        $f = new DayOfWeekField();
        $this->assertTrue($f->isSatisfiedBy(new DateTime(), '12#1'));
    }

    
    public function testValidatesHashValueNth()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('There are never more than 5 or less than 1 of a given weekday in a month');
        $f = new DayOfWeekField();
        $this->assertTrue($f->isSatisfiedBy(new DateTime(), '3#6'));
    }

    
    public function testValidateWeekendHash()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->validate('MON#1'));
        $this->assertTrue($f->validate('TUE#2'));
        $this->assertTrue($f->validate('WED#3'));
        $this->assertTrue($f->validate('THU#4'));
        $this->assertTrue($f->validate('FRI#5'));
        $this->assertTrue($f->validate('SAT#1'));
        $this->assertTrue($f->validate('SUN#3'));
        $this->assertTrue($f->validate('MON#1,MON#3'));
    }

    
    public function testHandlesZeroAndSevenDayOfTheWeekValues()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2011-09-04 00:00:00'), '0-2'));
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2011-09-04 00:00:00'), '6-0'));

        $this->assertTrue($f->isSatisfiedBy(new DateTime('2014-04-20 00:00:00'), 'SUN'));
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2014-04-20 00:00:00'), 'SUN#3'));
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2014-04-20 00:00:00'), '0#3'));
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2014-04-20 00:00:00'), '7#3'));
    }

    
    public function testHandlesLastWeekdayOfTheMonth()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2018-12-28 00:00:00'), 'FRIL'));
        $this->assertTrue($f->isSatisfiedBy(new DateTime('2018-12-28 00:00:00'), '5L'));
        $this->assertFalse($f->isSatisfiedBy(new DateTime('2018-12-21 00:00:00'), 'FRIL'));
        $this->assertFalse($f->isSatisfiedBy(new DateTime('2018-12-21 00:00:00'), '5L'));
    }

    
    public function testIssue47()
    {
        $f = new DayOfWeekField();
        $this->assertFalse($f->validate('mon,'));
        $this->assertFalse($f->validate('mon-'));
        $this->assertFalse($f->validate('*/2,'));
        $this->assertFalse($f->validate('-mon'));
        $this->assertFalse($f->validate(',1'));
        $this->assertFalse($f->validate('*-'));
        $this->assertFalse($f->validate(',-'));
    }

    
    public function testLiteralsExpandProperly()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->validate('MON-FRI'));
        $this->assertSame([1, 2, 3, 4, 5], $f->getRangeForExpression('MON-FRI', 7));
    }

    
    public function testLiteralsIgnoreCasingProperly()
    {
        $f = new DayOfWeekField();
        $this->assertTrue($f->validate('MON'));
        $this->assertTrue($f->validate('Mon'));
        $this->assertTrue($f->validate('mon'));
        $this->assertTrue($f->validate('Mon,Wed,Fri'));
    }
}
