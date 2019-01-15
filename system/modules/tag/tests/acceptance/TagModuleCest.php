<?php
class TagModuleCest
{
	var $lookupTitle = 'Your Highness';
    var $username = 'TestUser';
    var $firstname = 'Firstname';
    var $lastname = 'Lastname';

	public function testTags($I) {

		// create_tag_on_task & delete_tag_on_task
		$I->loginAsAdmin($I);
		$I->createUser($I, $this->username, 'password', $this->firstname, $this->lastname, 'Firstname@lastname.com', ['comment', 'favorites_user', 'file_upload', 'help_view', 'help_contact', 'inbox_reader', 'inbox_sender', 'user', 'report_user', 'tag_user', 'task_admin', 'task_user', 'task_group', 'timelog_user']);
        $I->createTaskGroup($I, 'Test Taskgroup',[
    		'task_group_type'=>'To Do',
    		'can_assign'=>'MEMBER',
    		'can_view'=>'GUEST',
    		'can_create'=>'MEMBER',
    		'is_active'=>'Yes',
    		'description'=>'A test group',
    	]);
        $I->addMemberToTaskGroup($I, 'Test Taskgroup', $this->firstname.' '.$this->lastname, 'OWNER');
        $I->createTask($I, 'Test Taskgroup', 'Test Task', [
    		'task_type'=>'To Do',
    		'title'=>'Test Task',
    		'status'=>'New',
    		'priority'=>'Normal',
    		'dt_due'=>strtotime('27/05/1988'),
    		'assignee_id'=>$this->firstname.' '.$this->lastname,
    		'estimate_hours'=>10,
    		'effort'=>11,
    		'description'=>'a test task',
		]);
		
		$I->clickCmfiveNavbar($I,'Task','Task Groups');
		$I->click('Test Taskgroup');
		$I->click('Test Task');
		$I->click("//span[@class='secondary label']");
		$I->click("//div[@class='selectize-input items not-full']");
		$I->fillField("//input[@id='display_tags_Task_1-selectized']","PRIMACY"); 
		
		//$I->wait(80);
		$I->click("Add");

	// 	<td>link=testTask</td>
	// 	<td>click</td>
	// <td>css=#tag_container_Task_1 &gt; span.secondary.label</td>
	// <td>sendKeys</td>
	// <td>//div[@id='tag_Task_1_modal']/div/div/div/div/input</td>
	// <td>testTaskTag</td> 
	// <td>mouseOver</td>
	// <td>//div[@id='tag_Task_1_modal']/div/div/div/div[2]/div/div</td> 
	// <td>clickAt</td>
	// <td>//div[@id='tag_Task_1_modal']/div/div/div/div[2]/div/div</td> 
	// <td>click</td>
	// <td>css=a.close-reveal-modal</td>
	// <td>assertTextPresent</td>
	// <td>testTaskTag</td> 
	// <td>click</td>
	// <td>css=span.info.label</td>
	// <td>sendKeys</td>
	// <td>//div[@id='tag_Task_1_modal']/div/div/div/div/input</td>
	// <td>anotherTestTag</td> <td>click</td>
	// <td>//div[@id='tag_Task_1_modal']/div/div/div/div[2]/div/div</td> 
	// <td>click</td>
	// <td>css=a.close-reveal-modal</td> 
	// <td>clickAndWait</td>
	// <td>link=Task List</td>
	// <td>click</td>
	// <td>name=reset</td>
	// <td>click</td>
	// <td>css=#tag_container_Task_2 &gt; span.secondary.label</td>
	// <td>click</td>
	// <td>//div[@id='tag_Task_2_modal']/div/div/div/div</td> 
	// <td>clickAt</td>
	// <td>//div[@id='tag_Task_2_modal']/div/div/div/div[2]/div/div/div[2]</td> 
	// <td>clickAndWait</td>
	// <td>link=Tag Admin</td>
	// <td>clickAndWait</td>
	// <td>css=button.button.tiny </td> 
	// <td>type</td>
	// <td>id=tag</td>
	// <td>anotherTestTag</td> 
	// <td>clickAndWait</td>
	// <td>//button[@type='submit']</td>
	// <td>assertText</td>
	// <td>css=div.alert-box.warning</td>
	// <td>Tag named 'anotherTestTag' already exists. ×</td> 
	// <td>type</td>
	// <td>id=tag</td>
	// <td>anothertestTagTest</td> 
	// <td>clickAndWait</td>
	// <td>//button[@type='submit']</td> 
	// <td>clickAndWait</td>
	// <td>link=Tag Admin</td>
	// <td>clickAndWait</td>
	// <td>link=Task List</td>
	// <td>click</td>
	// <td>name=reset</td>
	// <td>clickAndWait</td>
	// <td>link=Tag Admin</td> 
	// <td>clickAndWait</td>
	// <td>//button[@onclick=&quot;if(confirm('Are you sure you want to delete the anotherTestTag tag?')) {parent.location='/tag/delete/2'; return false;}&quot;]</td> 
	// <td>assertConfirmation</td>
	// <td>Are you sure you want to delete the anotherTestTag tag?</td>
	// <td>waitForTextPresent</td>
	// <td>deleted</td> 
	// <td>assertText</td>
	// <td>css=div.alert-box.info</td>
	// <td>Tag deleted ×</td> 
	// <td>clickAndWait</td>
	// <td>css=button.button.tiny </td> 
	// <td>type</td>
	// <td>id=tag</td>
	// <td>anotherTestTag</td> 
	// <td>clickAndWait</td>
	// <td>assertText</td>
	// <td>css=div.alert-box.info</td>
	// <td>Tag saved ×</td> 
	// <td>clickAndWait</td>
	// <td>link=Task List</td>

	}
}
