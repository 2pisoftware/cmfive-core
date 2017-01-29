<?php
class AuditService extends DbService {

	/**
	 *
	 * Adds an entry to the audit table
	 *
	 * The blacklist is a simple array of the form:
	 * array(
	 * 		array("<module>","<action>"),
	 * 		array("<module>","<action>"),
	 * 		...
	 * )
	 *
	 * @param $blacklist
	 */
	function addAuditLogEntry($blacklist = null) {
		// if blacklist exists
		// then bail out if the current module and action
		// is in the list
		if ($blacklist) {
			foreach ($blacklist as $line) {
				if ($line[0] == $this->w->currentModule() &&
				($line[1] == $this->w->currentAction() || $line[1] == "*")) {
					return;
				}
			}
		}
		$log = new Audit($this->w);
		$log->module = $this->w->currentModule();
		$log->submodule = $this->w->currentSubModule();		
		$log->action = $this->w->currentAction();
		$log->path = array_key_exists('REQUEST_URI',$_SERVER) ? $_SERVER['REQUEST_URI'] : '';
		$log->ip = $this->w->requestIpAddress();
		$log->insert();
	}
	
	function addDbAuditLogEntry($action, $class, $id) {
		if ($class != "Audit") {
			$log = new Audit($this->w);
			$log->module = $this->w->currentModule();
			$log->submodule = $this->w->currentSubModule();
			$log->action = $this->w->currentAction();
			$log->path = array_key_exists('REQUEST_URI',$_SERVER) ? $_SERVER['REQUEST_URI'] : '';
			$log->ip = $this->w->requestIpAddress();
			$log->db_action = $action;
			$log->db_class = $class;
			$log->db_id = $id;
			$log->insert();
		}
	}
	
	function getLoggedUsers() {
		$ids = $this->_db->sql("select distinct creator_id from audit")->fetch_all();
		$users = array();
		foreach ($ids as $id) {
			$users[] = $this->getObject("User", $id["creator_id"]);
		}
		return $users;
	}
	
	function getLoggedModules() {
		$modules = $this->_db->sql("select distinct module from audit order by module")->fetch_all();
		foreach ($modules as $m) {
			$list[] = $m['module'];
		}
		return $list;
	}
	
	function getLoggedActions() {
		$actions = $this->_db->sql("select distinct action from audit order by action")->fetch_all();
		foreach ($actions as $m) {
			$list[] = $m['action'];
		}
		return $list;
	}
	
	/**
	 * Return a list of user objects for
	 * all users that have interacted with the
	 * system in the last number of minutes
	 * as defined by $idleMinutes
	 *
	 * @param $idleMinutes (0..59)
	 */
	function getLoggedInUsers($idleMinutes = 10) {
		$users = array();
		$stmt = "SELECT distinct creator_id FROM audit where timediff(now(), dt_created) < '00:" . $idleMinutes . ":00' and creator_id > 0";
		$res = $this->_db->sql($stmt)->fetch_all();
		if ($res && sizeof($res)) {
			foreach ($res as $row) {
				$users[] = $this->getObject("User", $row['creator_id']);
			}
		}
		return $users;
	}
	
}
