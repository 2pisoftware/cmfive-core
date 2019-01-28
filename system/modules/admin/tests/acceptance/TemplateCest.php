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

		$I->createTemplate ($I,'Test Template','Admin'
			,'Templates',
			"	  <table width='100%' align='center' class='form-table' cellpadding='1'>	"
			."	           	"
			."	            <tr>	"
			."	             <td colspan='2' style='border:none;'><img width='400' src='http://2pisoftware.com/wp-content/uploads/2014/02/logo-transparent-742x1901.png' style='width: 400px;' />	"
			."	            </td>	"
			."	             <td colspan='2' style='border:none; text-align:right;'>	"
			."	            2pi Software<br/>	"
			."	            1 Millowine Ln, Bega, NSW 2550<br/>	"
			."	            info@2pisoftware.com<br/>	"
			."	            ACN 159945454<br/>	"
			."	            ABN 42159945454	"
			."	             </td>	"
			."	            </tr>	"
			."	             	"
			."	            </table>	"
			); 
		$I->see('Template Saved');
		$I->demoTemplate($I,'Test Template');
		$I->wait(2);
		$I->see("2pi Software");
		// delete also ?
	}
}