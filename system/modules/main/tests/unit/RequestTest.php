<?php

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * Test Request::int().
     *
     * @return void
     */
    public function testInt(): void
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
    public function testFloat(): void
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
    public function testBool(): void
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
    public function testString(): void
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
     * Test Request::array().
     *
     * @return void
     */
    public function testArray(): void
    {
        require_once("system/web.php");
        $w = new Web();

        $testCases = [
            // Test the different arrays work as expected.
            ["key" => "test-array-empty", "value" => [], "default" => [1, 2, 3], "want" => []],
            ["key" => "test-array-scalar", "value" => [1, 2, 3], "default" => [1, 2, 3], "want" => [1, 2, 3]],
            ["key" => "test-array-compound", "value" => [new stdClass()], "default" => [1, 2, 3], "want" => [new stdClass()]],
            // Test the default parameter works as expected for non-array types.
            ["key" => "test-default-null", "value" => null, "default" => [1, 2, 3], "want" => [1, 2, 3]],
            ["key" => "test-default-int", "value" => 1, "default" => [1, 2, 3], "want" => [1, 2, 3]],
            ["key" => "test-default-class", "value" => new stdClass(), "default" => [1, 2, 3], "want" => [1, 2, 3]],
        ];

        foreach ($testCases as $testCase) {
            $_REQUEST[$testCase["key"]] = $testCase["value"];
            $this->assertEquals($testCase["want"], Request::array($testCase["key"], $testCase["default"]));
            unset($_REQUEST[$testCase["key"]]);
        }

        // Test that an empty array is returned when that key doesn't exist.
        $this->assertEquals([], Request::array("test-missing"));

        // Test that the default parameter works as expected when the key doesn't exist.
        $this->assertEquals([1, 2, 3], Request::array("test-missing-default", [1, 2, 3]));
    }

    /**
     * Test Request::mixed().
     *
     * @return void
     */
    public function testMixed(): void
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

    /**
     * Test Request::has().
     *
     * @return void
     */
    public function testHas(): void
    {
        require_once("system/web.php");
        $w = new Web();

        // Test that a key is not found when it doesn't exist.
        $this->assertEquals(false, Request::has("key-1"));

        // Test that a key is found when it does exist.
        $_REQUEST["key-1"] = "value-1";
        $this->assertEquals(true, Request::has("key-1"));
        unset($_REQUEST["key-1"]);
    }

    /**
     * Test Request::hasAny().
     *
     * @return void
     */
    public function testHasAny(): void
    {
        require_once("system/web.php");
        $w = new Web();

        // Test that a single key is not found when it doesn't exist.
        $this->assertEquals(false, Request::hasAny("key-1"));
        // Test that multiple keys aren't found when they don't exist.
        $this->assertEquals(false, Request::hasAny("key-1", "key-2", "key-3"));

        // Test that a single key is found when it does exist.
        $_REQUEST["key-1"] = "value-1";
        $this->assertEquals(true, Request::hasAny("key-1"));

        // Test that any of these keys are found when some exist and some don't.
        unset($_REQUEST["key-1"]);
        $_REQUEST["key-2"] = "value-1";
        $_REQUEST["key-3"] = "value-1";
        $this->assertEquals(true, Request::hasAny("key-1", "key-2", "key-3"));
        unset($_REQUEST["key-2"]);
        unset($_REQUEST["key-3"]);
    }

    /**
     * Test Request::hasAll()
     *
     * @return void
     */
    public function testHasAll(): void
    {
        require_once("system/web.php");
        $w = new Web();

        // Test that a single key is not found when it doesn't exist.
        $this->assertEquals(false, Request::hasAll("key-1"));
        // Test that multiple keys aren't found when they don't exist.
        $this->assertEquals(false, Request::hasAll("key-1", "key-2", "key-3"));

        // Test that a single key is found when it does exist.
        $_REQUEST["key-1"] = "value-1";
        $this->assertEquals(true, Request::hasAll("key-1"));

        // Test that any of these keys are found when some exist and some don't.
        $_REQUEST["key-2"] = "value-1";
        $_REQUEST["key-3"] = "value-1";
        $this->assertEquals(true, Request::hasAll("key-1", "key-2", "key-3"));
        unset($_REQUEST["key-1"]);
        unset($_REQUEST["key-2"]);
        unset($_REQUEST["key-3"]);
    }
}
