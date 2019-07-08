<?php

class TaskTaskCompletionDateFix extends CmfiveMigration {

	public function up() {
		// UP
		$query = "UPDATE task t 
        INNER JOIN object_modification om 
             ON (t.id = om.object_id AND om.table_name = 'task')

		SET t.dt_completed = om.dt_modified

		WHERE t.is_closed = 1 
		AND t.dt_completed IS NULL;";

		$this->w->db->sql($query);
	}

	public function preText()
	{
		return null;
	}

	public function postText()
	{
		return null;
	}

	public function description()
	{
		return "Migration thats runs a query updating all task completion dates where the tasks are closed off but the value is incorrectly null. It will update the completion value to the last time they were modified which is most likely when they were closed off.";
	}
}
