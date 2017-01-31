<?php

class TaskAddSubscriber extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		// Create task table
		if (!$this->hasTable("task_subscriber")) {
			$this->table('task_subscriber', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addIdColumn("task_id")
				->addIdColumn("user_id")
				->addCmfiveParameters()
				->create();
		}

		$tasks = $this->w->Task->getTasks();
		if (!empty($tasks)) {
			foreach($tasks as $task) {
				$taskgroup = $task->getTaskGroup();

				$members = $taskgroup->getMembers();

				if (!empty($members)) {
					foreach($members as $member) {
						$subscriber = new TaskSubscriber($this->w);
						$subscriber->task_id = $task->id;
						$subscriber->user_id = $member->user_id;
						$subscriber->insert();
					}
				}
			}
		}
	}

	public function down() {
		$this->hasTable('task_subscriber') && $this->dropTable('task_subscriber');
	}
}