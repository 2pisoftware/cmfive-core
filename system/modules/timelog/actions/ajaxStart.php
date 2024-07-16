<?php

function ajaxStart_POST(Web $w) {
    LogService::getInstance($w)->setLogger('TIMELOG')->debug("cookies: " . json_encode($_COOKIE));

    if (TimelogService::getInstance($w)->hasActiveLog()) {
        LogService::getInstance($w)->debug("active log exists");
		
        return "0";
    }
    
    $p = $w->pathMatch("class", "id");
    
    
    if (!class_exists($p['class'])) {
        LogService::getInstance($w)->debug("class " . $p['class'] . " doesnt exist");
        return "0";
    }
    
    $object = TimelogService::getInstance($w)->getObject($p['class'], $p['id']);
    
    //buid start_time timestamp
    $start_time = null;
    if (!empty($_POST['start_time'])) {
        $start_string = $_POST['start_time'];
        $start_time = (new DateTime('now', new DateTimeZone('UTC')))->format('d-m-Y') . ' ' . $start_string;
    }
    
    
    if (!empty($object->id)) {
        $timelog = new Timelog($w);
        $timelog->fill($_POST);
        $timelog->start($object, $start_time);
		
		if (!empty($_POST['description'])) {
			$timelog->setComment($_POST['description']);
		}
		
        $w->out(json_encode([
            'object'    => $p['class'],
            'title'     => $object->getSelectOptionTitle(),
            'start_time'=> strtotime(str_replace("/", "-", $start_time))
        ]));
    } else {
        LogService::getInstance($w)->debug("object not found");
		return "0";
    }
}