<?php 
namespace Helper;

/******************************************
 * Shared helper functions for working with cmFive
 * all public methods declared in helper class will be available in $I
 ******************************************/
class CmFiveTestHelper extends \Codeception\Module
{

	/****************************
	 * MISC FUNCTIONS
	 ****************************/
	public function navigateTo($I,$menu,$submenu) {
		$I->moveMouseOver(['css'=>'#topnav_'.$menu]);
		$I->click($submenu);
	}
	
	public function findTableRowMatching($I,$columnNumber,$matchValue) {
		$rows=$I->grabMultiple('.tablesorter tbody tr td:nth-child('.$columnNumber.')');
		if (is_array($rows))  {
			foreach ($rows as  $k=>$v) {
				$index=$k + 1;
				if (trim($v)==trim($matchValue)) {
					return $index;
				}
			}
		}
		return false;
	}

	/**
	 * Fill a form from an array of data
	 * Assume that id attribute of form inputs match $data keys
	 * where key starts with check: or select: a modified key is used
	 * with the setOption or checkOption commands to set values
	 * otherwise the input is treated as text using fillField 
	 */	 
	public function fillForm($I,$data) {
		if (is_array($data)) {
			foreach ($data as $fieldName=>$fieldValue) {
				$fieldNameParts=explode(':',$fieldName);
				if ($fieldNameParts[0]=='check' && count($fieldNameParts)>1) {
					if ($fieldValue) {
						 $I->checkOption('#'.$fieldNameParts[1]);
					} else {
						$I->uncheckOption('#'.$fieldNameParts[1]);
					}
				} else if (($fieldNameParts[0]=='select' || $fieldNameParts[0]=='radio') && count($fieldNameParts)>1) {
					$I->selectOption('#'.$fieldNameParts[1] ,$fieldValue);
				} else if ($fieldNameParts[0]=='date' && count($fieldNameParts)>1) {
					$I->fillDatePicker($I,$fieldNameParts[1],$fieldValue);
				} else if ($fieldNameParts[0]=='datetime' && count($fieldNameParts)>1) {
					$I->fillDateTimePicker($I,$fieldNameParts[1],$fieldValue);
				} else if ($fieldNameParts[0]=='time' && count($fieldNameParts)>1) {
					$I->fillTimePicker($I,$fieldNameParts[1],$fieldValue);
				} else if ($fieldNameParts[0]=='rte' && count($fieldNameParts)>1) {
					$I->fillCkEditorById($I,$fieldNameParts[1],$fieldValue);
				} else if ($fieldNameParts[0]=='autocomplete' && count($fieldNameParts)>1) {
					$I->fillAutocomplete($I,$fieldNameParts[1],$fieldValue);
				} else {
					$I->fillField('#'.$fieldName ,$fieldValue);
				}
			}
		}
	}
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
	 * Login to CMFIVE
	 */
    public function login($I,$username,$password) {
		$I->wantTo('Log in');
		$I->amOnPage('/auth/login');
		// skip form filling if already logged in
		if (strpos('/auth/login',$I->grabFromCurrentUrl())!==false) {
			$I->fillField('login',$username);
			$I->fillField('password',$password);
			$I->click('Login');
			//$redirect=$I->grabFromDatabase('user','redirect_url',array('login'=>$username));
			//if (strlen(trim($redirect)>0)) $I->canSeeInCurrentUrl($redirect);
		}
	}
	
	/**
	 * Logout from CMFIVE
	 */	
	public function logout($I) {
		//$I->click('Logout');
		$I->amOnPage('/auth/logout');
	}
	
	
		
	/****************************
	 * USERS
	 ****************************/
	/**
	 * Create a new user
	 */
	public function createUser($I,$username,$password,$firstName,$lastName,$email) {
		$I->moveMouseOver(['css' => '#topnav_admin']);
		$I->click('List Users');
		$I->click('Add New User');
		$I->fillForm($I,[
		'login'=>$username,
		'password'=>$password,
		'password2'=>$password,
		'check:is_active'=>true,
		'firstname'=>$firstName,
		'lastname'=>$lastName,
		'email'=>$email]);
		$I->click('Save');
		$I->see('User '.$username.' added');
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
	

	
		
	/****************************
	 * TASK GROUPS
	 ****************************/
		
	/****************************
	 * Create a Task Group
	 ****************************/
	public function createTaskGroup($I,$taskGroup,$data) {
		$I->wait(1);
		$I->moveMouseOver(['css' => '#topnav_task']);
		$I->click('Task Groups');
		$I->click('New Task Group');
		$fields=[];
		$fields['select:task_group_type']=$data['task_group_type'];
		$fields['title']=$taskGroup;
		if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
		if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
		if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
		if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
		if (!empty($data['description'])) $fields['rte:description']=$data['description'];
		if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
		$this->fillForm($I,$fields);
		$I->click('Save');
		$I->see('Task Group '.$taskGroup.' added');
	}

	public function updateTaskGroup($I,$taskGroup,$data) {
		$I->wait(1);
		$I->moveMouseOver(['css' => '#topnav_task']);
		$I->wait(1);
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			$I->click('Edit Task Group');
			$fields=[];
			if (!empty($data['title'])) $fields['title']=$data['title'];
			if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
			if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
			if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
			if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
			if (!empty($data['description'])) $fields['rte:description']=$data['description'];
			if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
			$this->fillForm($I,$fields);
			$I->click('Update');
			$actionCompleted=true;	
		}
		$I->assertTrue($actionCompleted);
	}
	
	public function deleteTaskGroup($I,$taskGroup) {
		//$I->wait(1);
		//$I->moveMouseOver(['css' => '#topnav_task']);
		//$I->wait(1);
		//$I->click("Task Groups");
		$I->amOnPage('/task-group/viewtaskgrouptypes');
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			//$I->click('Delete Task Group');
			$I->executeJS('$("#members button:nth-child(4)").click();');
			$I->executeJS('$("#cmfive-modal .savebutton").click();');
			//$I->click('Delete');
			$actionCompleted=true;	
		}
		$I->assertTrue($actionCompleted);
	}
	
	public function addMemberToTaskGroup($I,$taskGroup,$userLabel,$role) {
		$I->wait(1);
		$I->moveMouseOver(['css' => '#topnav_task']);
		$I->wait(1);
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			$I->click('Add New Members');
			$I->selectOption('#role',$role);
			$I->selectOption('#member',$userLabel);
			// ?? click doesn't seem to work in this modal form ??
			//$I->click('Submit');
			//$I->click('button.savebutton');
			$I->executeJS('$("#cmfive-modal form").submit();');
			$actionCompleted=true;	
		}
		$I->assertTrue($actionCompleted);
	}
	
	public function updateMemberInTaskGroup($I,$taskGroup,$userLabel,$role) {
		$I->wait(1);
		$I->moveMouseOver(['css' => '#topnav_task']);
		$I->wait(1);
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			if ($userRowNumber=$this->findTableRowMatching($I,1,$userLabel) > 0) {
				$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
				$I->click('Edit',$context);
				$I->selectOption('#role',$role);
				$I->click('Update');
				$actionCompleted=true;	
			}
		}
		$I->assertTrue($actionCompleted);
	}
	public function removeMemberFromTaskGroup($I,$taskGroup,$userLabel) {
		$I->wait(1);
		$I->moveMouseOver(['css' => '#topnav_task']);
		$I->wait(1);
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			if ($userRowNumber=$this->findTableRowMatching($I,1,$userLabel) > 0) {
				$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
				$I->click('Delete',$context);
				$I->executeJS('$("#cmfive-modal .savebutton").click();');
				//$I->click('Delete');
				$actionCompleted=true;	
			}
		}
		$I->assertTrue($actionCompleted);
	}
	public function updateTaskGroupNotifications($I,$taskGroup,$notifications) {}
	
	
	
	/****************************
	 * TASKS
	 ****************************/
	
	/****************************
	 * Create a task
	 ****************************/
	public function createTask($I,$taskGroup,$task,$data) {
		//$I->click("Task","section.top-bar-section ul.left");
		$I->wait(1);
		$I->moveMouseOver(['css' => '#topnav_task']);
		$I->wait(1);
		$I->click('New Task');
		$this->fillForm($I,[
			'autocomplete:task_group_id'=>$taskGroup,
			'select:task_type'=>!empty($data['task_type']) ? $data['task_type'] : '',
			'title'=>$task,
			'select:status'=>!empty($data['status']) ? $data['status'] : '',
			'select:priority'=>!empty($data['priority']) ? $data['priority'] : '',
			'date:dt_due'=>$data['dt_due'],
			'select:assignee_id'=>!empty($data['assignee_id']) ? $data['assignee_id'] : '',
			'estimate_hours'=>!empty($data['estimate_hours']) ?  $data['estimate_hours'] : '',
			'effort'=>!empty($data['effort']) ? $data['effort'] : '',
			'rte:description'=>!empty($data['description']) ?  $data['description'] : '',
		]);
		$I->click('Save');
	}
	


/*******************************************************
 * LIB 
 *******************************************************/
 
public function fillDatePicker($I,$field,$date) {
	$day=date('j',$date);
	$month=date('M',$date);
	$year=date('Y',$date);
	$hour=date('H',$date);
	$dateFormatted=date('d/m/Y H:i',$date);
	$finalDateFormatted=date('d/m/Y',$date);
	$I->executeJS('return $("#'.$field.'").datepicker("setDate","'.$dateFormatted.'");');
	$I->seeInField('#'.$field,$finalDateFormatted);
} 

public function fillDateTimePicker($I,$field,$date) {
	$day=date('j',$date);
	$month=date('M',$date);
	$year=date('Y',$date);
	$hour=date('H',$date);
	$dateFormatted=date('d/m/Y H:i',$date);
	$finalDateTimeFormatted=date('d/m/Y h:i a',$date);
	$I->executeJS('return $("#'.$field.'").datepicker("setDate","'.$dateFormatted.'");');
	$I->seeInField('#'.$field,$finalDateTimeFormatted);
} 

public function fillTimePicker($I,$field,$date) {
	$day=date('j',$date);
	$month=date('M',$date);
	$year=date('Y',$date);
	$hour=date('H',$date);
	$dateFormatted=date('d/m/Y H:i',$date);
	$finalTimeFormatted=date('h:i a',$date);
	$I->executeJS('return $("#'.$field.'").datepicker("setDate","'.$dateFormatted.'");');
	$I->seeInField('#'.$field,$finalTimeFormatted);
} 

public function fillAutocomplete($I,$field,$value) {
	$I->fillField("#".$field,$value);
	$I->waitForElement(".ui-autocomplete a",2);
	// down
	$I->pressKey("#acp_".$field,"\xEE\x80\x95");
	// select
	$I->executeJS('$(".ui-autocomplete a").show(); $(".ui-autocomplete a").click();');
}
 
 
/*******************************************************
 * http://stackoverflow.com/questions/29168107/how-to-fill-a-rich-text-editor-field-for-a-codeception-acceptance-test
 *******************************************************/
 public function fillCkEditorById($I,$element_id, $content) {
        $I->fillRteEditor($I,
            \Facebook\WebDriver\WebDriverBy::cssSelector(
                '#cke_' . $element_id . ' .cke_wysiwyg_frame'
            ),
            $content
        );
    }

    public function fillCkEditorByName($I,$element_name, $content) {
        $I->fillRteEditor($I,
            \Facebook\WebDriver\WebDriverBy::cssSelector(
                'textarea[name="' . $element_name . '"] + .cke .cke_wysiwyg_frame'
            ),
            $content
        );
    }
    public  function fillRteEditor($I,$selector, $content) {
        $I->executeInSelenium(
            function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver)
            use ($selector, $content) {
                $webDriver->switchTo()->frame(
                    $webDriver->findElement($selector)
                );

                $webDriver->executeScript(
                    'arguments[0].innerHTML = "' . addslashes($content) . '"',
                    [$webDriver->findElement(\Facebook\WebDriver\WebDriverBy::tagName('body'))]
                );

                $webDriver->switchTo()->defaultContent();
            });
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





















