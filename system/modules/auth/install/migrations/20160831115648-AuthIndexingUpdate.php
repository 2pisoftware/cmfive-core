<?php

class AuthIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('user', 'is_admin');
		$this->addIndexToTable('user', 'is_active');
		$this->addIndexToTable('user', 'is_deleted');
		$this->addIndexToTable('user', 'contact_id');
		
		// There is already an index on user_id for user_role
		$this->addIndexToTable('user_role', 'is_deleted');
		
		$this->addIndexToTable('group_user', 'group_id');
		$this->addIndexToTable('group_user', 'user_id');
		$this->addIndexToTable('group_user', 'is_active');
		
		$this->addIndexToTable('contact', 'is_deleted');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('user', 'is_admin');
		$this->removeIndexFromTable('user', 'is_active');
		$this->removeIndexFromTable('user', 'is_deleted');
		$this->removeIndexFromTable('user', 'contact_id');
		
		// There is already an index on user_id for user_role
		$this->removeIndexFromTable('user_role', 'is_deleted');
		
		$this->removeIndexFromTable('group_user', 'group_id');
		$this->removeIndexFromTable('group_user', 'user_id');
		$this->removeIndexFromTable('group_user', 'is_active');
		
		$this->removeIndexFromTable('contact', 'is_deleted');
	}

}
