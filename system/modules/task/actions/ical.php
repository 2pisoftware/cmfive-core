<?php

function ical_GET(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
        return null;
    }
    
    $task = TaskService::getInstance($w)->getTask($p['id']);
    if (empty($task->id) || empty($task->dt_due)) {
        return null;
    }
    
//    $timezone = new DateTimeZone('Australia/Sydney');
    $date = date("Ymd", $task->dt_due);
    
    // Borrowed from here http://stackoverflow.com/questions/1463480/how-can-i-use-php-to-dynamically-publish-an-ical-file-to-be-read-by-google-calen
    $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true)) . "@2pisoftware.com
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART;VALUE=DATE:" . $date . "
DTEND;VALUE=DATE:" . $date . "
SUMMARY:" . $task->title . "
END:VEVENT
END:VCALENDAR";
    
    // Set correct content-type-header
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');
    echo $ical;
    exit;
}