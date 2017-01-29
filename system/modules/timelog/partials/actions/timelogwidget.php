<?php namespace System\Modules\Timelog;

function timelogwidget(\Web $w) {
	
    $w->ctx("active_log", $w->Timelog->getActiveTimelogForUser());
    
    $w->ctx('tracked_object', ($w->Timelog->hasTrackingObject() ? $w->timelog->getTrackingObject() : null));

}