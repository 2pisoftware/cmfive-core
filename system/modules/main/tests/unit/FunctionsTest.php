<?php

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testArrayUniqueMultidimensional(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["input" => [[1, 2], [1, 2], [3, 4], [3, 4]], "want" => [0 => [1, 2], 2 => [3, 4]]],
            ["input" => [[new stdClass(), 'cat'], [new stdClass(), 'cat'], ['dog', 4], ['dog', 4]], "want" => [0 => [new stdClass(), 'cat'], 2 => ['dog', 4]]],
            ["input" => [[1, 2], [3, 4], [5, 6], [3, 4], [7, 8], 9, 10, [11, 12], [1, 2], 9], "want" => [0 => [1, 2], 1 => [3, 4], 2 => [5, 6], 4 => [7, 8], 5 => 9, 6 => 10, 7 => [11, 12]]],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], array_unique_multidimensional($testCase["input"]));
        }
    }

    public function testGetAllLocaleValues(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["base_locale" => "de_DE", "want" => ['de_DE', 'de_DE@euro', 'deu', 'deu_deu', 'german']],
            ["base_locale" => "fr_FR", "want" => ['fr_FR', 'fr_FR@euro', 'french']],
            ["base_locale" => "en_AU", "want" => ['en_AU.utf8', 'en_AU', 'australian']],
            ["base_locale" => "en_GB", "want" => false],
            ["base_locale" => "it_CH", "want" => false],
            ["base_locale" => "no_NO", "want" => false],
            ["base_locale" => "zh_CN", "want" => false],
            ["base_locale" => "literally_anything_else", "want" => false],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], getAllLocaleValues($testCase["base_locale"]));
        }
    }

    public function testHumanReadableBytes(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["input" => 1, "rounding" => 2, "bytesValue" => true, "want" => "1 B"],
            ["input" => 1024, "rounding" => 2, "bytesValue" => true, "want" => "1024 B"],
            ["input" => 1024, "rounding" => 2, "bytesValue" => false, "want" => "1.02 KB"],
            ["input" => 20000, "rounding" => 2, "bytesValue" => true, "want" => "19.53 KB"],
            ["input" => 20000, "rounding" => 2, "bytesValue" => false, "want" => "20 KB"],
            ["input" => 48152665, "rounding" => 2, "bytesValue" => true, "want" => "45.92 MB"],
            ["input" => 48152665, "rounding" => 4, "bytesValue" => true, "want" => "45.922 MB"],
            ["input" => 48152865, "rounding" => 4, "bytesValue" => true, "want" => "45.9222 MB"],
            ["input" => 48152865.6, "rounding" => 10, "bytesValue" => true, "want" => "45.9221511841 MB"],
            ["input" => 16106127360, "rounding" => 2, "bytesValue" => true, "want" => "15 GB"],
            ["input" => 16106127360, "rounding" => 4, "bytesValue" => true, "want" => "15 GB"],
            ["input" => 16106127360, "rounding" => 4, "bytesValue" => false, "want" => "16.1061 GB"],
            ["input" => 2316106127360, "rounding" => 4, "bytesValue" => false, "want" => "2.3161 TB"],
            ["input" => 2316106127360, "rounding" => 2, "bytesValue" => false, "want" => "2.32 TB"],
            ["input" => 2316106127360, "rounding" => 2, "bytesValue" => true, "want" => "2.11 TB"],
            ["input" => 2316106127360, "rounding" => 0, "bytesValue" => false, "want" => "2 TB"],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], humanReadableBytes($testCase["input"], $testCase["rounding"], $testCase["bytesValue"]));
        }
    }

    public function testIsNumber(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["var" => null, "want" => false],
            ["var" => 0, "want" => true],
            ["var" => '', "want" => false],
            ["var" => 'catdog', "want" => false],
            ["var" => 1, "want" => true],
            ["var" => 1.05, "want" => true],
            ["var" => 12398471235861209384712093856109234578, "want" => true],
            ["var" => -600, "want" => true],
            ["var" => -600.054, "want" => true],
            ["var" => "100", "want" => true],
            ["var" => "-100.5", "want" => true],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], isNumber($testCase["var"]));
        }
    }

    public function testDefaultVal(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["val" => null, "default" => "test", "want" => "test"],
            ["val" => "test", "default" => "default", "want" => "test"],
            ["val" => "test", "default" => null, "want" => "test"],
            ["val" => 123, "default" => "456", "want" => 123],
            ["val" => null, "default" => 123, "want" => 123],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], defaultVal($testCase["val"], $testCase["default"]));
        }
    }

    public function testToSlug(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["title" => "this is a test", "want" => "this-is-a-test"],
            ["title" => "Th_Is,iS.A/Tes t?", "want" => "th-is-is-a-tes-t?"],
            ["title" => "  Th_Is ,iS. A/ Tes   t? ", "want" => "--th-is--is--a--tes---t?-"],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], toSlug($testCase["title"]));
        }
    }

    public function testPaginate(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["array" => [1, 2, 3, 4, 5, 6], "pagesize" => 1, "want" => [[1], [2], [3], [4], [5], [6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "pagesize" => 2, "want" => [[1, 2], [3, 4], [5, 6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "pagesize" => 3, "want" => [[1, 2, 3], [4, 5, 6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "pagesize" => 4, "want" => [[1, 2, 3, 4], [5, 6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "pagesize" => 5, "want" => [[1, 2, 3, 4, 5], [6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "pagesize" => 6, "want" => [[1, 2, 3, 4, 5, 6]]],

            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "pagesize" => 1, "want" => [['cat'], [2.01], [[3]], [new stdClass()], ['dog'], [6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "pagesize" => 2, "want" => [['cat', 2.01], [[3], new stdClass()], ['dog', 6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "pagesize" => 3, "want" => [['cat', 2.01, [3]], [new stdClass(), 'dog', 6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "pagesize" => 4, "want" => [['cat', 2.01, [3], new stdClass()], ['dog', 6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "pagesize" => 5, "want" => [['cat', 2.01, [3], new stdClass(), 'dog'], [6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "pagesize" => 6, "want" => [['cat', 2.01, [3], new stdClass(), 'dog', 6]]],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], paginate($testCase["array"], $testCase["pagesize"]));
        }
    }

    public function testColumnize(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["array" => [1, 2, 3, 4, 5, 6], "noOfColumns" => 1, "want" => [[1, 2, 3, 4, 5, 6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "noOfColumns" => 2, "want" => [[1, 2, 3], [4, 5, 6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "noOfColumns" => 3, "want" => [[1, 2], [3, 4], [5, 6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "noOfColumns" => 4, "want" => [[1], [2], [3], [4], [5], [6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "noOfColumns" => 5, "want" => [[1], [2], [3], [4], [5], [6]]],
            ["array" => [1, 2, 3, 4, 5, 6], "noOfColumns" => 6, "want" => [[1], [2], [3], [4], [5], [6]]],

            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "noOfColumns" => 1, "want" => [['cat', 2.01, [3], new stdClass(), 'dog', 6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "noOfColumns" => 2, "want" => [['cat', 2.01, [3]], [new stdClass(), 'dog', 6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "noOfColumns" => 3, "want" => [['cat', 2.01], [[3], new stdClass()], ['dog', 6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "noOfColumns" => 4, "want" => [['cat'], [2.01], [[3]], [new stdClass()], ['dog'], [6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "noOfColumns" => 5, "want" => [['cat'], [2.01], [[3]], [new stdClass()], ['dog'], [6]]],
            ["array" => ['cat', 2.01, [3], new stdClass(), 'dog', 6], "noOfColumns" => 6, "want" => [['cat'], [2.01], [[3]], [new stdClass()], ['dog'], [6]]],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], columnize($testCase["array"], $testCase["noOfColumns"]));
        }
    }

    // public function testRotateImage(): void
    // {
    //     // Untestable - needs image
    // }

    public function testStrcontains(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["haystack" => "this is a test", "needle_array" => ['thi', 'a t'], "want" => true],
            ["haystack" => "this is a test", "needle_array" => ['thsi', 'aa t', 's a'], "want" => true],
            ["haystack" => "this is a test", "needle_array" => ['thsi', 'aa t', 'as a'], "want" => false],
            ["haystack" => "this is a test", "needle_array" => [], "want" => false],
            ["haystack" => "this is a test", "needle_array" => ['this is not a test', 'this is also not a test'], "want" => false],
            ["haystack" => "this is a test", "needle_array" => ['THIS IS A TEST'], "want" => true],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], strcontains($testCase["haystack"], $testCase['needle_array']));
        }
    }

    public function testStartsWith(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["haystack" => "", "needle" => "test", "want" => false],
            ["haystack" => "", "needle" => "", "want" => false],
            ["haystack" => "test", "needle" => "", "want" => false],
            ["haystack" => "", "needle" => "test", "want" => false],
            ["haystack" => "test", "needle" => "test", "want" => true],
            ["haystack" => "this is a test", "needle" => "this", "want" => true],
            ["haystack" => "this is a test", "needle" => [], "want" => false],
            ["haystack" => "this is a test", "needle" => ["test"], "want" => false],
            ["haystack" => "this is a test", "needle" => ["this"], "want" => true],
            ["haystack" => "this is a test", "needle" => ["test", "this"], "want" => true],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], startsWith($testCase["haystack"], $testCase['needle']));
        }
    }

    public function testStrWhitelist(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["dirty_data" => "<this is a test>", "limit" => 2, "want" => "t"],
            ["dirty_data" => "<this is a test>", "limit" => 0, "want" => "this is a test"],
            ["dirty_data" => "<<>><><><>", "limit" => 0, "want" => ""],
            ["dirty_data" => "this is a test,.\/-", "limit" => 0, "want" => "this is a test,./-"],
            ["dirty_data" => "this is a test <><><><>", "limit" => 14, "want" => "this is a test"],
            ["dirty_data" => "/\/\/\/\/\/\/", "limit" => 0, "want" => "///////"],
            ["dirty_data" => "?????????????", "limit" => 2, "want" => ""],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], str_whitelist($testCase["dirty_data"], $testCase['limit']));
        }
    }

    public function testPhoneWhitelist(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["dirty_data" => "(12) 34-56-78- 90", "want" => "(12) 34-56-78- 90"],
            ["dirty_data" => "(12) abc34-def56-ghi78- 90", "want" => "(12) 34-56-78- 90"],
            ["dirty_data" => "(12) abc34-def56-ghi78- 90", "want" => "(12) 34-56-78- 90"],
            ["dirty_data" => "this is a test", "want" => "   "],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], phone_whitelist($testCase["dirty_data"]));
        }
    }

    public function testIntWhitelist(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["dirty_data" => "1234567890", "limit" => 2, "want" => "12"],
            ["dirty_data" => "1234567890", "limit" => 0, "want" => ""],
            ["dirty_data" => "abcdefghi1234567890", "limit" => 100, "want" => "1234567890"],
            ["dirty_data" => "abc1234567890", "limit" => 3, "want" => ""],
            ["dirty_data" => "1234567890", "limit" => 100, "want" => "1234567890"],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], int_whitelist($testCase["dirty_data"], $testCase['limit']));
        }
    }

    public function testGetTimeSelect(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["start" => 8, "end" => 10, "want" => [["08:00 am", "8:00"], ["08:30 am", "8:30"], ["09:00 am", "9:00"], ["09:30 am", "9:30"], ["10:00 am", "10:00"], ["10:30 am", "10:30"]]],
            ["start" => 11, "end" => 13, "want" => [["11:00 am", "11:00"], ["11:30 am", "11:30"], ["12:00 pm", "12:00"], ["12:30 pm", "12:30"], ["01:00 pm", "13:00"], ["01:30 pm", "13:30"]]],
            ["start" => 18, "end" => 20, "want" => [["06:00 pm", "18:00"], ["06:30 pm", "18:30"], ["07:00 pm", "19:00"], ["07:30 pm", "19:30"], ["08:00 pm", "20:00"], ["08:30 pm", "20:30"]]],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], getTimeSelect($testCase["start"], $testCase['end']));
        }
    }


    public function testDateFormats(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["date" => "01/01/1970", "format" => null, "want" => "01/01/1970"],
            ["date" => "0", "format" => null, "want" => null],
            ["date" => null, "format" => null, "want" => null],
            ["date" => "1", "format" => null, "want" => "01/01/1970"],
            ["date" => "1", "format" => "U", "want" => "1"],
            ["date" => time(), "format" => "Y-m-d", "want" => date('Y-m-d')],
            ["date" => "01/02/2022", "format" => "Y-m-d", "want" => "2022-02-01"],
            ["date" => "01/02/2022", "format" => "d/m/Y", "want" => "01/02/2022"],
            ["date" => "01/02/2022", "format" => "m/d/Y", "want" => "02/01/2022"],
            ["date" => "01/02/2022", "format" => "Y-m-d H:i:s", "want" => "2022-02-01 00:00:00"],
            ["date" => "01/02/2022", "format" => DateTimeInterface::ATOM, "want" => "2022-02-01T00:00:00+00:00"],
            ["date" => "02/01/2022", "format" => DateTimeInterface::ATOM, "want" => "2022-01-02T00:00:00+00:00"],
            ["date" => "01/02/2022", "format" => DateTimeInterface::RSS, "want" => "Tue, 01 Feb 2022 00:00:00 +0000"],
            ["date" => "01/02/2022 14:30:00", "format" => DateTimeInterface::ATOM, "want" => "2022-02-01T14:30:00+00:00"],
            ["date" => "01/02/2022 14:30:00", "format" => "H:i", "want" => "14:30"],
            ["date" => "01/02/2022 14:30:00", "format" => "h:i a", "want" => "02:30 pm"],
            ["date" => "14:30:00", "format" => "h:i a", "want" => "02:30 pm"],
            ["date" => "14:30:00", "format" => "Y-m-d h:i a", "want" => date("Y-m-d") . " 02:30 pm"],
        ];

        foreach ($testCases as $testCase) {
            if (is_null($testCase["format"])) {
                $this->assertEquals($testCase["want"], formatDate($testCase["date"]));
            } else {
                $this->assertEquals($testCase["want"], formatDate($testCase["date"], $testCase['format']));
            }
        }
    }

    public function formatNumber(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["number" => 0, "want" => "0.00"],
            ["number" => -0, "want" => "0.00"],
            ["number" => -1000.45678, "want" => -1000.46],
            ["number" => 1000.54321, "want" => 1000.54],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], formatNumber($testCase["number"]));
        }
    }
    
    /**
     * Test formatCurrency() in functions.php.
     *
     * @return void
     */
    public function testFormatCurrency(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["value" => -1, "locale" => "en_AU", "currency" => "AUD", "want" => "-$1.00"],
            ["value" => -1.55, "locale" => "en_AU", "currency" => "AUD", "want" => "-$1.55"],
            ["value" => -1.555, "locale" => "en_AU", "currency" => "AUD", "want" => "-$1.56"],
            ["value" => 0, "locale" => "en_AU", "currency" => "AUD", "want" => "$0.00"],
            ["value" => 0.55, "locale" => "en_AU", "currency" => "AUD", "want" => "$0.55"],
            ["value" => 0.555, "locale" => "en_AU", "currency" => "AUD", "want" => "$0.56"],
            ["value" => 1, "locale" => "en_AU", "currency" => "AUD", "want" => "$1.00"],
            ["value" => 1.55, "locale" => "en_AU", "currency" => "AUD", "want" => "$1.55"],
            ["value" => 1.555, "locale" => "en_AU", "currency" => "AUD", "want" => "$1.56"],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], formatCurrency($testCase["value"], $testCase["locale"], $testCase["currency"]));
        }
    }

    public function testRecursiveArraySearch(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["haystack" => ["this" => ["is" => ["a" => ["test"]]]], "needle" => "test", "want" => "this"],
            ["haystack" => ["this" => ["is" => ["a" => ["not here"]]], "this2" => ["is2" => ["a2" => ["not here"]], ["test" => "test"]]], "needle" => "test", "want" => "this2"],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], recursiveArraySearch($testCase["haystack"], $testCase["needle"]));
        }
    }

    public function testInMultiarray(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["value" => 10, "array" => [10 => "something"], "want" => true],
            ["value" => 10, "array" => ["test" => ["something" => 10]], "want" => true],
            ["value" => 10, "array" => ["test" => ["something" => "else"]], "want" => false],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], in_multiarray($testCase["value"], $testCase["array"]));
        }
    }

    public function testGetValueFromMultiarray(): void
    {
        require_once("system/functions.php");

        $testCases = [
            ["key" => 10, "array" => [10 => "something"], "want" => "something"],
            ["key" => 10, "array" => ["test" => ["something" => 10]], "want" => null],
            ["key" => 10, "array" => ["test" => ["something" => "else"]], "want" => null],
            ["key" => 10, "array" => ["test" => ["something" => "else", 10 => ["test"]]], "want" => ['test']],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], getValueFromMultiarray($testCase["key"], $testCase["array"]));
        }
    }
}
