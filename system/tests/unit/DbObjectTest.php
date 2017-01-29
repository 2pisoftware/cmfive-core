<?php
use \Codeception\Util\Stub;
	
class DbObjectTest extends  \Codeception\TestCase\Test {
	
	
	/**
	* @var \UnitGuy
	*/
	protected $guy;
	
	public $web;
	public $dbService;
	
	public function _before() {
		$this->web = Stub::construct("Web",[],[]);
		$this->web->initDb();
		$this->dbService = new DbService($this->web);
	}
	
	function captureOutput($class,$functionToRun,$arguments=[]) {
		$t = ob_get_clean(); // get current output buffer and stopping output buffering
		ob_start(); // start output buffering
		call_user_func_array(array($class,$functionToRun),$arguments);
		//$class->$functionToRun($arguments);
		$generated=ob_get_contents();
		ob_end_clean();
		ob_start(); // start output buffering
		echo($t); // restore output bufferob_start();
		//echo($generated);
		return $generated;
	}

	function createDbRecord($objectName,$data) {
		$testData1=new $objectName($this->web);
		$testData1->fill($data);
		$testData1->insert();
	}
	
	
	/*****************************************
	 * TESTS
	 *****************************************/
	function test_setPassword() {
		$noLabel= new TestmoduleFoodNoLabel($this->web);
		$noLabel->setPassword('fredfredfredfred');
		$this->assertEquals(Config::get('system.password_salt'),'fredfredfredfred');
		// no change for false evaluation of value
		$noLabel->setPassword(false);
		$this->assertEquals(Config::get('system.password_salt'),'fredfredfredfred');
	}
	
	function test_decrypt() {
		$data= new TestmoduleData($this->web);
		$data->setPassword('fredfredfredfred');
		$data->s_data=AESencrypt('mysecret', 'fredfredfredfred');
		//codecept_debug();
		$data->decrypt();
		$this->assertEquals($data->s_data,'mysecret');
	}
    
	function test_labelGenerators() {
		//function test_selectOptionTitle() {}
		//function test_getSelectOptionTitle() {}
		//function test_getSelectOptionValue() {}
		//function printSearchTitle() {
		//function printSearchListing() {
		//function printSearchUrl() {
		//public function __toString() {
		$noLabel= new TestmoduleFoodNoLabel($this->web);
		$noLabel->id=999;
		$this->assertEquals($noLabel->__toString(),'TestmoduleFoodNoLabel[999]');
		$this->assertEquals($noLabel->printSearchTitle(),'TestmoduleFoodNoLabel[999]');
		$this->assertEquals($noLabel->printSearchListing(),'TestmoduleFoodNoLabel[999]');
		$this->assertNull($noLabel->printSearchUrl());
		$this->assertEquals($noLabel->getSelectOptionTitle(),'999');
		$hasTitle= new TestmoduleFoodHasTitle($this->web);
		$hasTitle->title='my title';
		$this->assertEquals($hasTitle->getSelectOptionTitle(),'my title');
		$hasName= new TestmoduleFoodHasName($this->web);
		$hasName->name='my name';
		$this->assertEquals($hasName->getSelectOptionTitle(),'my name');
	}
	
	function test_toLink() { //$class = null, $target = null, $user = null) {
		$data= new TestmoduleData($this->web);
		$data->title='my title';
		$data->id=99;
		$user=new User($this->web);
		$link=$data->toLink('myclass','_new',$user);
		//codecept_debug($link);
		$this->assertEquals($link,"<a href='http://localhost/' class='myclass' target='_new' >TestmoduleData[99]</a>");
		$data= new TestmoduleFoodNoLabel($this->web);
		$data->title='my title';
		$data->id=99;
		$link=$data->toLink('myclass','_new',$user);
		//codecept_debug($link);
		$this->assertEquals($link,"TestmoduleFoodNoLabel[99]");
	}

    function test_readConvert() {
		$data= new TestmoduleData($this->web);
		$this->assertEquals($data->readConvert('d_fred','12/4/2012'),$data->d2Time('12/4/2012'));
		$this->assertEquals($data->readConvert('dt_fred','12/4/2012'),$data->dt2Time('12/4/2012'));
		$this->assertEquals($data->readConvert('fred','eek'),'eek');
	} //$k, $v) {

    function test_updateConvert() {
		$data= new TestmoduleData($this->web);
		//1334152800
		$this->assertEquals($data->updateConvert('d_fred','12/4/2012'),$data->time2D('12/4/2012'));
		$this->assertEquals($data->updateConvert('dt_fred','12/4/2012'),$data->time2Dt('12/4/2012'));
		$this->assertEquals($data->updateConvert('t_fred',99999999),$data->time2T(99999999));
		$this->assertEquals($data->updateConvert('s_fred','mysecret'),AESencrypt('mysecret', Config::get('system.password_salt')));
		// no conversion
		$this->assertEquals($data->updateConvert('fred','eek'),'eek');
	} //$k, $v) {
    
    function test_getObjectVars() {
		$data= new TestmoduleData($this->web);
		$this->assertEquals($data->getObjectVars(),['title','data','d_last_known','t_killed','dt_born','s_data','id']);
	} //) {
 
	function test_fill() {
		$data= new TestmoduleData($this->web);
		$data->fill(['data'=>'thedata','title'=> 'thetitle','notafield'=>'some ignored value','d_last_known'=>'65765766667']);
		$this->assertEquals($data->data,'thedata');
		$this->assertEquals($data->title,'thetitle');
		$this->assertEquals($data->d_last_known,'65765766667');
		$data->fill(['data'=>'thedata','title'=> 'thetitle','notafield'=>'some ignored value','d_last_known'=>'65765766667'],true);
		$this->assertEquals($data->d_last_known,$data->d2Time('65765766667'));
	} //$row, $convert = false) {}
	
	function test_copy() {} //$saveToDB = false) {}
	
	function test_toArray() {
		$data= new TestmoduleData($this->web);
		$fill=['title'=>'the title','data'=>'the data','d_last_known'=>'the last date known','t_killed'=>'time killed','dt_born'=>'date born','s_data'=>'secret data','id'=>'the ID'];
		$data->fill($fill);
		$this->assertEquals($data->toArray(),$fill);
		//codecept_debug($data->toArray());
	}

	function test_getDbTableName() {
		//function _tn() {
    	$hasName=new TestmoduleFoodHasName($this->web);
		$hasTitle=new TestmoduleFoodHasTitle($this->web);
		$noLabel=new TestmoduleFoodNoLabel($this->web);
		// class var
		$this->assertEquals($hasName->_tn(),'patch_testmodule_food_has_name');
		// static
		$this->assertEquals($hasTitle->_tn(),'patch_testmodule_food_has_title');
		// default from get_class munged
		$this->assertEquals($noLabel->_tn(),'testmodule_food_no_label');
	}
   
	function test_getDbTableColumnNames() {
		$hasName=new TestmoduleFoodHasName($this->web);
		$hasTitle=new TestmoduleFoodHasTitle($this->web);
		$noLabel=new TestmoduleFoodNoLabel($this->web);
		
		$this->assertEquals($hasName->getDbTableColumnNames(),['id','name']);
		$this->assertEquals($hasTitle->getDbTableColumnNames(),['id','title']);
		$this->assertEquals($noLabel->getDbTableColumnNames(),['id','data']);
	}

	function test_getHumanReadableAttributeName() { //$attribute) {
		$data=new TestmoduleData($this->web);
		$in=['d_jo','dt_jo','t_jo','t_dt_jo','customer_id','my_long_name'];
		$out=['Jo','Jo','Jo','Dt Jo','Customer','My Long Name'];
		foreach ($in as $key=>$attribute) {
			$this->assertEquals($data->getHumanReadableAttributeName($attribute),$out[$key]); 
		}
	}
	
    function test_getDbColumnName() { //$attr) {
		//function _cn($attr) {
		$data=new TestmoduleData($this->web);
		// just returns incoming value
		$this->assertEquals($data->_cn('arandomvalue'),'arandomvalue');
	}



    function test_getIndexContent() {
		$data= new TestmoduleData($this->web);
		$data->id='99';
		$data->title='thetitle';
		$data->data='thedata';
		//codecept_debug($data->getIndexContent());
		$this->assertEquals($data->getIndexContent(),'thetitle thedata interestingly thern testmoduledata::99');
		// TODO IMPLEMENT HOOKS AND TRY THE $ignoreAdditional parameter
	} //$ignoreAdditional = true) {
    
	function test_validate() {
		TestmoduleData::$_validation = [
			"title" => array('required'),
			"data" => array('required','number'),
			"notafield" => array('required'),
		];
		$data= new TestmoduleData($this->web);
		$data->title='thetitle';
		$data->data='99';
		$validationResult=$data->validate();
		$this->assertEquals($validationResult['success'],true);
		$this->assertEquals($validationResult['valid'],['title','data','data']);
		$this->assertEquals($validationResult['invalid'],[]);
		//codecept_debug();
		$data->data='the stuff';
		$validationResult=$data->validate();
		$this->assertEquals($validationResult['success'],false);
		$this->assertEquals($validationResult['valid'],['title','data']);
		$this->assertEquals($validationResult['invalid'],['data'=>['Invalid Number']]);
		//codecept_debug($data->validate());
		$data->data='99';
		$data->title='';
		$validationResult=$data->validate();
		$this->assertEquals($validationResult['success'],false);
		$this->assertEquals($validationResult['valid'],['data','data']);
		$this->assertEquals($validationResult['invalid'],['title'=>['Required Field']]);
		//codecept_debug($data->validate());
		TestmoduleData::$_validation=[];
	}
	
	function test_getSelectOptions() {
		$data= new TestmoduleData($this->web);
		// no options
		$this->assertEquals($data->getSelectOptions('title'),[]);
		// array of strings
		TestmoduleData::$_title_ui_select_strings = array("option1","option2");
		$this->assertEquals($data->getSelectOptions('title'),['option1','option2']);
		
		// lookup db table
		$data= new TestmoduleFoodHasName($this->web);
		//codecept_debug('C:'.count($data->getSelectOptions('title')));
		$this->createDbRecord('Lookup',['id'=>'1001','type'=>'testtype','code'=>'WHATMYNAME','title'=>'what is my name']);
		$this->createDbRecord('Lookup',['id'=>'1002','type'=>'testtype','code'=>'WHEREMYNAME','title'=>'where is my name']);
		$this->createDbRecord('Lookup',['id'=>'1003','type'=>'nottesttype','code'=>'WHEREELSEMYNAME','title'=>'where else is my name']);
		
		TestmoduleFoodHasName::$_title_ui_select_lookup_code = "testtype"; 
		$options=$data->getSelectOptions('title');
		$this->assertEquals(count($options),2);
		$this->assertEquals($options[0]->code,'WHATMYNAME');
		$this->assertEquals($options[1]->code,'WHEREMYNAME');
		
		// objects lookup
		$data= new TestmoduleFoodHasTitle($this->web);
		//codecept_debug('C:'.count($data->getSelectOptions('title')));
		$this->createDbRecord('TestmoduleData',['id'=>'1001','data'=>'testtype','title'=>'what is my name']);
		$this->createDbRecord('TestmoduleData',['id'=>'1002','data'=>'testtype','title'=>'where is my name']);
		$this->createDbRecord('TestmoduleData',['id'=>'1003','data'=>'nottesttype','title'=>'where else is my name']);		
		TestmoduleFoodHasTitle::$_title_ui_select_objects_class = "TestmoduleData"; //"Contact";
		TestmoduleFoodHasTitle::$_title_ui_select_objects_filter = ['data'=>'testtype']; //array("is_deleted"=>0);
	
		$options=$data->getSelectOptions('title');
		$this->assertEquals(count($options),2);
		$this->assertEquals($options[0]->title,'what is my name');
		$this->assertEquals($options[1]->title,'where is my name');
		
		
		
		//codecept_debug($data->getSelectOptions('title'));
		//TestmoduleData::$_title_ui_select_objects_class = ""; //"Contact";
		//TestmoduleData::$_title_ui_select_objects_filter = []; //array("is_deleted"=>0);
	
	} //$field) {


    
	function test_dateTimeConversions() {
		//function getDate($var, $format = 'd/m/Y') {
		//function getDateTime($var, $format = 'd/m/Y H:i') {
		//function getTime($var, $format = null) {
		//function setTime($var, $date) {
		//function setDate($var, $date) {
		//function setDateTime($var, $date) {
		
		$data= new TestmoduleData($this->web);
		$data->d_last_known=99999999;
		// get
		$this->assertEquals($data->getDate('d_last_known'),$data->time2D(99999999,'d/m/Y'));
		$this->assertEquals($data->getDateTime('d_last_known'),$data->time2Dt(99999999,'d/m/Y H:i'));
		$this->assertEquals($data->getTime('d_last_known'),$data->time2T(99999999,null));
		// set
		$data->setTime('d_last_known',99999999);
		$this->assertEquals($data->d_last_known,$data->t2Time(99999999));
		$data->setDate('d_last_known','12/02/2013');
		$this->assertEquals($data->d_last_known,$data->d2Time('12/02/2013'));
		$data->setDateTime('d_last_known','12/02/2013 12:00pm');
		$this->assertEquals($data->d_last_known,$data->dt2Time('12/02/2013 12:00pm'));
		
	}
   
	function test_insertOrUpdate() {
		$object=Stub::construct('TestmoduleData',['web'=>&$this->web],['update'=>function($a,$b) {echo "::UPDATE::".$a.'::'.$b.'::'; },'insert'=>function($a) {echo "::INSERT::".$a.'::'; }]);
		$out=$this->captureOutput($object,'insertOrUpdate',[]);
		$this->assertEquals($out,'::INSERT::1::');
		$out=$this->captureOutput($object,'insertOrUpdate',["1","0"]);
		$this->assertEquals($out,'::INSERT::0::');
		$object->id=5;
		$out=$this->captureOutput($object,'insertOrUpdate',[]);
		$this->assertEquals($out,'::UPDATE::::1::');
		$out=$this->captureOutput($object,'insertOrUpdate',["1","0"]);
		$this->assertEquals($out,'::UPDATE::1::0::');
	} //$force_null_values = false, $force_validation = true) {

    function test_insertUpdateDelete() {
		// TODO fix bugs when this test is run as part of whole suite
		return ;
		//function insert()
		//function update($force_null_values = false, $force_validation = true) {
		//function delete($force = false) {
		$this->web->_module='testmodule';
			
		// override Auth service with stub
		$this->web->_services['Auth']=Stub::make("AuthService",[
			'login'=>'',
			'loggedIn'=>true,
			'user'=>function() {
				$user=Stub::make('User',[]);
				$user->id=1;
				return $user;
			}
		]);
		$object=new TestmoduleData($this->web);
		$object->setPassword('fredfredfredfred');
		
		// check INSERT
		$object->fill(['title'=>'thetitle','data'=>'thedata','d_last_known'=>'17/03/2009','t_killed'=>'999999999','dt_born'=>'21/03/2009 7:00pm','s_data'=>'thesecret']);
		$output=$this->captureOutput($object,'insert',[]);
		//codecept_debug("::OUT::".$output."::");
		$this->assertEquals($output,':::DBHOOK:::testmodule_core_dbobject_before_insert::::::DBHOOK:::testmodule_core_dbobject_before_insert_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_before_insert::::::DBHOOK:::testmodule_core_dbobject_after_insert::::::DBHOOK:::testmodule_core_dbobject_after_insert::::::DBHOOK:::testmodule_core_dbobject_after_insert_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_indexChange_TestmoduleData:::');
		  
		
		
		// check defaults
		$this->assertNotNull($object->dt_created);
		//codecept_debug([$object->dt_modified, $object->dt_created,$object->dt_modified - $object->dt_created]);
		// 2 second window between setting modified and created
		$this->assertTrue($object->dt_modified - $object->dt_created <2 && $object->dt_modified - $object->dt_created>=0);
		$this->assertEquals($object->creator_id,'1');
		$this->assertEquals($object->modifier_id,'1');
		// check db 
		$this->assertTrue($object->id > 0);
		$record=[];
		$record['title']=$this->guy->grabFromDatabase('testmodule_data','title',['id'=>$object->id]);
		$record['data']=$this->guy->grabFromDatabase('testmodule_data','data',['id'=>$object->id]);
		$record['d_last_known']=$this->guy->grabFromDatabase('testmodule_data','d_last_known',['id'=>$object->id]);
		$record['t_killed']=$this->guy->grabFromDatabase('testmodule_data','t_killed',['id'=>$object->id]);
		$record['dt_born']=$this->guy->grabFromDatabase('testmodule_data','dt_born',['id'=>$object->id]);
		$record['s_data']=$this->guy->grabFromDatabase('testmodule_data','s_data',['id'=>$object->id]);
		$this->assertEquals($record,['title'=>'thetitle','data'=>'thedata','d_last_known'=>'2009-03-17','t_killed'=>'11:46:39','dt_born'=>'2009-03-21 19:00:00','s_data'=>'AneEIUoDSs8kgH/Kms7dfw==']);
		// check inserts into context
		$inserts=$this->web->ctx('db_inserts');
		$this->assertTrue(array_key_exists('TestmoduleData',$inserts) && $inserts['TestmoduleData']===[$object->id]);
		// ensure modified date differs on update
		sleep(1);
		//  now check UPDATE
		$object->fill(['title'=>'newtitle','data'=>'newdata','d_last_known'=>'17/03/2015','t_killed'=>'99999944','dt_born'=>'21/03/2015 7:00pm','s_data'=>'AneEIUoDSs8kgH/Kms7dfw==']);
		$output=$this->captureOutput($object,'update',[]);
	//	codecept_debug("::OUT::".$output."::");
		$this->assertEquals($output,
		':::DBHOOK:::testmodule_core_dbobject_before_update::::::DBHOOK:::testmodule_core_dbobject_before_insert::::::DBHOOK:::testmodule_core_dbobject_after_insert::::::DBHOOK:::testmodule_core_dbobject_after_update::::::DBHOOK:::testmodule_core_dbobject_after_update_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_indexChange_TestmoduleData:::');
	
		// check defaults
		$this->assertNotNull($object->dt_created);
		$this->assertNotEquals($object->dt_created,$object->dt_modified);
		$this->assertTrue($object->id > 0);
		$record=[];
		$record['title']=$this->guy->grabFromDatabase('testmodule_data','title',['id'=>$object->id]);
		$record['data']=$this->guy->grabFromDatabase('testmodule_data','data',['id'=>$object->id]);
		$record['d_last_known']=$this->guy->grabFromDatabase('testmodule_data','d_last_known',['id'=>$object->id]);
		$record['t_killed']=$this->guy->grabFromDatabase('testmodule_data','t_killed',['id'=>$object->id]);
		$record['dt_born']=$this->guy->grabFromDatabase('testmodule_data','dt_born',['id'=>$object->id]);
		$record['s_data']=$this->guy->grabFromDatabase('testmodule_data','s_data',['id'=>$object->id]);
	//	codecept_debug($record);
		$this->assertEquals($record,['title'=>'newtitle','data'=>'newdata','d_last_known'=>'2015-03-17','t_killed'=>'20:45:44','dt_born'=>'2015-03-21 19:00:00','s_data'=>'D+p4Y9iRvvQM+HC20OiT/vD0rLRVY2MRoqp5UYjerfs=']);
		// check inserts into context
		$updates=$this->web->ctx('db_updates');
		$this->assertTrue(array_key_exists('TestmoduleData',$updates) && $updates['TestmoduleData']===[$object->id]);
		
		// now check DELETE
		$this->assertTrue($object->id > 0);
		$this->assertNotEquals($this->guy->grabFromDatabase('testmodule_data','is_deleted',['id'=>$object->id]),'1');
		
		$output=$this->captureOutput($object,'delete',[]);
		//codecept_debug("::OUT::".$output."::");
		// note that delete calls both delete and update hooks when is_deleted flag is used
		$this->assertEquals($output,
	':::DBHOOK:::testmodule_core_dbobject_before_delete::::::DBHOOK:::testmodule_core_dbobject_before_delete_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_before_update::::::DBHOOK:::testmodule_core_dbobject_before_insert::::::DBHOOK:::testmodule_core_dbobject_after_insert::::::DBHOOK:::testmodule_core_dbobject_after_update::::::DBHOOK:::testmodule_core_dbobject_after_update_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_indexChange_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_before_insert::::::DBHOOK:::testmodule_core_dbobject_after_insert::::::DBHOOK:::testmodule_core_dbobject_after_delete::::::DBHOOK:::testmodule_core_dbobject_after_delete_TestmoduleData::::::DBHOOK:::testmodule_core_dbobject_indexChange_TestmoduleData:::'); 
		$this->assertEquals($this->guy->grabFromDatabase('testmodule_data','is_deleted',['id'=>$object->id]),'1');
		// check inserts into context
		$updates=$this->web->ctx('db_deletes');
		$this->assertTrue(array_key_exists('TestmoduleData',$updates) && $updates['TestmoduleData']===[$object->id]);
		// really delete using force
		$object->delete(true);
		$this->guy->dontSeeInDatabase('testmodule_data',['id'=>$object->id]);
		
		// check delete where no is_deleted field
		$object=new TestmoduleFoodHasName($this->web);
		$object->fill(['name'=>'fred']);
		$object->insert();
		$this->guy->seeInDatabase('patch_testmodule_food_has_name',['id'=>$object->id]);
		$object->delete();
		$this->guy->dontSeeInDatabase('patch_testmodule_food_has_name',['id'=>$object->id]);
		
	} 


/*
    
    
    // stubbing
    function getCreator() {
    function getModifier() {
   
   // ??  - aspects, 
    function __construct(Web &$w) {
    public function __clone(){
    public function __get($name) {
     
     
    
	// NOT TESTED
	// intended to be overridden
	function canList(User $user) {
    function canView(User $user) {
    function canEdit(User $user) {
    function canDelete(User $user) {
    // tested by calls to insert,update,delete
    function test_callHooks($type, $action) {}
	
*/

}
