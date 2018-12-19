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
}