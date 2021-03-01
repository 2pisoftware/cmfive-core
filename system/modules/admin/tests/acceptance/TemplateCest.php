 <?php

class TemplateCest
{
    public function testTemplate($I)
    {
        $I->wantTo('Create a template');
        $I->login($I, 'admin', 'admin');

        $I->createTemplate(
            $I,
            'Test Template',
            'Admin',
            'Templates',
            "	  <table width='100%' align='center' class='form-table' cellpadding='1'>	"
                . "	           	"
                . "	            <tr>	"
                . "	             <td colspan='2' style='border:none;'><img width='400' src='' style='width: 400px;' />	"
                . "	            </td>	"
                . "	             <td colspan='2' style='border:none; text-align:right;'>	"
                . "	            Test Company<br/>	"
                . "	            123 Test St, Test Town, NSW 1234<br/>	"
                . "	            test@example.com<br/>	"
                . "	            ACN 123456789<br/>	"
                . "	            ABN 12345678901	"
                . "	             </td>	"
                . "	            </tr>	"
                . "	             	"
                . "	            </table>	"
        );
        $I->see('Template Saved');
        $I->demoTemplate($I, 'Test Template');
        $I->wait(2);
        $I->see("Test Company");
    }
}
