<?php namespace System\Modules\Tag;

function listTags(\Web $w, $params) {
	// Check roles access
	// Admin gets to add new tags globally
	// User can attach an existing tag
	// Different scripts handle this functionality - more checks done in action
	$user = $w->Auth->user();
	
	// Load scripts into main template
	if(!empty($user)) {
		if ($user->hasAnyRole(["tag_admin", "tag_user"])) {
			$w->enqueueScript(["uri" => "/system/modules/tag/assets/js/tagButton.js", "weight" => 500]);
			$w->enqueueStyle(["uri" => "/system/modules/tag/assets/css/tagButton.css", "weight" => 500]);
			
			if($user->hasRole("tag_admin")) {
				$w->enqueueScript(["uri" => "/system/modules/tag/assets/js/tagButtonAdmin.js", "weight" => 499]);
			}
		}
	}
	
	if (!empty($params['object'])) {
		$object_class = get_class($params['object']);
		$object_id = ($params['object']->id === null) ? 0 : $params['object']->id; 
        $limit = (isset($params['limit']) && is_int($params['limit'])) ? $params['limit'] : -1;
        
		$w->ctx("object_class", $object_class);
		$w->ctx("object_id", $object_id);
		
		$tags = $w->Tag->getTagsByObject($object_id, $object_class);
		
		$w->ctx("tags", $tags);
        $w->ctx("limit", $limit);
	} else {
		$w->ctx("object_class", '');
		$w->ctx("object_id", '');
	}
	
	$w->ctx("user", $user);
}
