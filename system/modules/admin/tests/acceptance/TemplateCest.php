<?php

class TemplateCest
{
	public function _before()
    {
    }

    public function _after()
    {
    }

	public function testTemplate ($I) {
		$I->wantTo('Create a template');
		$I->login($I,'admin','admin');
		$I->clickCmfiveNavbar($I, 'Admin', 'Templates');
		$I->click("Add Template");
		$I->fillField('#title', 'Test Template');
		$I->click('#is_active');
		$I->fillField('#module', 'Admin');
		$I->fillField('#category', 'Templates');
		$I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue('test')");
		$I->click('Save');
		$I->see('Template Saved');
		$I->click('Back to Templates List');
		$I->see('Test Template');
		// delete also & log out!
	}
}