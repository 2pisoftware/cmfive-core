<?php
class RestSession extends DbObject {
	public $user_id;
	public $token;
	public $dt_created;
	public $dt_modified;
	
	function setUser(User $user) {
		if ($user) {
			$this->user_id = $user->id;
			$this->token = sha1($user->id.$user->getFullName().time());
		}
	}
	
	function getUser() {
		if ($this->user_id) {
			return $this->getObject("User", $this->user_id);
		}
	}
	
	function getDbTableName() {
		return "rest_session";
	}
	
}