<?php
namespace Helper;

class CmfiveTaskModule extends \Codeception\Module
{
 

  public function createTaskGroup($I,$taskGroup,$data) {
        $I->clickCmfiveNavbar($I,'Task', 'Task Groups');
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
        $I->fillForm($fields);
        $I->click('Save');
        $I->see('Task Group '.$taskGroup.' added');
    }

  public function updateTaskGroup($I,$taskGroup,$data) {
    $I->clickCmfiveNavbar($I,'Task', 'Task Groups');
        $I->see($taskGroup);
        $I->click($taskGroup, '.tablesorter');
        $I->click('Edit Task Group');
        $I->waitForElement('#title');
        $fields=[];
        if (!empty($data['title'])) $fields['title']=$data['title'];
        if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
        if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
        if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
        if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
        if (!empty($data['description'])) $fields['rte:description']=$data['description'];
        if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
        $I->fillForm($fields);
        $I->click('Update');
    $I->see('Task Group ' . $data['title'] . ' updated.');
    }

  public function deleteTaskGroup($I,$taskGroup) {
        $I->clickCmfiveNavbar($I,'Task', 'Task Groups');
    $I->click($taskGroup);
    $I->click('Delete Task Group');
    $I->waitForElement('#cmfive-modal');
    $I->wait(1);
    $I->see('To be able to delete a task group, please ensure there are no active tasks');
    $I->click('.close-reveal-modal');
    $taskgroup_url = $I->grabFromCurrentUrl();
    $tasks = $I->grabMultiple('h4 + table td:nth-child(1) a', 'href');
    foreach ($tasks as $task) {
      $I->deleteTask($I,$task);
    }
    $I->amOnPage($taskgroup_url);
    $I->click('Delete Task Group');
    $I->wait(1);
    $I->click('#cmfive-modal button');
    $I->see('Task Group ' . $taskGroup . ' deleted.');
    }

  public function addMemberToTaskGroup($I,$taskGroup,$userLabel,$role) {
    $I->clickCmfiveNavbar($I,'Task', 'Task Groups');
          $I->click($taskGroup);
          $I->click('Add New Members');
          $I->waitForElement('#role');
          $I->selectOption('#role',$role);
          $I->selectOption('#member',$userLabel);
        $I->click('Submit');
    $I->see('Task Group updated');
    }

  public function updateMemberInTaskGroup($I,$taskGroup,$user,$role) {
        $I->clickCmfiveNavbar($I,'Task', 'Task Groups');
    $I->click($taskGroup);
    $row = $I->findTableRowMatching(1, $user);
    $I->click('Edit', 'tbody tr:nth-child('.$row . ')');
    $I->waitForElement('#role');
    $I->selectOption('#role',$role);
    $I->click('Update');
        $I->see('Task Group updated');
    }

    public function removeMemberFromTaskGroup($I,$taskGroup,$user) {
    $I->clickCmfiveNavbar($I,'Task', 'Task Groups');
    $I->click($taskGroup);
    $row = $I->findTableRowMatching(1, $user);
    $I->click('Delete', 'tbody tr:nth-child('.$row . ')');
    $I->waitForElement('#cmfive-modal button');
    $I->click('#cmfive-modal button');
    $I->see('Task Group updated');
    }

  public function createTask($I,$taskGroup,$task,$data) {
        $I->clickCmfiveNavbar($I,'Task', 'New Task');
        // workaround below
        $I->executeJS("$('#acp_task_group_id').autocomplete('search', 'testgroup')");
        $I->click('testTasks_testgroup updated');
    $I->wait(1);
        // ends here, more investigation needed to make it a function or figure out the reason can't use fillField.
        $I->fillForm(['select:task_type'=>!empty($data['task_type']) ? $data['task_type'] : '',
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
    $I->waitForBackendToRefresh($I);
    }

  public function editTask($I,$task,$data) {
    $I->clickCmfiveNavbar($I,'Task', 'Task List');
    $I->click('Filter');
    $I->click($task);
        $I->fillForm([
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
        $I->click('.savebutton');
        $I->waitForBackendToRefresh($I);
    }

  public function deleteTask($I,$url) {
    $I->amOnUrl($url);
    $I->click('Delete');
    $I->acceptPopup();
  }

}