<?php

class TaskIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('task', 'is_closed');
		$this->addIndexToTable('task', 'parent_id');
		$this->addIndexToTable('task', 'task_group_id');
		$this->addIndexToTable('task', 'assignee_id');
		$this->addIndexToTable('task', 'is_deleted');
		
		$this->addIndexToTable('task_data', 'task_id');
		
		$this->addIndexToTable('task_group', 'is_active');
		$this->addIndexToTable('task_group', 'default_assignee_id');
		$this->addIndexToTable('task_group', 'is_deleted');
		
		$this->addIndexToTable('task_group_member', 'task_group_id');
		$this->addIndexToTable('task_group_member', 'user_id');
		$this->addIndexToTable('task_group_member', 'is_active');
		
		$this->addIndexToTable('task_group_notify', 'task_group_id');
		
		$this->addIndexToTable('task_group_user_notify', 'task_group_id');
		$this->addIndexToTable('task_group_user_notify', 'user_id');
		$this->addIndexToTable('task_group_user_notify', 'value');
		
		$this->addIndexToTable('task_object', 'task_id');
		$this->addIndexToTable('task_object', 'table_name');
		$this->addIndexToTable('task_object', 'object_id');
		
		$this->addIndexToTable('task_user_notify', 'user_id');
		$this->addIndexToTable('task_user_notify', 'task_id');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('task', 'is_closed');
		$this->removeIndexFromTable('task', 'parent_id');
		$this->removeIndexFromTable('task', 'task_group_id');
		$this->removeIndexFromTable('task', 'assignee_id');
		$this->removeIndexFromTable('task', 'is_deleted');
		
		$this->removeIndexFromTable('task_data', 'task_id');
		
		$this->removeIndexFromTable('task_group', 'is_active');
		$this->removeIndexFromTable('task_group', 'default_assignee_id');
		$this->removeIndexFromTable('task_group', 'is_deleted');
		
		$this->removeIndexFromTable('task_group_member', 'task_group_id');
		$this->removeIndexFromTable('task_group_member', 'user_id');
		$this->removeIndexFromTable('task_group_member', 'is_active');
		
		$this->removeIndexFromTable('task_group_notify', 'task_group_id');
		
		$this->removeIndexFromTable('task_group_user_notify', 'task_group_id');
		$this->removeIndexFromTable('task_group_user_notify', 'user_id');
		$this->removeIndexFromTable('task_group_user_notify', 'value');
		
		$this->removeIndexFromTable('task_object', 'task_id');
		$this->removeIndexFromTable('task_object', 'table_name');
		$this->removeIndexFromTable('task_object', 'object_id');
		
		$this->removeIndexFromTable('task_user_notify', 'user_id');
		$this->removeIndexFromTable('task_user_notify', 'task_id');
	}

}
