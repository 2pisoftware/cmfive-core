<?php
namespace Helper;

class CmfiveFormModule extends \Codeception\Module
{
	public function createForm($I, $name, $description) {
		$I->clickCmfiveNavbar($I, 'Form', 'Forms');
		$I->wait(1);
		$I->click("//button[contains(text(),'Add')]");
		$I->fillField('#title', $name);
		$I->fillField('#description', $description);
		$I->click('Save');
	}

	public function editForm($I, $name, $rename , $description) { 
		$I->clickCmfiveNavbar($I, 'Form', 'Forms');
		$I->wait(1);
		$rowIndex = $I->findTableRowMatching(1,$name); 
		$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
		$I->wait(1); 
		$I->fillField('#title', $rename);
		$I->fillField('#description', $description);
		$I->click('Save');
	}

	
	public function deleteForm($I, $name) { 
		$I->clickCmfiveNavbar($I, 'Form', 'Forms'); 
		$rowIndex = $I->findTableRowMatching(1,$name); 
		$I->click('Delete', 'tbody tr:nth-child('.$rowIndex . ')');
		$I->wait(1); 
		$I->acceptPopup();
		$I->wait(1); 
		$I->clickCmfiveNavbar($I, 'Form', 'Forms');
		$I->dontSee($name);
	}

	public function createApplication($I, $name, $description) {
		$I->clickCmfiveNavbar($I, 'Form', 'Applications');
		$I->wait(1);
		$I->click("//button[contains(text(),'Create App')]");
		$I->fillField("//label[contains(text(),'Title')]//input", $name);
		$I->fillField("//label[contains(text(),'Description')]//textarea", $description);
		$I->click("//div[@class='switch']"); //selectOption('#is_active' ,1);
		$I->wait(1);
		$I->click('Save');
	}

	public function attachApplicationForm($I, $name, $form) { 
		$I->clickCmfiveNavbar($I, 'Form', 'Applications');
		$I->wait(1);
		$rowIndex = $I->findTableRowMatching(1,$name);
		$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
		$I->wait(2);
		$I->click("Attach form");
		$I->wait(1); 
		$I->selectOption("//label[contains(text(),'Form')]/select",$form);   
		 $I->click("//div[@id='form_application_form_modal']//button[1]"); 
	}
 
	public function addFormField($I, $form, $name, $key , $type) { 
		$I->clickCmfiveNavbar($I, 'Form', 'Forms');
		$I->wait(1);
		$I->click($form);
		$I->wait(2);
		$I->click("Add a field");
		$I->wait(1);
		$I->fillField("#name",$name);
		$I->fillField("#technical_name",$key); 
		$I->selectOption("//label[contains(text(),'Type')]/select",$type);  
		$I->wait(1); 
		$I->click("//div[@id='cmfive-modal']//button[1]"); 
	}
	// public function defineReportSQL($I, $name, $SQL) {
	// 	$I->wait(1);
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
	// 	$I->wait(2);
	// 	$rowIndex = $I->findTableRowMatching(1,$name);
	// 	$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
	// 	$I->wait(1);
	// 	$I->click('SQL');
	// 	$I->wait(1);
	// 	//echo "$('.CodeMirror')[0].CodeMirror.setValue('".$SQL."')";
	// 	$I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue(\"".$SQL."\")");
	// 	//$I->click('SQL');
	// 	//$I->wait(1);
	// 	$I->click("//div[@id='code']/form/div/button"); 
	// }

	// public function attachTemplate($I, $name, $template) { 
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
	// 	$I->wait(2);
	// 	$rowIndex = $I->findTableRowMatching(1,$name);
	// 	$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
	// 	$I->wait(2);
	// 	$I->click("//a[@href='#templates' and text()='Templates']");
	// 	$I->wait(1);
	// 	$I->click('Add Template');
	// 	$I->wait(1);
	// 	$I->selectOption("#template_id",$template);  
	// 	$I->selectOption("#type",'HTML'); 
	// 	 $I->click('Save'); 
	// }

	// public function runReportTemplate($I, $name, $template) { 
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
	// 	$I->wait(2);
	// 	$rowIndex = $I->findTableRowMatching(1,$name);
	// 	$I->click($name, 'tbody tr:nth-child('.$rowIndex . ')');
	// 	$I->wait(2); 
	// 	$I->selectOption("#template",$template);  
	// 	 $I->click('Display Report'); 
	// }

	// public function requestReport($I, $name) {
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
	// 	$I->wait(1);
	// 	$rowIndex = $I->findTableRowMatching(1,$name);
	// 	$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
	// 	$I->wait(1);
	// 	$I->click("//div[@id='report']/button");
		
	// }

	// public function createFeed($I, $reportName, $feed) {
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
	// 	$I->click('Create Feed');
	// 	$I->fillField('#title', $feed);
	// 	$I->selectOption('#rid', $reportName);
	// 	$I->click('Save');
	// }

	// public function requestReportConnection($I) {
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Connections');
	// 	$I->click('Add a Connection');
	// 	$I->wait(2);
	// 	$I->selectOption('#db_driver',"mysql");
	// 	$dbs=$I->getDB_Settings();
	// 	$I->fillField("#db_host",$dbs['DB_Hostname']);
	// 	$I->fillField("s_db_user",$dbs['DB_Username']);
	// 	$I->fillField("s_db_password",$dbs['DB_Password']);
	// 	$I->fillField("#db_database",$dbs['DB_Database']);
	// 	$I->wait(2);
	// 	$I->click('Save');
	// 	$I->wait(2);
	// 	$I->click('Test Connection');
	// 	$I->wait(2);
	// 	$I->see("Connected to DB");
	// 	$I->click("//a[@class='close-reveal-modal']");
	// }

	// public function linkReportConnection($I,$report) {
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
	// 	$I->wait(2);
	// 	$rowIndex = $I->findTableRowMatching(1,$report);
	// 	$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
	// 	$I->wait(2);
	// 	$dbs=$I->getDB_Settings(); 
	// 	$I->selectOption("#report_connection_id","mysql:"
	// 				.$dbs['DB_Database']."@".$dbs['DB_Hostname']);   
	// 	 $I->click('Save'); 

	// }

	// public function getFeedURL($I,  $feed) {
	// 	$I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
	// 	$I->wait(1);
	// 	$rowIndex = $I->findTableRowMatching(1,$feed);
	// 	$I->click('View', 'tbody tr:nth-child('.$rowIndex . ')');
	// 	return $I->grabAttributeFrom("//div[contains(text(),'Feed URL')]/following-sibling::div"
	// 				,"innerHTML");
		
	// }
}