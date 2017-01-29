<?php
namespace Helper;
if (!defined('DS'))  define('DS', DIRECTORY_SEPARATOR);

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
	
	/*******************
     * Create a test folder containing directory tree inside
     * subFolder1/
     * 	unit.suite.yml
     *  unit/
     * 		funTest.php
     * subFolder2/
     * 	unit.suite.yml
     *  unit/
     * 		boringTestCest.php
     * 		interestingTestCept.php
     * README.txt
     * @param path to create test folder inside
     ********************/
    public function createTestFolderTree($path)  {
		@mkdir($path,0777,true);
		@mkdir($path.DS.'testFolder');
		@mkdir($path.DS.'testFolder'.DS.'subFolder1');
		@mkdir($path.DS.'testFolder'.DS.'subFolder2');
		@mkdir($path.DS.'testFolder'.DS.'subFolder1'.DS.'unit');
		file_put_contents($path.DS.'testFolder'.DS.'subFolder1'.DS.'unit.suite.yml',' ');
		file_put_contents($path.DS.'testFolder'.DS.'subFolder1'.DS.'unit'.DS.'funTest.php',' ');
		@mkdir($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit');
		file_put_contents($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit.suite.yml',' ');
		file_put_contents($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit'.DS.'boringTestCest.php',' ');
		file_put_contents($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit'.DS.'interestingTestCept.php',' ');
		file_put_contents($path.DS.'testFolder'.DS.'README.txt','test text');
	}
	/*******************
     * Check a test folder structure containing a file and 2 sub folders
     * exists at the specified path
     * @param path to check if is test folder 
     ********************/
    public function isTestFolderTree($path) {
		if (
		is_dir($path.DS.'testFolder') &&
		is_dir($path.DS.'testFolder'.DS.'subFolder1') &&
		is_dir($path.DS.'testFolder'.DS.'subFolder2') &&
		is_dir($path.DS.'testFolder'.DS.'subFolder1'.DS.'unit') &&
		is_dir($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit') &&
		file_exists($path.DS.'testFolder'.DS.'subFolder1'.DS.'unit.suite.yml') && 
		file_exists($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit.suite.yml') && 
		file_exists($path.DS.'testFolder'.DS.'subFolder1'.DS.'unit'.DS.'funTest.php') && 
		file_exists($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit'.DS.'boringTestCest.php') && 
		file_exists($path.DS.'testFolder'.DS.'subFolder2'.DS.'unit'.DS.'interestingTestCept.php') && 
		file_exists($path.DS.'testFolder'.DS.'README.txt')
		) {
			return true;
		} else {
			return false;
		}
	}
	
}
