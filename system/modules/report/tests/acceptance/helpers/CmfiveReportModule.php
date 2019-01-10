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
		$I->wait(1);
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

}