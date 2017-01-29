<?php
class adminCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // auth details
	var $username='admin';
	var $password='admin';
	
	public function testUserAdmin($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->setUserPermissions($I,'testuser',array('comment','user','favorites_user'));
		$I->updateUser($I,'testuser',array('firstname'=>'Fred' ,'lastname'=>'Flintstone','check:is_admin'=>true));
		$I->deleteUser($I,'testuser');
	}
	
	public function testGroupAdmin($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUserGroup($I,'new test group');
		$I->seeLink('new test group');
		$I->updateUserGroup($I,'new test group','testgroup');
		$I->seeLink('testgroup');
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->addUserToUserGroup($I,'testuser','testy tererer','testgroup');
		$I->removeUserFromUserGroup($I,'testuser','testy tererer','testgroup');
		$I->setUserGroupPermissions($I,'testgroup',array('comment','user','favorites_user'));
		$I->deleteUserGroup($I,'testgroup');
		$I->deleteUser($I,'testuser');
	}
	
	

}
