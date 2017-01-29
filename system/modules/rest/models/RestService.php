<?php
class RestService extends RestSearchableService {

	private $token;
	/********************************************
	 * Check if the requested class is allowed access via the REST api
	 ********************************************/
	function checkModuleAccess($className) {
		if (in_array($className,Config::get('system.rest_allow'))) {
			return true;
		}
		return false;
	}
	
	/********************************************
	 * Get a token for the rest api.
	 * Only return a token if the user is not already logged in.
	 ********************************************/
	function getTokenJson($api,$username = null, $password = null) {
		$user=$this->w->Auth->user();
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
		return null;
	}

	function listJson($classname, $query, $token,$allowDeleted=false) {
		$checkToken=$this->checkTokenJson($token);
		if (!empty($checkToken)) {
			return $checkToken;
		}
		if (!$this->checkModuleAccess($classname)) {
			return $this->errorJson('No access to '.$classname);
		}
		try {
			$os = $this->search($classname, $query,$allowDeleted);
		} catch (Exception $e) {
			return $this->errorJson($e);
		}
		if ($os) {
			foreach ($os as $o) {
				if ($o->canView($this->w->Auth->user())) {
					$oJson=$o->toArray();
					$ar[] = $oJson;
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
		$o->insert();	
		$o->id=$this->w->ctx('db_inserts')[$classname][0];
		//http_response_code(201);
		//header('Location: '.$this->w->webroot().'/rest/index/'.$classname.'/id/'.$o->id."/?token=".$this->token);
		return $this->successJson($o->toArray());
	} 
	
	function saveJson($classname, $id, $record, $token) {
		$checkToken=$this->checkTokenJson($token);
		if (!empty($checkToken)) {
			return $checkToken;
		}
		if (!$this->checkModuleAccess($classname)) {
			return $this->errorJson('No access to '.$classname);
		}
		
		if (intval($id)>0) { 
			$o = $this->getObject($classname, $id);
			if ($o->canEdit($this->w->Auth->user())) {
				if ($o) {
					$o->fill($record);
					$saveResult=$o->update();
					// validation
					if ($saveResult) {
						if (empty($saveResult['invalid'])) { 
							//http_response_code(204);
							//header('Location: '.$this->w->webroot().'/rest/index/'.$classname.'/id/'.$o->id."/?token=".$this->token);
							// reload database data
							$o=$this->getObjects($classname,['id'=>$id]);
							$oJson=$o[0]->toArray();
							return $this->successJson($oJson);
						} else {
							return $this->errorJson($saveResult['invalid']);
						}
					} else {
						return $this->errorJson('No feedback from save.');
					}

					} else {
					return $this->_saveNew($classname,$record);
				}
			} else {
				http_response_code(403);
				return $this->errorJson('Not allowed');
			}
		}
	}
	
	
	function deleteJson($classname, $id, $token) {
		$checkToken=$this->checkTokenJson($token);
		if (!empty($checkToken)) {
			return $checkToken;
		}
		if (!$this->checkModuleAccess($classname)) {
			return $this->errorJson('No access to '.$classname);
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
