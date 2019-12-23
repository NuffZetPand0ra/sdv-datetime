<?php
namespace nuffy\tests\SDV;

use PHPUnit\Framework\TestCase;
use nuffy\SDV\DateTime;

class DateTimeTest extends TestCase
{
    public function testCanCreateDateTime()
    {
        $date = new DateTime;
        $this->assertInstanceOf(DateTime::class, $date);
    }

    public function testCanCalculateMinutes()
    {
        $date = new DateTime("1-1-2 06:20");
        $this->assertEquals(20, $date->getMinute());
        $date = new DateTime("12-4-2 12:30");
        $this->assertEquals(30, $date->getMinute());
    }
    public function testCanCalculateHours()
    {
        $date = new DateTime("1-1-2 06:20");
        $this->assertEquals(6, $date->getHour());
        $date = new DateTime("12-4-2 12:30");
        $this->assertEquals(12, $date->getHour());
    }
    public function testCanCalculateDays()
    {
        $date = new DateTime("1-2-1 06:20");
        $this->assertEquals(29, $date->getDay());

        $date = new DateTime("4-2-12");
        $this->assertEquals(12, $date->getDayOfSeason());
        $date = new DateTime("4-2-28");
        $this->assertEquals(28, $date->getDayOfSeason());
        $date = new DateTime("4-2-01");
        $this->assertEquals(1, $date->getDayOfSeason());
    }

    public function testEchoesDatesCorrectly()
    {
        $date = new DateTime("1-2-01 8:20");
        $this->assertEquals("1-2-01 08:20", $date->__tooString());
        
        $date = new DateTime("2-4-12 12:00");
        $this->assertEquals("2-4-12 12:00", $date->__tooString());
        
        $date = new DateTime("26-3-18 0:01");
        $this->assertEquals("26-3-18 00:01", $date->__tooString());

        $date = new DateTime("26-3-18 0:00");
        $this->assertEquals("26-3-18 00:00", $date->__tooString());
    }

    public function testCanCalculateDifferences()
    {
        $date1 = new DateTime("1-2-08");
        $date2 = new DateTime("1-2-11");
        $this->assertEquals(3, $date1->diffDays($date2));
        $this->assertEquals("In 3 days", $date1->diffForHumans($date2));
        $this->assertEquals("3 days ago", $date2->diffForHumans($date1));
        $this->assertEquals("Today", $date1->diffForHumans($date1));
    }

    public function testCanFormatDate()
    {
        $date = new DateTime("1-2-18");
        $this->assertEquals("1", $date->format("Y"));
        $this->assertEquals("Hello1218", $date->format('\H\e\l\l\oymd'));
    }

    public function testCanParseDates()
    {
        $date = new DateTime("Summer 12");
        $this->assertEquals(2, $date->getSeason());
        $this->assertEquals(12, $date->getDayOfSeason());
    }


}