<?php

function ajaxSearch_GET(Web $w) {
    $results = $w->Search->getResults($w->request("term"), $w->request("index"));
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
				
                $query = $w->db->get($inst_class->getDbTableName())->where($where)->fetch_all();
                if (!empty($query)) {
                    $query_objects = $inst_class->getObjectsFromRows($class, $query);
                    foreach($query_objects as $query_object) {
                        if ($query_object->canList($w->Auth->user()) || $query_object->canView($w->Auth->user())) {
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