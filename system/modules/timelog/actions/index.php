<?php

define('TIMELOG_DEFAULT_PAGE', 1);
define('TIMELOG_DEFAULT_PAGE_SIZE', 20);

function index_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
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

    //FIXME build tables

    $timelog_table_headers = ['From', 'To', 'Object', 'Description', 'Actions'];
    $timelog_table_column_widths = [10, 10, 24, 40, 16];

    $time_entries = $time_entry_objects;
    // Cannot use inbuilt table functions as do not allow for fixed width columns
    $timelog_table = '';
    if (!empty($time_entries)) {
        foreach ($time_entries as $date => $entry_struct) {
            // Build table for each day, including headers
            $timelog_table .= '<div style="padding-bottom: 20px;">';
            $timelog_table .= '<h4 style="border-bottom: 1px solid #777;">' . $date . '<span style="float: right;">' . TaskService::getInstance($w)->getFormatPeriod($entry_struct['total']) . '</span></h4>';
            $timelog_table .= '<table class="table-striped"><thead><tr>';

            //not every day will have the same number of columns depending on additional info from external modules
            for ($index = 0; $index < count($timelog_table_headers); $index++) {
                $timelog_table .= '<th width="' . $timelog_table_column_widths[$index] . '%">' . $timelog_table_headers[$index] . '</th>';
            }
            $timelog_table .= '</tr></thead><tbody>';

            foreach ($entry_struct['entries'] as $time_entry) {
                //load, if it exists, additional details
                $hook_response = $w->callHook('task', 'additional_details', $time_entry->getLinkedObject());
                $additional_details = $hook_response[0];
                $project = !empty($additional_details[0]) ? $additional_details[0][1] : 'No project assigned';

                $timelog_table .= '<tr>';
                $timelog_table .= '<td>' . formatDate($time_entry->dt_start, "H:i:s") . '</td>';
                $timelog_table .= '<td>' . formatDate($time_entry->dt_end, "H:i:s") . '</td>';
                $timelog_table .= '<td>' . ($time_entry->getLinkedObject() ? (get_class($time_entry->getLinkedObject()) . ": " . $time_entry->getLinkedObject()->toLink()) : '') . '</td>';
                $timelog_table .= '<td>' . ($time_entry->getComment() ? $time_entry->getComment()->comment : 'no comment found') . '</td>';
                $timelog_table .= '<td>' . HtmlBootstrap5::buttonGroup(($time_entry->object_class == 'Task' ? HtmlBootstrap5::b('/task/edit/' . $time_entry->object_id . "#timelog", "View All", null, null, false, "btn btn-primary") : '') .
                        ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::box('/timelog/edit/' . $time_entry->id, "Edit", true, null, null, null, "isbox", null, "btn btn-secondary") : '') .
                        ($time_entry->canEdit(AuthService::getInstance($w)->user()) || $time_entry->canDelete(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::dropdownButton(
                            "More",
                            [
                                ($time_entry->canEdit(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::box('/timelog/move/' . $time_entry->id, 'Move', true, null, null, null, "isbox", null, "dropdown-item btn-sm text-start") : ''),
                                '<hr class="dropdown-divider">',
                                ($time_entry->canDelete(AuthService::getInstance($w)->user()) ? HtmlBootstrap5::b('/timelog/delete/' . $time_entry->id, 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message, null, false, "dropdown-item btn-sm text-start") : '')
                            ],
                            "btn-info btn btn-sm rounded-0 rounded-end-1"
                        ) : '')
                ) . '</td>';
                $timelog_table .= '</tr>';
            }
            $timelog_table .= '</tbody></table></div>';
        }
    } else {
        $timelog_table = '<h4>No time logs found</h4>';
    }
    $w->ctx('tables', $timelog_table);
}
