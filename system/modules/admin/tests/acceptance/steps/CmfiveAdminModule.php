<?php
namespace Step\Acceptance;

class CmfiveAdminModule extends \CmfiveUI
{

 
  public function createUser($username,$password,$firstName,$lastName,$email, array $permissions = []) {
    $this->clickCmfiveNavbar('Admin', 'List Users');
        $this->click('Add New User');
        $this->waitForElement('#login');
        $this->fillForm([
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
      $this->click('#check_'.$permission);
    }
    $this->click('Save');
        $this->see('User '.$username.' added');
    }
 
    
		public function editUser($user,$data) {
			$this->clickCmfiveNavbar('Admin', 'List Users');
			$rowIndex = $this->findTableRowMatching(1,$user);
			$this->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')'); 
			$this->see('Administration - Edit User - ' . $user);
			$this->fillForm($data);
			$this->click('.savebutton');
			$this->wait(1);
			$this->see('User ' . $user . ' updated.');
		}

		public function editLookup($lookup,$data) {
			$this->wait(1);
			$this->clickCmfiveNavbar('Admin', 'Lookup');
			$this->wait(1);
			$rowIndex = $this->findTableRowMatching(3,$lookup);
			$this->click('Edit', 'tbody tr:nth-child('.$rowIndex . ')'); 
			$this->wait(1);
			$this->fillForm($data);
			$this->wait(1);
			$this->click("//div[@id='cmfive-modal']//button[contains(text(),'Update')]");
			$this->wait(1);
			$this->see('Lookup Item edited');
		}

		public function createLookup($type, $code, $title) {
			$this->clickCmfiveNavbar('Admin', 'Lookup');
			$this->click('New Item');
			$this->click("//div[@id='tab-2']//label[@class='small-12 columns']//select[@id='type']");
			$this->click("//label[@class='small-12 columns']//option[@value='title'][contains(text(),'title')]");
			//$this->selectOption('#type',$type);
			$this->fillField('#code' ,$code);
			$this->fillField('#title' ,$title);
			$this->click(".savebutton");
			$this->wait(1);
			$this->see('Lookup Item added');
    }
    
}