<?php

class AdminSetAllCommentsToInternal extends CmfiveMigration {

	public function up() {
		// UP

		// Only run this migration if there are no existing internal messages
		// Only then do we assume we are migrating on a system that has only had
		// this concept introduced
		$existing_internal_comment = AdminService::getInstance($this->w)->getObject("Comment", ['is_internal' => 1, 'is_deleted' => 0]);

		if (empty($existing_internal_comment->id)) {
			$this->w->db->update('comment', ['is_internal' => 1])->where('1',1)->execute();
		}
	}

	public function down() {
		// DOWN
		
		// Do nothing
	}

}
