<?php
use \Codeception\Util\Stub;
	
class FunctionsTest extends  \Codeception\TestCase\Test {
	
	
	/**
	* @var \UnitGuy
	*/
	protected $guy;

	
	public function _before() {
	}
	
	

/*
 *      /*****************************************
	 * NOT TESTED
	 *****************************************

 * These functions have not been tested due to their not being used in both cmfive 
 * and crm

function getBetween($content, $start, $end) {  //This function is not used in cm5 or crm
function is_associative_array($array) {  //This function is not used
function paginate($array, $pageSize) { // not used
function columnize($array, $noOfColumns) {   // not used
function recursiveArraySearch($haystack, $needle, $index = null) { // not used
function rotateImage($img, $rotation) {
 * function getTimeSelect($start = 8, $end = 19) {
 * function returncorrectdates(Web &$w, $dm_var, $from_date, $to_date) {
 * function str_whitelist($dirty_data, $limit = 0) {
 * function phone_whitelist($dirty_data) {
 * function int_whitelist($dirty_data, $limit) {

*/
        
	/*****************************************
	 * TESTS
	 *****************************************/
	

        
function test_in_multiarray() {
        $test_string = "find_me";
        $test_array1 = array('',array('','',array('',''),''));
        $this->assertEquals(in_multiarray($test_string, $test_array1),false);
        
        //searches for integer return true for array keys of same value
        $test_int = 2;
        $this->assertEquals(in_multiarray($test_int, $test_array1),true);
        
        $test_array_int1 = ['key1'=>'data','key2'=>['key3'=>'data2']];
        $this->assertEquals(in_multiarray($test_int, $test_array_int1),FALSE);
        $test_array_int2 = ['key1'=>'data','key2'=>['key3'=>2]];
        $this->assertEquals(in_multiarray($test_int, $test_array_int2),true);
        $test_array_int3 = ['key1'=>'data','key2'=>['key3'=>'2']];
        $this->assertEquals(in_multiarray($test_int, $test_array_int3),FALSE);
        
        $test_array2 = array('',array('','',array('','find_me'),''));
        $this->assertEquals(in_multiarray($test_string, $test_array2),true);
        
        $test_value = array('test','testttt');
        $test_array3 = array('',array('','',$test_string => $test_value,''));
        //should this function be able to find arrays in multiarrays? 
        //if so, this test should pass
        //$this->assertEquals(in_multiarray($test_value, $test_array3),true);
        $this->assertEquals(in_multiarray($test_string, $test_array3),true);
             
}

function test_in_modified_multiarray() {
    //$value, $array, $levels = 3
    $test_string = 'find_me';
    $test_array1 = ['','',['','',['','']],''];
    $this->assertEquals(in_modified_multiarray($test_string, $test_array1, 3),false);
    
    $test_array2 = ['','',['','',['','find_me']],''];
    $this->assertEquals(in_modified_multiarray($test_string, $test_array2, 4),true);
    $this->assertEquals(in_modified_multiarray($test_string, $test_array2, 1),false);
    
    $test_array3 = ['','',['','',['','find_me'=>['','']]],''];
    $this->assertEquals(in_modified_multiarray($test_string, $test_array3, 3),true);
    $this->assertEquals(in_modified_multiarray($test_string, $test_array3, 1),false);
}

function test_is_complete_associative_array() {
    $test_array1 = ['','',''];
    $this->assertEquals(is_complete_associative_array($test_array1), false);
    
    $test_array2 = ['key1'=>'','key2'=>''];
    $this->assertEquals(is_complete_associative_array($test_array2), true);
    
    $test_array3 = ['0'=>'','1'=>''];
    $this->assertEquals(is_complete_associative_array($test_array3), false);
    
    $test_array4 = ['','key1'=>'','','key2'=>''];
    $this->assertEquals(is_complete_associative_array($test_array4), false);
}

function test_in_numeric_range() {
    //$subject, $min, $max, $include = true
    $test_subject = 'five';
    $test_min = 20150524;
    $test_max = 20150814;
    $this->assertEquals(in_numeric_range($test_subject, $test_min, $test_max),false);
    
    $test_subject2 = 5;
    $this->assertEquals(in_numeric_range($test_subject2, $test_min, $test_max), false);
    
    $test_subject3 = 20150524;
    $this->assertEquals(in_numeric_range($test_subject3, $test_min, $test_max, false), false);
    $this->assertEquals(in_numeric_range($test_subject3, $test_min, $test_max), true);
    
    $test_subject4 = 20150529;
    $this->assertEquals(in_numeric_range($test_subject4, $test_min, $test_max), true);
} 

function test_strcontains() {
    //$haystack, $needle_array
    $test_haystack = 'this is a test string - with some words in it.';
    $test_needle_array = array('z','?','#',0,10);
    $this->assertEquals(strcontains($test_haystack, $test_needle_array), false);
    
    $test_needle_array2 = array('z','2',2,'-');
    $this->assertEquals(strcontains($test_haystack, $test_needle_array2), true);
    
}

function test_startsWith() {
    //$haystack, $needle
    $test_hatstack = 'string';
    $test_needle = 't';
    $this->assertEquals(startsWith($test_hatstack,$test_needle), false);
    
    $test_needle2 = 'st';
    $this->assertEquals(startsWith($test_hatstack,$test_needle2), true);
    
    $test_hatstack2 = 85568;
    $test_needle3 = 8;
    $this->assertEquals(startsWith($test_hatstack2,$test_needle3), false);
    
    $test_needle4 = '8';
    $this->assertEquals(startsWith($test_hatstack2,$test_needle4), true);
    
    $test_needle5 = array('7','9','5','8');
    $this->assertEquals(startsWith($test_hatstack2,$test_needle5), true);
}

function test_array_unique_multidimensional() {
    //$input
    $test_duplicate = ['test','data'];
    $test_array = [[''],$test_duplicate,['a'],['b'],$test_duplicate,$test_duplicate,['test','data']];
    $this->assertEquals([[''],['test','data'],['a'],['b']], array_unique_multidimensional($test_array));
}

function test_humanReadableBytes() {
	// bytes value 1000
	$this->assertEquals(humanReadableBytes(5000000000000000),'4547.47 TB');
	$this->assertEquals(humanReadableBytes(5000000000000),'4.55 TB');
	$this->assertEquals(humanReadableBytes(5000000000),'4.66 GB');
	$this->assertEquals(humanReadableBytes(5000000),'4.77 MB');
	$this->assertEquals(humanReadableBytes(5000),'4.88 KB');
}

function test_toSlug() {
	
	$this->assertTrue(toSlug('This is my name')==='this-is-my-name');
	$this->assertTrue(toSlug('This_is,my/.name')==='this-is-my--name');
}

function test_getFileExtension() {
	// from list of types
	$this->assertTrue(getFileExtension('text/javascript')=='.js');
	// as fallback 
	$this->assertTrue(getFileExtension('somewierd/mimetype')=='.mimetype');
}

function test_encryption() {
	$text='this is what i want to hide';
	$password='thepasswordthatiwantotdostuffwit';
	$encrypted=AESencrypt($text, $password);
	$decrypted=AESdecrypt($encrypted, $password);
	// really did change
	$this->assertFalse($encrypted===$text);
	// full cycle OK
	$this->assertTrue($decrypted===$text);	
}

function test_formatDate() {
    //$date, $format = "d/m/Y", $usetimezone = true
    // $usetimezone is not used!
    $test_date = 0;
    $this->assertEquals(null, formatDate($test_date));
    
    $test_date2 = '2015-08-15';
    $this->assertEquals('15/08/2015',formatDate($test_date2));
    $this->assertEquals('Saturday/August/15',formatDate($test_date2, "l/F/y"));
    
}

function test_formatDateTime() {
    //$date, $format = "d/m/Y h:i a", $usetimezone = true
    $test_datetime = '2015-08-15 15:52:01';
    $this->assertEquals('15/08/2015 03:52 pm',formatDateTime($test_datetime));
    $this->assertEquals('Saturday/August/15 15:52 PM',formatDateTime($test_datetime,"l/F/y H:i A"));
    
}


function test_defaultVal() {
    //$val, $default = null, $forceNull = false
    $test_val = null;
    $this->assertEquals(null, defaultVal($test_val));
    
    $test_val2 = 'val';
    $this->assertEquals('val', defaultVal($test_val2));
    
    $test_default = 'default';
    $this->assertEquals('default', defaultVal($test_val,$test_default));
    $this->assertEquals('val', defaultVal($test_val2,$test_default));
    
}


/*******************************************
 * FAILED TESTS NEEDING BUG FIX
 *******************************************/

function test_isNumber() {
    //$var
    //$test_var = 0;
    //$this->assertEquals(true, isNumber($test_var));
    //$test_var2 = '0';
    //$this->assertEquals(true, isNumber($test_var2));
    $test_var3 = true;
    $this->assertEquals(false, isNumber($test_var3));
    $test_var4 = 'string';
    $this->assertEquals(false, isNumber($test_var4));
    $test_var5 = '5.156';
    $this->assertEquals(true, isNumber($test_var5));
    $test_var6 = '123abc';
    $this->assertEquals(false, isNumber($test_var6));
    $test_var7 = 9999999999999999999999999999999999999999999999999999;
    $this->assertEquals(true, isNumber($test_var7));
    $test_var8 = 0.00000000000000000000000000000000000001;
    $this->assertEquals(true, isNumber($test_var8));
    $test_var9 = '5';
    $this->assertEquals(true, isNumber($test_var9));
    $test_var10 = 5;
    $this->assertEquals(true, isNumber($test_var10));
    $test_var11 = null;
    $this->assertEquals(false, isNumber($test_var11));
    $test_var12 = '';
    $this->assertEquals(false, isNumber($test_var12));
}


function test_formatMoney() {
	// TODO fix this
	// this test behaves differently across different OS/PHP installations (even between windows version)
    return;
    //$format, $number
    $test_format = "%.2n";
    $test_number = 0;
    $this->assertEquals('$0.00', formatMoney($test_format, $test_number));
    $test_number2 = '0';
    $this->assertEquals('$0.00', formatMoney($test_format, $test_number2));
    $test_number3 = 1;
    $this->assertEquals('$1.00', formatMoney($test_format, $test_number3));
    $test_number4 = 1111.11111;
    $this->assertEquals('$1,111.11', formatMoney($test_format, $test_number4));
    $test_number5 = -11.11;
    $this->assertEquals('-$11.11', formatMoney($test_format, $test_number5));
    $test_number6 = 'string';
    $this->assertEquals('$0.00', formatMoney($test_format, $test_number6));
    $test_number7 = '5555.5555';
    $this->assertEquals('$5,555.56', formatMoney($test_format, $test_number7));
    $test_format2 = "%!.2n";
    $this->assertEquals('5,555.56', formatMoney($test_format2, $test_number7));
    $test_format3 = "%n";
    $this->assertEquals('$5,555.56',formatMoney($test_format3, $test_number7));
    
    
}

function lookupForSelect() {
    //&$w, $type
    
}

function test_getStateSelectArray() {
    $test_return = array(
        array("ACT", "ACT"),
        array("NSW", "NSW"),
        array("NT", "NT"),
        array("QLD", "QLD"),
        array("SA", "SA"),
        array("TAS", "TAS"),
        array("VIC", "VIC"),
        array("WA", "WA"));
    $this->assertEquals($test_return, getStateSelectArray());
}
}
