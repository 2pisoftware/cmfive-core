<?php
namespace Step\Acceptance;

class CmfiveTaskModule extends \CmfiveUI
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
        $this->click('testTasks_testgroup updated');
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
    $this->waitForElementNotVisible('.loading_overlay',8);
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
    $this->waitForElementNotVisible('.loading_overlay',8);
    }

  public function deleteTask($url) {
    $this->amOnUrl($url);
    $this->click('Delete');
    $this->acceptPopup();
  }

}