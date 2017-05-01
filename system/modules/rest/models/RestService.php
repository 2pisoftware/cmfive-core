<?php
class RestService extends SearchableDbObject {
	
	private $token;
	
	function checkModuleAccess($className) {
		// STEVER: TODO I am having problems using Config. It is not returning the expected values as per config.php. Changes are not reflected. New values are ??????
		if (!in_array('rest',Config::get('system.allow_module'))) return $this->errorJSON('REST module is disabled');
		//var_dump(Config::get('system'));
		//die();
		$allowed="None";
		if (is_array(Config::get('system.rest_allow'))) {
			$allowed=implode(",",Config::get('system.rest_allow'));
			if (!in_array($className,Config::get('system.rest_allow'))) {
				return $this->errorJSON('REST module is not enabled for this type of record('.$className.')- allowed '.$allowed);
			}
		} else {
			return $this->errorJSON('REST module is not enabled for this type of record('.$className.') - allowed '.$allowed);
		}
	}
	
	// only require API key if user is not already logged in
	function getTokenJson($api='',$username='', $password='') {
		$user=$this->w->Auth->_user;
		if (intval($user->id) > 0) {
			// OK
		} else {
			if ($api && trim($api) == trim(Config::get('system.rest_api_key'))) {
				$user = $this->w->Auth->login($username,$password,null,true);
			} else {
				return $this->errorJson("wrong API key");
			}
		}
		// allow for logged in user
		if ($user) {
			$session = new RestSession($this->w);
			$session->setUser($user);
			$session->insert();
			return $this->successJson($session->token);
		} else {
			return $this->errorJson("authentication failed for ".$username.", ".$password.", ".$api);
		}
	}
	
	/**
	 * Will check if token exists and set the REST user
	 * in the authentication service to facilitate all
	 * normal permission checks
	 *
	 * returns JSON message if error.
	 *
	 * @param unknown $token
	 * @return unknown
	 */
	function checkTokenJson($token) {
		$user=$this->w->Auth->user();
		// bypass token if user is logged in
		if (empty($user)) {
			if (!$token) {
				return $this->errorJson("Missing token");
			}
			$this->token=$token;
			$session = $this->getObject("RestSession", array("token"=>$token));
			if ($session) {
				$user = $session->getUser();
				$this->w->Auth->setRestUser($user);
			} else {
				return $this->errorJson("No session associated with this token");
			}
		}
		return null;
	}
	
	function listJson($classname, $where, $token) {
		$error = $this->checkTokenJson($token);
		if (!$error) $error = $this->checkModuleAccess($classname);
		if ($error) {
			return $error;
		}
		$os = $this->searchObjects($classname, $where);
		if ($os) {
			foreach ($os as $o) {
				if ($o->canView($this->w->Auth->user())) {
					$ar[] = $o->toArray();
				}
			}
			return $this->successJson($ar);
		} else {
			return $this->successJson([]);
		}
		
	}
	
	private function _saveNew($classname,$record) {
		$o = new $classname($this->w);
		$o->fill($record);
		$o->insert(true);
		$o->id=$this->w->ctx('db_inserts')[$classname][0];
		http_response_code(201);
		header('Location: '.$this->w->webroot().'/rest/index/'.$classname.'/id/'.$o->id."/?token=".$this->token);
		return $this->successJson($o->toArray());
	}
	
	function saveJson($classname, $id, $record, $token) {
		$error = $this->checkTokenJson($token);
		if (!$error) $error = $this->checkModuleAccess($classname);
		if ($error) {
			return $error;
		}
		if (intval($id)>0) {
			$o = $this->getObject($classname, $id);
			if ($o) {
				if ($o->canEdit($this->w->Auth->user())) {
					// convert json into array and update object
					//$ar = json_decode($json,true);
					$o->fill($record);
					$o->update(true);
					http_response_code(204);
					//header('Location: '.$this->w->webroot().'/rest/index/'.$classname.'/id/'.$o->id."/?token=".$this->token);
					return $this->successJson($o->toArray());
				} else {
					http_response_code(403);
					return $this->errorJson('Not allowed');
				}
			} else {
				return $this->_saveNew($classname,$record);
			}
		} else {
			return $this->_saveNew($classname,$record);
		}
	}
	
	
	function deleteJson($classname, $id, $token) {
		$error = $this->checkTokenJson($token);
		if (!$error) $error = $this->checkModuleAccess($classname);
		if ($error) {
			return $error;
		}
		
		$o = $this->getObject($classname, $id);
		if ($o) {
			if ($o && $o->canDelete($this->w->Auth->user())) {
				$o->delete();
				http_response_code(204);
				return $this->successJSON('deleted');
			} else {
				http_response_code(403);
				return $this->errorJSON('Not allowed to delete this record');
			}
		} else {
			http_response_code(404);
			return $this->errorJSON('Cannot find record to delete');
		}
	}
	
	function errorJson($message) {
		return json_encode(array("error" => $message));
	}
	
	function successJson($results) {
		return json_encode(array("success" => $results));
	}
	
}
