<?php

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * Test Request::int().
     *
     * @return void
     */
    public function testInt()
    {
        require_once("system/web.php");
        $w = new Web();

        $testCases = [
            // Test the casting to an int works as expected for scalar types.
            ["key" => "test-cast", "value" => 1, "default" => null, "want" => 1],
            ["key" => "test-cast", "value" => 1.5, "default" => null, "want" => 1],
            ["key" => "test-cast", "value" => true, "default" => null, "want" => 1],
            ["key" => "test-cast", "value" => "1", "default" => null, "want" => 1],
            // Test the default parameter works as expected for non-scalar types.
            ["key" => "test-default", "value" => [], "default" => 1, "want" => 1],
            ["key" => "test-default", "value" => new stdClass(), "default" => 1, "want" => 1],
            ["key" => "test-default", "value" => null, "default" => 1, "want" => 1],
        ];

        foreach ($testCases as $testCase) {
            $_REQUEST[$testCase["key"]] = $testCase["value"];
            $this->assertEquals($testCase["want"], Request::int($testCase["key"], $testCase["default"]));
            unset($_REQUEST[$testCase["key"]]);
        }

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::int("test-missing"));

        // Test that the default parameter works as expected when the key doesn't exist.
        $this->assertEquals(1, Request::int("test-missing-default", 1));
    }

    /**
     * Test Request::float().
     *
     * @return void
     */
    public function testFloat()
    {
        require_once("system/web.php");
        $w = new Web();

        $testCases = [
            // Test the casting to an int works as expected for scalar types.
            ["key" => "test-cast", "value" => 1, "default" => null, "want" => 1.0],
            ["key" => "test-cast", "value" => 1.5, "default" => null, "want" => 1.5],
            ["key" => "test-cast", "value" => true, "default" => null, "want" => 1.0],
            ["key" => "test-cast", "value" => "1.5", "default" => null, "want" => 1.5],
            // Test the default parameter works as expected for non-scalar types.
            ["key" => "test-default", "value" => [], "default" => 1.5, "want" => 1.5],
            ["key" => "test-default", "value" => new stdClass(), "default" => 1.5, "want" => 1.5],
            ["key" => "test-default", "value" => null, "default" => 1.5, "want" => 1.5],
        ];

        foreach ($testCases as $testCase) {
            $_REQUEST[$testCase["key"]] = $testCase["value"];
            $this->assertEquals($testCase["want"], Request::float($testCase["key"], $testCase["default"]));
            unset($_REQUEST[$testCase["key"]]);
        }

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::float("test-missing"));

        // Test that the default parameter works as expected when the key doesn't exist.
        $this->assertEquals(1.5, Request::float("test-missing-default", 1.5));
    }

    /**
     * Test Request::bool().
     *
     * @return void
     */
    public function testBool()
    {
        require_once("system/web.php");
        $w = new Web();

        $testCases = [
            // Test the casting to an int works as expected for scalar types.
            ["key" => "test-cast", "value" => 1, "default" => null, "want" => true],
            ["key" => "test-cast", "value" => 0, "default" => null, "want" => false],
            ["key" => "test-cast", "value" => 1.5, "default" => null, "want" => true],
            ["key" => "test-cast", "value" => true, "default" => null, "want" => true],
            ["key" => "test-cast", "value" => false, "default" => null, "want" => false],
            ["key" => "test-cast", "value" => "1.5", "default" => null, "want" => true],
            // Test the default parameter works as expected for non-scalar types.
            ["key" => "test-default", "value" => [], "default" => true, "want" => true],
            ["key" => "test-default", "value" => new stdClass(), "default" => true, "want" => true],
            ["key" => "test-default", "value" => null, "default" => true, "want" => true],
        ];

        foreach ($testCases as $testCase) {
            $_REQUEST[$testCase["key"]] = $testCase["value"];
            $this->assertEquals($testCase["want"], Request::bool($testCase["key"], $testCase["default"]));
            unset($_REQUEST[$testCase["key"]]);
        }

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::bool("test-missing"));

        // Test that the default parameter works as expected when the key doesn't exist.
        $this->assertEquals(true, Request::bool("test-missing-default", true));
    }

    /**
     * Test Request::string().
     *
     * @return void
     */
    public function testString()
    {
        require_once("system/web.php");
        $w = new Web();

        $testCases = [
            // Test the casting to an int works as expected for scalar types.
            ["key" => "test-cast", "value" => 1, "default" => null, "want" => "1"],
            ["key" => "test-cast", "value" => 1.5, "default" => null, "want" => "1.5"],
            ["key" => "test-cast", "value" => true, "default" => null, "want" => "1"],
            ["key" => "test-cast", "value" => "1.5", "default" => null, "want" => "1.5"],
            // Test the default parameter works as expected for non-scalar types.
            ["key" => "test-default", "value" => [], "default" => "1", "want" => "1"],
            ["key" => "test-default", "value" => new stdClass(), "default" => "1", "want" => "1"],
            ["key" => "test-default", "value" => null, "default" => "1", "want" => "1"],
        ];

        foreach ($testCases as $testCase) {
            $_REQUEST[$testCase["key"]] = $testCase["value"];
            $this->assertEquals($testCase["want"], Request::string($testCase["key"], $testCase["default"]));
            unset($_REQUEST[$testCase["key"]]);
        }

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::string("test-missing"));

        // Test that the default parameter works as expected when the key doesn't exist.
        $this->assertEquals("1", Request::string("test-missing-default", "1"));
    }

    /**
     * Test Request::mixed().
     *
     * @return void
     */
    public function testMixed()
    {
        require_once("system/web.php");
        $w = new Web();

        // Set the value to an int and test that it can be retrieved.
        $_REQUEST["test-mixed"] = 1;
        $this->assertEquals(1, Request::mixed("test-mixed"));

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::mixed("test-mixed-null"));

        // Test that a default value is returned when that key doesn't exist.
        $this->assertEquals(2, Request::mixed("test-mixed-default", 2));
    }
}
