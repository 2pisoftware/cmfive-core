<?php

function timelog_core_template_menu(Web $w)
{
    if (TimelogService::getInstance($w)->shouldShowTimer()) {
        return $w->partial('timelogwidget', null, 'timelog');
    }
}

// delete any timelogs attached to deleted object
function timelog_core_dbobject_after_delete($w, $obj)
{
    $timelogs = TimelogService::getInstance($w)->getTimelogsForObject($obj);
    if (!empty($timelogs)) {
        foreach ($timelogs as $timelog) {
            $timelog->delete();
        }
    }
}
