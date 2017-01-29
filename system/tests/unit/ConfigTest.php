<?php
use \Codeception\Util\Stub;


class ConfigTest extends  \Codeception\TestCase\Test {
	
	/**
	* @var \UnitGuy
	*/
	protected $guy;
	
	public $config;
	
	
	public function _before() {
		//echo "ENV:".getenv('thisTestRun_testIncludePath');
		//die();
		//chdir(getenv('thisTestRun_testIncludePath'));
		//require_once "system/classes/Config.php";

		 $this->config = Stub::construct('Config');
	}
	
	public function testGetAndSet() {
		// Test empty get
		$this->assertEmpty($this->config->get('i_dont_exist'));
		
		// Test with setting empty array (depends on set working)
		$this->config->set('test', []);
		$this->assertEquals([], $this->config->get('test'));
		
		// Test with integer
		$this->config->set('test', 123);
		$this->assertEquals(123, $this->config->get('test'));
		
		// Test with string
		$this->config->set('test', 'i am a test');
		$this->assertEquals('i am a test', $this->config->get('test'));
		
		// Test with array
		$this->config->set('test', [123, 'i am a test']);
		$this->assertEquals([123, 'i am a test'], $this->config->get('test'));
		
		// Test with more than one level
		$this->config->set('test.second_level', 'i am a test');
		$this->assertEquals([0 => 123, 1 => 'i am a test', 'second_level' => 'i am a test'], $this->config->get('test'));
		$this->assertEquals('i am a test', $this->config->get('test.second_level'));
	}
	
	/**
	 * @depends testGetAndSet
	 */
	public function testKeys() {
		// Test default usage is empty
		// Default usage requires three array values to produce a result: array("topmenu", "active", "path");
		// You can override this functionality by providing true to ::keys
		$this->assertEmpty($this->config->keys());
		
		$this->assertEquals([0 => 'test'], $this->config->keys(true));
	}
	
	public function testAppend() {
		
	}
	
	public function testToJson() {
		
	}
	
	public function testFromJson() {
		
	}
	
}
