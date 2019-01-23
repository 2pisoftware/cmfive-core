<?php
namespace Helper;

class CmfiveReportModule extends \Codeception\Module
{
	public function createReport($I, $name, $module) {
		$I->clickCmfiveNavbar($I, 'Report', 'Create a Report');
		$I->fillField('#title', $name);
		$I->selectOption('#module', $module);
		$I->click('Save');
	}

	public function defineReportSQL($I, $name, $SQL) {
		$I->wait(1);
		$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
		$I->wait(2);
		$rowIndex = $I->findTableRowMatching(1,$name);
		$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
		$I->wait(1);
		$I->click('SQL');
		$I->wait(1);
		//echo "$('.CodeMirror')[0].CodeMirror.setValue('".$SQL."')";
		$I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue(\"".$SQL."\")");
		//$I->click('SQL');
		//$I->wait(1);
		$I->click("//div[@id='code']/form/div/button"); 
	}

	public function requestReport($I, $name) {
		$I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
		$I->wait(1);
		$rowIndex = $I->findTableRowMatching(1,$name);
		$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')');
		$I->wait(1);
		$I->click("//div[@id='report']/button");
		
	}

	public function createFeed($I, $reportName, $feed) {
		$I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
		$I->click('Create Feed');
		$I->fillField('#title', $feed);
		$I->selectOption('#rid', $reportName);
		$I->click('Save');
	}

	public function requestReportConnection($I) {
		$I->clickCmfiveNavbar($I, 'Report', 'Connections');
		$I->click('Add a Connection');
		$I->wait(2);
		$I->selectOption('#db_driver',"mysql");
		$dbs=$I->getDB_Settings();
		$I->fillField("#db_host",$dbs['DB_Hostname']);
		$I->fillField("s_db_user",$dbs['DB_Username']);
		$I->fillField("s_db_password",$dbs['DB_Password']);
		$I->fillField("#db_database",$dbs['DB_Database']);
		$I->wait(2);
		$I->click('Save');
		$I->wait(2);
		$I->click('Test Connection');
		$I->wait(2);
		$I->see("Connected to DB");
	}

	public function getFeedURL($I,  $feed) {
		$I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
		$I->wait(1);
		$rowIndex = $I->findTableRowMatching(1,$feed);
		$I->click('View', 'tbody tr:nth-child('.$rowIndex . ')');
		return $I->grabAttributeFrom("//div[contains(text(),'Feed URL')]/following-sibling::div"
					,"innerHTML");
		
	}
}