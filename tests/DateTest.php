<?php
namespace nuffy\tests\SDV;

use PHPUnit\Framework\TestCase;
use nuffy\SDV\Date;

class DateTest extends TestCase
{
    public function testCanCreateDate()
    {
        $date = new Date;
        $this->assertInstanceOf(Date::class, $date);
    }

    public function testEchoesDatesCorrectly()
    {
        $date = new Date("1-2-01");
        $this->assertEquals("1-2-01", $date->__tooString());
        
        $date = new Date("2-4-12");
        $this->assertEquals("2-4-12", $date->__tooString());
        
        $date = new Date("26-3-18");
        $this->assertEquals("26-3-18", $date->__tooString());
    }

    public function testCanCalculateDifferences()
    {
        $date1 = new Date("1-2-08");
        $date2 = new Date("1-2-11");
        $this->assertEquals(3, $date1->diff($date2));
        $this->assertEquals("In 3 days", $date1->diffForHumans($date2));
        $this->assertEquals("3 days ago", $date2->diffForHumans($date1));
        $this->assertEquals("Today", $date1->diffForHumans($date1));
    }

}