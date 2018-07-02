<?php
/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class CmfiveGuy extends \Codeception\Actor
{
    use _generated\CmfiveGuyActions;

    public function clickCmfiveNavbar($category,$link) {
  		$this->click($category,"section.top-bar-section ul.left");
  		$this->moveMouseOver(['css' => '#topnav_'.strtolower($category)]);
  		$this->waitForText($link);
      $this->wait(.5);
  		$this->click($link);
  	}

    /**
  	 * Fill a form from an array of data
  	 * Assume that id attribute of form inputs match $data keys
  	 * where key starts with check: or select: a modified key is used
  	 * with the setOption or checkOption commands to set values
  	 * otherwise the input is treated as text using fillField
  	 */
  	public function fillForm($data) {
  		if (is_array($data)) {
  			foreach ($data as $fieldName=>$fieldValue) {
  				$fieldNameParts=explode(':',$fieldName);
  				if ($fieldNameParts[0]=='check' && count($fieldNameParts)>1) {
  					if ($fieldValue) {
  						 $this->checkOption('#'.$fieldNameParts[1]);
  					} else {
  						$this->uncheckOption('#'.$fieldNameParts[1]);
  					}
  				} else if (($fieldNameParts[0]=='select' || $fieldNameParts[0]=='radio') && count($fieldNameParts)>1) {
  					$this->selectOption('#'.$fieldNameParts[1] ,$fieldValue);
  				} else if ($fieldNameParts[0]=='date' && count($fieldNameParts)>1) {
  					$this->fillDatePicker($fieldNameParts[1],$fieldValue);
  				} else if ($fieldNameParts[0]=='datetime' && count($fieldNameParts)>1) {
  					$this->fillDateTimePicker($fieldNameParts[1],$fieldValue);
  				} else if ($fieldNameParts[0]=='time' && count($fieldNameParts)>1) {
  					$this->fillTimePicker($fieldNameParts[1],$fieldValue);
  				} else if ($fieldNameParts[0]=='rte' && count($fieldNameParts)>1) {
  					$this->fillCkEditorById($fieldNameParts[1],$fieldValue);
  				} else if ($fieldNameParts[0]=='autocomplete' && count($fieldNameParts)>1) {
  					$this->fillAutocomplete($fieldNameParts[1],$fieldValue);
  				} else {
  					$this->fillField('#'.$fieldName ,$fieldValue);
  				}
  			}
  		}
  	}

    public function findTableRowMatching($columnNumber,$matchValue) {
  		$rows=$this->grabMultiple('.tablesorter tbody tr td:nth-child('.$columnNumber.')');
  		if (is_array($rows))  {
  			foreach ($rows as  $k=>$v) {
  				$thisndex=$k + 1;
  				if (trim($v)==trim($matchValue)) {
  					return $thisndex;
  				}
  			}
  		}
  		return false;
  	}

    /*******************************************************
     * http://stackoverflow.com/questions/29168107/how-to-fill-a-rich-text-editor-field-for-a-codeception-acceptance-test
     *******************************************************/
     public function fillCkEditorById($element_id, $content) {
            $this->fillRteEditor(\Facebook\WebDriver\WebDriverBy::cssSelector(
                    '#cke_' . $element_id . ' .cke_wysiwyg_frame'
                ),
                $content
            );
        }

        public function fillCkEditorByName($element_name, $content) {
            $this->fillRteEditor(\Facebook\WebDriver\WebDriverBy::cssSelector(
                    'textarea[name="' . $element_name . '"] + .cke .cke_wysiwyg_frame'
                ),
                $content
            );
        }
        public  function fillRteEditor($selector, $content) {
            $this->executeInSelenium(
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

        public function fillDatePicker($field,$date) {
        	$day=date('j',$date);
        	$month=date('M',$date);
        	$year=date('Y',$date);
        	$hour=date('H',$date);
        	$dateFormatted=date('d/m/Y H:i',$date);
        	$finalDateFormatted=date('d/m/Y',$date);
        	$this->executeJS('return $("#'.$field.'").datepicker("setDate","'.$dateFormatted.'");');
        	$this->seeInField('#'.$field,$finalDateFormatted);
        }

        public function fillDateTimePicker($field,$date) {
        	$day=date('j',$date);
        	$month=date('M',$date);
        	$year=date('Y',$date);
        	$hour=date('H',$date);
        	$dateFormatted=date('d/m/Y H:i',$date);
        	$finalDateTimeFormatted=date('d/m/Y h:i a',$date);
        	$this->executeJS('return $("#'.$field.'").datepicker("setDate","'.$dateFormatted.'");');
        	$this->seeInField('#'.$field,$finalDateTimeFormatted);
        }

        public function fillTimePicker($field,$date) {
        	$day=date('j',$date);
        	$month=date('M',$date);
        	$year=date('Y',$date);
        	$hour=date('H',$date);
        	$dateFormatted=date('d/m/Y H:i',$date);
        	$finalTimeFormatted=date('h:i a',$date);
        	$this->executeJS('return $("#'.$field.'").datepicker("setDate","'.$dateFormatted.'");');
        	$this->seeInField('#'.$field,$finalTimeFormatted);
        }

        public function fillAutocomplete($field,$value) {
          echo "<pre>"; var_dump('wooah autocomplete'); die;
        	$this->fillField("#".$field,$value);
        	$this->waitForElement(".ui-autocomplete a",2);
        	// down
        	$this->pressKey("#acp_".$field,"\xEE\x80\x95");
        	// select
        	$this->executeJS('$(".ui-autocomplete a").show(); $(".ui-autocomplete a").click();');
        }


    public function login($username,$password) {
  		$this->amOnPage('/auth/login');
  		// skip form filling if already logged in
  		if (strpos('/auth/login',$this->grabFromCurrentUrl())!==false) {
        $this->waitForElement('#login');
  			$this->fillField('login',$username);
  			$this->fillField('password',$password);
  			$this->click('Login');
      }
    }
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

    public function createTaskGroup($taskGroup,$data) {
  		$this->clickCmfiveNavbar('Task', 'Task Groups');
      $this->click('New Task Group');
  		$fields=[];
  		$fields['select:task_group_type']=$data['task_group_type'];
  		$fields['title']=$taskGroup;
  		if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
  		if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
  		if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
  		if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
  		if (!empty($data['description'])) $fields['rte:description']=$data['description'];
  		if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
  		$this->fillForm($fields);
  		$this->click('Save');
  		$this->see('Task Group '.$taskGroup.' added');
  	}

    public function updateTaskGroup($taskGroup,$data) {
      $this->clickCmfiveNavbar('Task', 'Task Groups');
  		$this->see($taskGroup);
  		$this->click($taskGroup, '.tablesorter');
  		$this->click('Edit Task Group');
  		$this->waitForElement('#title');
  		$fields=[];
  		if (!empty($data['title'])) $fields['title']=$data['title'];
  		if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
  		if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
  		if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
  		if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
  		if (!empty($data['description'])) $fields['rte:description']=$data['description'];
  		if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
  		$this->fillForm($fields);
  		$this->click('Update');
      $this->see('Task Group ' . $data['title'] . ' updated.');
  	}

    public function deleteTaskGroup($taskGroup) {
  		$this->clickCmfiveNavbar('Task', 'Task Groups');
      $this->click($taskGroup);
      $this->click('Delete Task Group');
      $this->waitForElement('#cmfive-modal');
      $this->wait(1);
      $this->see('To be able to delete a task group, please ensure there are no active tasks');
      $this->click('.close-reveal-modal');
      $taskgroup_url = $this->grabFromCurrentUrl();
      $tasks = $this->grabMultiple('h4 + table td:nth-child(1) a', 'href');
      foreach ($tasks as $task) {
        $this->deleteTask($task);
      }
      $this->amOnPage($taskgroup_url);
      $this->click('Delete Task Group');
      $this->wait(1);
      $this->click('#cmfive-modal button');
      $this->see('Task Group ' . $taskGroup . ' deleted.');
  	}

    public function addMemberToTaskGroup($taskGroup,$userLabel,$role) {
      $this->clickCmfiveNavbar('Task', 'Task Groups');
			$this->click($taskGroup);
			$this->click('Add New Members');
			$this->waitForElement('#role');
			$this->selectOption('#role',$role);
			$this->selectOption('#member',$userLabel);
  		$this->click('Submit');
      $this->see('Task Group updated');
  	}

    public function updateMemberInTaskGroup($taskGroup,$user,$role) {
  		$this->clickCmfiveNavbar('Task', 'Task Groups');
      $this->click($taskGroup);
      $row = $this->findTableRowMatching(1, $user);
      $this->click('Edit', 'tbody tr:nth-child('.$row . ')');
      $this->waitForElement('#role');
      $this->selectOption('#role',$role);
      $this->click('Update');
  		$this->see('Task Group updated');
  	}

  	public function removeMemberFromTaskGroup($taskGroup,$user) {
      $this->clickCmfiveNavbar('Task', 'Task Groups');
      $this->click($taskGroup);
      $row = $this->findTableRowMatching(1, $user);
      $this->click('Delete', 'tbody tr:nth-child('.$row . ')');
      $this->waitForElement('#cmfive-modal button');
      $this->click('#cmfive-modal button');
      $this->see('Task Group updated');
  	}

    public function createTask($taskGroup,$task,$data) {
  		$this->clickCmfiveNavbar('Task', 'New Task');
  		// workaround below
  		$this->executeJS("$('#acp_task_group_id').autocomplete('search', 'testgroup')");
  		$this->click('testgroup updated');
      $this->wait(1);
  		// ends here, more investigation needed to make it a function or figure out the reason can't use fillField.
  		$this->fillForm(['select:task_type'=>!empty($data['task_type']) ? $data['task_type'] : '',
  			'title'=>$task,
  			'select:status'=>!empty($data['status']) ? $data['status'] : '',
  			'select:priority'=>!empty($data['priority']) ? $data['priority'] : '',
  			'date:dt_due'=>$data['dt_due'],
  			'select:assignee_id'=>!empty($data['assignee_id']) ? $data['assignee_id'] : '',
  			'estimate_hours'=>!empty($data['estimate_hours']) ?  $data['estimate_hours'] : '',
  			'effort'=>!empty($data['effort']) ? $data['effort'] : '',
  			'rte:description'=>!empty($data['description']) ?  $data['description'] : '',
  		]);
  		$this->click('Save');
      $this->waitForElementNotVisible('.loading_overlay');
  	}

    public function editTask($task,$data) {
      $this->clickCmfiveNavbar('Task', 'Task List');
      $this->click('Filter');
      $this->click($task);
  		$this->fillForm([
        // 'select:task_type'=>!empty($data['task_type']) ? $data['task_type'] : '',
  			// 'title'=>$task,
  			'select:status'=>!empty($data['status']) ? $data['status'] : '',
  			// 'select:priority'=>!empty($data['priority']) ? $data['priority'] : '',
  			// 'date:dt_due'=>!empty($data['priority']) ? $data['dt_due'] : '',
  			// 'select:assignee_id'=>!empty($data['assignee_id']) ? $data['assignee_id'] : '',
  			// 'estimate_hours'=>!empty($data['estimate_hours']) ?  $data['estimate_hours'] : '',
  			// 'effort'=>!empty($data['effort']) ? $data['effort'] : '',
  			// 'rte:description'=>!empty($data['description']) ?  $data['description'] : '',
  		]);
  		$this->click('.savebutton');
      $this->waitForElementNotVisible('.loading_overlay');
  	}

    public function deleteTask($url) {
      $this->amOnUrl($url);
      $this->click('Delete');
      $this->acceptPopup();
    }
}
