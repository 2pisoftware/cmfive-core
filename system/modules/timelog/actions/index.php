<?php

define('TIMELOG_DEFAULT_PAGE', 1);
define('TIMELOG_DEFAULT_PAGE_SIZE', 20);

function index_GET(Web $w) {
    $page = $w->request("p", TIMELOG_DEFAULT_PAGE);
    $page_size = $w->request("ps", TIMELOG_DEFAULT_PAGE_SIZE);
    
    //Get days in their 10 day groupings
    $days_with_timelogs = TimelogService::getInstance($w)->daysForTimelogs($w->Auth->user());
    //Get the day group corrosponding to the current page
    $page_bracket = $days_with_timelogs[$page - 1];
    //Get timelogs to display that belong to the corrosponding 10 days
    $timelog = TimelogService::getInstance($w)->getTimelogsForUser($w->Auth->user(), false, $page_bracket[count($page_bracket) - 1], $page_bracket[0]);

    $total_results = TimelogService::getInstance($w)->countTotalTimelogsForUser($w->Auth->user(), false);

    $w->ctx('pagination', Html::pagination($page, (count($days_with_timelogs)), $page_size, ($total_results), '/timelog'));
    
    $time_entry_objects = [];

    if (!empty($timelog)) {
        foreach ($timelog as $time_entry) {
            
            $entry_date = date('d/m', $time_entry->dt_start);
            if (empty($time_entry_objects[$entry_date])) {
                $time_entry_objects[$entry_date] = ['entries' => [], "total" => 0];
            }

            $time_entry_objects[$entry_date]['total'] += $time_entry->getDuration();
            $time_entry_objects[$entry_date]['entries'][] = $time_entry; 
        }
    }

    $w->ctx('time_entries', $time_entry_objects);
}
