<?php

use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testSanitise()
    {
        require_once("system/web.php");
        $w = new Web();

        $table = [
            ["string" => "This is a string", "want" => "This is a string"],
        ];

        foreach ($table as $t) {
            $this->assertEquals($t["want"], Html::sanitise($t["string"]));
        }
    }
}