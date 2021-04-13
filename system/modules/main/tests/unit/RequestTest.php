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

        // Set the value to an int and test that it can be retrieved.
        $_REQUEST["test-int"] = 1;
        $this->assertEquals(1, Request::int("test-int"));

        // Set the value to not an int and test that the default value is returned.
        $_REQUEST["test-int"] = "not-an-int";
        $this->assertEquals(2, Request::int("test-int", 2));

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::int("test-int-null"));

        // Test that a default value is returned when that key doesn't exist.
        $this->assertEquals(2, Request::int("test-int-default", 2));
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

        // Set the value to a float and test that it can be retrieved.
        $_REQUEST["test-float"] = 2.5;
        $this->assertEquals(2.5, Request::float("test-float"));

        // Set the value to not a float and test that the default value is returned.
        $_REQUEST["test-float"] = "not-a-float";
        $this->assertEquals(3.5, Request::float("test-float", 3.5));

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::float("test-float-null"));

        // Test that a default value is returned when that key doesn't exist.
        $this->assertEquals(3.5, Request::float("test-float-default", 3.5));
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

        // Set the value to a bool and test that it can be retrieved.
        $_REQUEST["test-bool"] = false;
        $this->assertEquals(false, Request::bool("test-bool"));

        // Set the value to not a bool and test that the default value is returned.
        $_REQUEST["test-bool"] = "not-a-bool";
        $this->assertEquals(true, Request::bool("test-bool", true));

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::bool("test-bool-null"));

        // Test that a default value is returned when that key doesn't exist.
        $this->assertEquals(true, Request::bool("test-bool-default", true));
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

        // Set the value to a string and test that it can be retrieved.
        $_REQUEST["test-string"] = "test-value";
        $this->assertEquals("test-value", Request::string("test-string"));

        // Set the value to not a string and test that the default value is returned.
        $_REQUEST["test-string"] = 1;
        $this->assertEquals("default-test-value", Request::string("test-string", "default-test-value"));

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::string("test-string-null"));

        // Test that a default value is returned when that key doesn't exist.
        $this->assertEquals(true, Request::string("test-string-default", true));
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
        $_REQUEST["test-int"] = 1;
        $this->assertEquals(1, Request::int("test-int"));

        // Test that null is returned when that key doesn't exist.
        $this->assertEquals(null, Request::int("test-int-null"));

        // Test that a default value is returned when that key doesn't exist.
        $this->assertEquals(2, Request::int("test-int-default", 2));
    }
}
