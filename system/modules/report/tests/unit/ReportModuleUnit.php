<?php

use PHPUnit\Framework\TestCase;

class report_ReportModuleUnit extends TestCase //\Codeception\Test\Unit
{
    public function testSQLI()
    {
        require_once("system/web.php");

        $w = new Web();
        $w->initDB();

        $serve = ReportService::getInstance($w);

        // non malicious cases:  $and = $serve->unitaryWhereToAndClause($sqli);

        $and = $serve->unitaryWhereToAndClause("");
        echo $and . "\n";
        $this->assertSame($and, "");

        $try = [
            "and dog=cat",
            "dog=cat",
            "and reportthing.dog = cat",
            "'dog=cat'",
            "'dog'='cat'",
            "dog='cat'",
        ];
        foreach ($try as $sqli) {
            $and = $serve->unitaryWhereToAndClause($sqli);
            echo $and . "\n";
            $this->assertSame($and, " and r.dog = 'cat' ");
        }


        $try = [
            ["dog" => "cat"],
            ["reportingthing.dog" => "cat"],
            ["'dog'" => "'cat'"],
            ["'dog'" => "cat"],
            ["dog" => "'cat'"],

        ];
        foreach ($try as $sqli) {
            $and = $serve->unitaryWhereToAndClause($sqli);
            echo $and . "\n";
            $this->assertSame($and, " and r.dog = 'cat' ");
        }

        $try =
            [
                "dog" => "cat",
                "pig" => "fish",
            ];
        $and = $serve->unitaryWhereToAndClause($try);
        echo $and . "\n";
        $this->assertSame($and, " and r.dog = 'cat'  and r.pig = 'fish' ");

        //  malicious cases:  $and = $serve->unitaryWhereToAndClause($sqli);

        $and = $serve->unitaryWhereToAndClause("");
        echo $and . "\n";
        $this->assertSame($and, "");

        $try = [
            "';run SQL--",
            "id = r.id",
            [
                "id = r.id or 1=1 or 1" => "r.id"
            ]
        ];
        foreach ($try as $sqli) {
            $and = $serve->unitaryWhereToAndClause($sqli);
            echo $and . "\n";
            $terms = explode("=", str_replace(" and ", "", $and));
            $this->assertEquals(count($terms), 2);
            $this->assertEquals(substr(trim($terms[1]), 0, 1), "'");
            $this->assertEquals(substr(trim($terms[1]), -1, 1), "'");
            $terms = explode(".", $terms[0]);
            $this->assertEquals(count($terms), 2);
            $terms = explode(" ", trim($terms[1]));
            $this->assertEquals(count($terms), 1);
        }
    }
}
