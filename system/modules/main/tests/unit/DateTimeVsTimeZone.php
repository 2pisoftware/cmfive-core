<?php

use PHPUnit\Framework\TestCase;

class DateTimeVsTimeZone extends TestCase //\Codeception\Test\Unit
{
    public function testTimeZoneRatchet()
    {
        require_once("system/web.php");
        require_once("system/classes/Config.php");

        $w = new Web();  
        
        $timezone = Config::get('system.timezone');
        if (empty($timezone)) {
            $timezone = 'UTC';
        }
        date_default_timezone_set($timezone);

        $w->initDB();

        $creation = new Contact($w);
        $creation->firstname = "initForDtTest";
        $creation->insert();

        $created = authService::getInstance($w)->getObject("contact",['firstname' => "initForDtTest"]);
        $stamp = $created->dt_created;

        for ($i = 0; $i < 20; $i++) {
            $created = authService::getInstance($w)->getObject("contact",['firstname' => "initForDtTest"]);
            $created->firstname = "looped_" . $i;
            $created->update();
        }

        $flipped = $created->dt_created;
        $created->delete();

        $this->assertSame($stamp, $flipped);
        $this->assertNotNull($stamp);
    }
}
