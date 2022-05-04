<?php

function ajaxSearch_GET(Web $w) {
    $results = SearchService::getInstance($w)->getResults(Request::string("term"), Request::string("index"));
    $result_ids = [];
    $result_objects = [];

    // Flatten results
    if (!empty($results[0])) {
        foreach($results[0] as $result) {
            if (empty($result_ids[$result['class_name']])) {
                $result_ids[$result['class_name']] = [];
            }
            
            $result_ids[$result['class_name']][] = $result['object_id'];
        }
        
        // Fetch all objects
        foreach($result_ids as $class => $ids) {
            if (class_exists($class)) {
				/* @var $inst_class DbObject */
                $inst_class = new $class($w);
				
				$where = ['id' => $ids];
				if (in_array('is_deleted', $inst_class->getDbTableColumnNames())) {
					$where['is_deleted'] = 0;
				}
				
                $query = $w->db->get($inst_class->getDbTableName())->where($where)->fetchAll();
                if (!empty($query)) {
                    $query_objects = $inst_class->getObjectsFromRows($class, $query);
                    foreach($query_objects as $query_object) {
                        if ($query_object->canList(AuthService::getInstance($w)->user()) || $query_object->canView(AuthService::getInstance($w)->user())) {
                            $autocomplete = new stdClass();
                            $autocomplete->value = $query_object->id . " - " . $query_object->getSelectOptionTitle();
                            $autocomplete->id = $query_object->id; //getSelectOptionValue();
                            $result_objects[] = $autocomplete;
                        }
                    }
                }
            }
        }
    }
    
    echo json_encode($result_objects);
}