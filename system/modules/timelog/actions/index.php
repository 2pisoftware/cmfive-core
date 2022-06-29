<?php

define('TIMELOG_DEFAULT_PAGE', 1);
define('TIMELOG_DEFAULT_PAGE_SIZE', 20);

function index_GET(Web $w)
{
    $page = Request::int("p", TIMELOG_DEFAULT_PAGE);
    $pagesize = Request::int("ps", TIMELOG_DEFAULT_PAGE_SIZE);

    // Get paged timelogs
    $timelog = TimelogService::getInstance($w)->getTimelogsForUser(AuthService::getInstance($w)->user(), false, $page, $pagesize);
    $totalresults = TimelogService::getInstance($w)->countTotalTimelogsForUser(AuthService::getInstance($w)->user(), false);

    $w->ctx('pagination', Html::pagination($page, (ceil($totalresults / $pagesize)), $pagesize, $totalresults, '/timelog'));

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
