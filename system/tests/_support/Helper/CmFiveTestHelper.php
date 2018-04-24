<?php
namespace Helper;

// DEPRECATED!
// all actions are now in cmfiveguy.php.
// seeing what is useful from here and moving it to the new file.

/******************************************
 * Shared helper functions for working with cmFive
 * all public methods declared in helper class will be available in $I
 ******************************************/


class CmFiveTestHelper extends \Codeception\Module
{
	/**
	 * debug
	 */
	public function dumpSelector($I,$sel1) {
		// .tablesorter tbody tr
		$usernames=$I->grabMultiple($sel1);
		codecept_debug($usernames);
		codecept_debug(array('DONE'=>'mehere'));
	}

	/**
	 * Logout from CMFIVE
	 */
	public function logout($I) {
		//$I->click('Logout');
		$I->amOnPage('/auth/logout');
	}


	/**
	 * Delete a user matching $username
	 */
	public function deleteUser($I,$username) {
		$actionCompleted=false; // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Users');
		$usernames=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($usernames))  {
			foreach ($usernames as  $k=>$u) {
				if (trim($u)==trim($username)) {
					$index=$k + 1;
					$deleteButton=".tablesorter tbody tr:nth-child(". $index .")";
					// disable confirm
					$I->executeJS('window.confirm = function(){return true;}');
					//codecept_debug('username match on DeleTE - '.$deleteButton);
					$I->click('Remove',$deleteButton);
					$I->executeJS('window.confirm = function(){return true;}');
					$I->click('Delete user');
					//$I->acceptPopup();
					$I->see('User '.$username.' deleted');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}

	/**
	 * Set permissions a user matching $username
	 */
	public function setUserPermissions($I,$username,$permissions) {
		$actionCompleted=false;  // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Users');
		$usernames=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($usernames))  {
			foreach ($usernames as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($username)) {
					$I->click("Permissions",$button);
					$I->uncheckOption('input[type="checkbox"]');
					if (is_array($permissions)) {
						foreach ($permissions as $permission) {
							$I->checkOption('#check_'.$permission);
						}
					}
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}

	/**
	 * Create a user then login as them aa
	 */
	public function createAndLoginUser($I,$user) {
		$admin=['username'=>'admin','password'=>'admin'];
		$I->login($I,$admin['username'],$admin['password']);
		$I->createUser($I,$user['username'],$user['password'],$user['first_name'],$user['last_name'],$user['email']);
		if (array_key_exists('roles',$user)) {
			$I->setUserPermissions($I,$user['username'],$user['roles']);
		}
		$I->logout($I);
		$I->login($I,$user['username'],$user['password']);
	}

	/**
	 * Update a user matching $username
	 */
	public function updateUser($I,$username,$data) {
		$actionCompleted=false;  // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Users');
		$usernames=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($usernames))  {
			foreach ($usernames as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($username)) {
					$I->click("Edit",$button);
					$this->fillForm($I,$data);
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}

	/****************************
	 * Create an employee record associated with the provided user (containing array data)
	 ****************************/
	public function createEmployee($I,$user,$employee=[]) {
		$employee['autocomplete:user_id']=$user['first_name'].' '.$user['last_name'];
		$I->doCreateNewRecord($I,'Staff','New Employee','Save','Employee created','staff_employee', $employee);

	}
	/****************************
	 * USERGROUPS
	 ****************************/
	/**
	 * Create a new user group
	 */
	public function createUserGroup($I,$title) {
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Groups');
		$I->click('New Group');
		$I->fillField('#title',$title);
		$I->click('Save');
		$I->see('New group added');
		$I->seeLink($title);
	}

	/**
	 * Delete a user group matching $title
	 */
	public function deleteUserGroup($I,$title) {
		$actionCompleted=false; // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Groups');
		$titles=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($titles))  {
			foreach ($titles as  $k=>$u) {
				if (trim($u)==trim($title)) {
					$index=$k + 1;
					$button=".tablesorter tbody tr:nth-child(". $index .")";
					// disable confirm
					$I->executeJS('window.confirm = function(){return true;}');
					$I->click('Delete',$button);

					$I->see('Group is deleted');
					$I->dontSeeLink($title);
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}

	/**
	 * Update a user matching $username
	 */
	public function updateUserGroup($I,$oldTitle,$newTitle) {
		$actionCompleted=false;  // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($oldTitle)) {
					$I->click($u);
					$I->fillField('#title',$newTitle);
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}

	/**
	 * Add a user to an existing user group
	 */
	public function addUserToUserGroup($I,$user,$userLabel,$userGroup,$isOwner='') {
		$actionCompleted=false;  // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($userGroup)) {
					$I->click('More Info',$button);
					$I->click('New Member');
					$I->selectOption('#member_id',$userLabel);
					if ($isOwner)  $I->checkOption('#is_owner');
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}
	/**
	 * Add a user to an existing user group
	 */
	public function removeUserFromUserGroup($I,$user,$userLabel,$userGroup) {
		$actionCompleted=false;  // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($userGroup)) {
					$I->click('More Info',$button);
					$users=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
					if (is_array($users))  {
						foreach ($users as  $uk=>$user) {
							$index=$uk + 1;
							$button=".tablesorter tbody tr:nth-child(". $index .")";
							if (trim($user)==trim($userLabel)) {
								// disable confirm
								$I->executeJS('window.confirm = function(){return true;}');
								$I->click('Delete');
								$actionCompleted=true;
							}
						}
					}
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}


	/**
	 * Set permissions a user matching $username
	 */
	public function setUserGroupPermissions($I,$userGroup,$permissions) {
		$actionCompleted=false;  // make sure we do find the user
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($userGroup)) {
					$I->click("More Info",$button);
					$I->click("Edit Permissions");
					$I->uncheckOption('input[type="checkbox"]');
					if (is_array($permissions)) {
						foreach ($permissions as $permission) {
							$I->checkOption('#check_'.$permission);
						}
					}
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}

/*******************************************************
 * generic crud routines based on meta data from test class
 * these generic routines will work in some BUT NOT ALL cases
 *******************************************************/

	/**
	 * Create a new record with default parameters from test object
	 */
	public function createNewRecord($I,$test) {
		$this->doCreateNewRecord($I,$test->navSelector,'.addbutton','Save',$test->recordLabel.' created',$test->databaseTable, $test->validRecord);
		// TODO return id of new record
	}

	/**
	 * Create a new record
	 */
	public function doCreateNewRecord($I,$navSelector,$createButtonSelector,$saveButtonSelector,$saveText,$databaseTable,$record) {
		$I->wantTo('Create a new record');
		// twice to for flyout menus
		$I->click($navSelector);
		$I->click($navSelector);

		$I->click($createButtonSelector);
		$I->fillForm($I,$record);

/*		$r=array();
		if (array_key_exists('input',$record)) {
			foreach($record['input'] as $field=>$value) {
				$I->fillField($field,$value);
				$r[$field]=$value;
			}
		}
		if (array_key_exists('select',$record)) {
			foreach($record['select'] as $field=>$value) {
				$I->selectOption($field,$value);
				$r[$field]=$value;
			}
		}
		if (array_key_exists('checkbox',$record)) {
			foreach($record['checkbox'] as $field=>$value) {
				if ($value=='1') {
					$I->checkOption($field);
				} else {
					$I->uncheckOption($field);
				}
				$r[$field]=$value;
			}
		}
*/
		$I->click($saveButtonSelector);
		$I->see($saveText);
		//$I->seeInDatabase($databaseTable,$r);
	}

	/**
	 * Edit a record with default parameters from test object
	 */
	public function editRecord($I,$test) {
		$this->doEditRecord($I,$test->navSelector,$test->moduleUrl.'edit/','Update',$test->recordLabel.' updated',$test->databaseTable, $test->validDBRecord,$test->updateData);
	}

	/**
	 * Edit a record
	 */
	public function doEditRecord($I,$navSelector,$editButtonUrl,$saveButtonSelector,$saveText,$databaseTable,$record,$updateData) {
		$I->wantTo('Edit and save a record');

		$I->click($navSelector);
		$I->click($navSelector);
		//$record['is_deleted']='0';
		//$id=1;
		$id=$I->grabFromDatabase($databaseTable,'id',$record);
		//codecept_debug(["A",$ad,"B"]);
		//$id=$dbRec['id'];

		$I->click('.editbutton[href^="'.$editButtonUrl.$id.'"]');
		$I->wait(1);
		$I->fillForm($I,$updateData);

		$I->click($saveButtonSelector);
		$I->see($saveText);
		//$I->seeInDatabase($databaseTable, $r);
	}

	/**
	 * Delete a record with default parameters from test
	 */
	public function deleteRecord($I,$test) {
		$this->doDeleteRecord($I,
			$test->navSelector, // nav to page
			$test->moduleUrl.'delete/',  // delete link base
			$test->recordLabel.' deleted',  // success message
			$test->databaseTable,  // table
			$test->validDBRecord);  // dummy record
	}

	/**
	 * Delete a record
	 */
	public function doDeleteRecord($I,$navSelector,$deleteButtonUrl,$deletedText,$databaseTable,$record) {
		//$id=$I->haveInDatabase($databaseTable, $record);
		$id=1;
		$I->wantTo('Delete a record');
		$I->click($navSelector);
		$I->click($navSelector);
		// disable confirm
		$I->executeJS('window.confirm = function(){return true;}');
		$I->click('.deletebutton[href^="'.$deleteButtonUrl.$id.'"]');
		$I->see($deletedText);
		//$I->seeInDatabase($databaseTable, array('id' => $id,'is_deleted'=>'1'));
		return $id;
	}

	/**
	 * Run search tests
	 */
/*	public function searchRecords($I,$test) {
		$this->doSearchRecords($I,$test->navSelector,$test->searches);
	}
	public function doSearchRecords($I,$navSelector,$searches) {
		$I->wantTo('Search '.$navSelector);
		$I->click($navSelector);
		$I->click($navSelector);
		$I->runSearches($I,$searches);
	}
	*//**
	 * Run a search with criteria and check number of results for each element of searches array
	 */
/*	public function runSearches($I,$searches) {
		foreach ($searches as $k=> $searchCriteria) {
			if (array_key_exists('input',$searchCriteria)) {
				foreach ($searchCriteria['input'] as $field => $value) {
					$I->fillField("#".$field,$value);
				}
			}
			if (array_key_exists('select',$searchCriteria)) {
				foreach ($searchCriteria['select'] as $field => $value) {
					$I->selectOption("#".$field,$value);
				}
			}
			if (array_key_exists('checkbox',$searchCriteria)) {
				foreach ($searchCriteria['checkbox'] as $field => $value) {
					if ($value=='1') {
						$I->checkOption($field);
					} else {
						$I->uncheckOption($field);
					}
				}
			}
			$I->click('Filter');
			$I->seeNumberOfElements('table.tablesorter tbody tr',$searchCriteria['result']);
		}
	}*/

}
