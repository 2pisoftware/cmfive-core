<?php

define('TIMELOG_DEFAULT_PAGE', 1);
define('TIMELOG_DEFAULT_PAGE_SIZE', 20);

function index_GET(Web $w) {
    $page = $w->request("p", TIMELOG_DEFAULT_PAGE);
    $pagesize = $w->request("ps", TIMELOG_DEFAULT_PAGE_SIZE);
    
	// Get paged timelogs
    $timelog = $w->Timelog->getTimelogsForUser($w->Auth->user(), false, $page, $pagesize);
    $totalresults = $w->Timelog->countTotalTimelogsForUser($w->Auth->user(), false);

    $w->ctx('pagination', Html::pagination($page, (ceil($totalresults / $pagesize)), $pagesize, $totalresults, '/timelog'));
    
    $time_entry_objects = array();
	
    if (!empty($timelog)) {
        foreach($timelog as $time_entry) {
            
            $entry_date = date('d/m', $time_entry->dt_start);
            if (empty($time_entry_objects[$entry_date])) {
                $time_entry_objects[$entry_date] = array('entries' => array(), "total" => 0);
            }
            
            $time_entry_objects[$entry_date]['total'] += $time_entry->getDuration();
            $time_entry_objects[$entry_date]['entries'][] = $time_entry; 
        }
    }
    $w->ctx('time_entries', $time_entry_objects);
}
