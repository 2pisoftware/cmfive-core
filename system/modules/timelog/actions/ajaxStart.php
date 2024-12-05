<?php

function ajaxStart_POST(Web $w) {
    $w->setLayout(null);

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
        $time_object = new DateTime(date('d-m-Y',time()) . ' ' . $start_string);
        $start_time = $time_object->getTimestamp();
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
            'start_time'=>$start_time
        ]));
    } else {
        LogService::getInstance($w)->debug("object not found");
        return "0";
    }
}