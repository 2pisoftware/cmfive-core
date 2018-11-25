<?php
namespace Helper;

class CmfiveAdminModule extends \Codeception\Module
{

 
  public function createUser($I,$username,$password,$firstName,$lastName,$email, array $permissions = []) {
    $I->clickCmfiveNavbar($I,'Admin', 'List Users');
        $I->click('Add New User');
        $I->waitForElement('#login');
        $I->fillForm([
        'login'=>$username,
        'password'=>$password,
        'password2'=>$password,
        'check:is_active'=>true,
        'firstname'=>$firstName,
        'lastname'=>$lastName,
        'email'=>$email]);
    if (empty($permissions)) {
      $permissions = ['user'];
    }
    foreach ($permissions as $permission) {
      $I->click('#check_'.$permission);
    }
    $I->click('Save');
        $I->see('User '.$username.' added');
    }
 
    
		public function editUser($I,$user,$data) {
			$I->clickCmfiveNavbar($I,'Admin', 'List Users');
			$rowIndex = $I->findTableRowMatching(1,$user);
			$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')'); 
			$I->see('Administration - Edit User - ' . $user);
			$I->fillForm($data);
			$I->click('.savebutton');
			$I->wait(1);
			$I->see('User ' . $user . ' updated.');
		}

		public function editLookup($I,$lookup,$data) {
			$I->wait(1);
			$I->clickCmfiveNavbar($I,'Admin', 'Lookup');
			$I->wait(1);
			$rowIndex = $I->findTableRowMatching(3,$lookup);
			$I->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')'); 
			$I->wait(1);
			$I->fillForm($data);
			$I->wait(1);
			$I->click("//div[@id='cmfive-modal']//button[contains(text(),'Update')]");
			$I->wait(1);
			$I->see('Lookup Item edited');
		}

		public function createLookup($I,$type, $code, $title) {
			$I->clickCmfiveNavbar($I,'Admin', 'Lookup');
			$I->click('New Item');
			$I->click("//div[@id='tab-2']//label[@class='small-12 columns']//select[@id='type']");
			$I->click("//label[@class='small-12 columns']//option[@value='title'][contains(text(),'title')]");
			//$I->selectOption('#type',$type);
			$I->fillField('#code' ,$code);
			$I->fillField('#title' ,$title);
			$I->click(".savebutton");
			$I->wait(1);
			$I->see('Lookup Item added');
    }
    
}