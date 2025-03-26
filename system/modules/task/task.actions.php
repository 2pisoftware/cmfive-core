<?php

function editComment_GET(Web &$w) {
    $p = $w->pathMatch("taskid", "comm_id");

    // get the relevant comment
    $comm = TaskService::getInstance($w)->getComment($p['comm_id']);

    // build the comment for edit
    $form = array(
        array("Comment", "section"),
        array("", "textarea", "comment", strip_tags($comm->comment ?? ""), 45, 25),
    );

    // return the comment for display and edit
    $form = HtmlBootstrap5::form($form, $w->localUrl("/task/editComment/" . $p['taskid'] . "/" . $p['comm_id']), "POST", "Save");
    $w->setLayout(null);
    $w->out($form);
}

function popComment_GET(Web &$w) {
    $p = $w->pathMatch("taskid", "comm_id");

    // get the relevant comment
    $comm = TaskService::getInstance($w)->getComment($p['comm_id']);

    // build the comment for display
    $form = array(
        array("Comment", "section"),
        array("", "textarea", "comment", strip_tags($comm->comment ?? ""), 45, 25),
    );

    // return the comment for display
    $form = HtmlBootstrap5::form($form);
    $w->setLayout(null);
    $w->out($form);
}

function editComment_POST(Web $w) {
    $p = $w->pathMatch("taskid", "comm_id");
    $task = TaskService::getInstance($w)->getTask($p['taskid']);

    // convert any HTML to entities for display
    $_REQUEST['comment'] = htmlspecialchars($_REQUEST['comment']);

    // get the relevant comment
    $comm = TaskService::getInstance($w)->getComment($p['comm_id']);

    // if comment exists, update it. if not, create it.
    if ($comm) {
        $comm->fill($_REQUEST);
        $comm->update();
        $commsg = "Comment updated.";
    } else {
        $comm = new TaskComment($w);
        $comm->fill($_REQUEST);
        $comm->obj_table = $task->getDbTableName();
        $comm->obj_id = $p['taskid'];
        $comm->insert();
        $commsg = "Comment created.";
    }
    // add to context for notifications post listener
    $w->ctx("TaskComment", $comm);
    $w->ctx("TaskEvent", "task_comments");

    // return
    $w->msg($commsg, "/task/edit/" . $p['taskid'] . "#timelog");
}

function attachForm_GET(Web $w) {
    $p = $w->pathMatch("id");

    // get relevant task
    $task = TaskService::getInstance($w)->getTask($p['id']);

    // build form to upload document/attachment
    $form = array(
        array("Attach Document", "section"),
        array("Document", "file", "form"),
        array("Description", "textarea", "description", null, "26", "6"),
    );

    // diplay form
    $form = HtmlBootstrap5::form($form, $w->localUrl("/task/attachForm/" . $task->id), "POST", " Upload ", null, null, null, 'multipart/form-data');

    $w->setLayout(null);
    $w->out($form);
}

function attachForm_POST(Web $w) {
    $p = $w->pathMatch("id");

    // get relevant task
    $task = TaskService::getInstance($w)->getTask($p['id']);

    // if task exists get REQUEST and FILE object for insert into attachment database against this task
    if ($task) {
        $description = Request::string('description');

        if ($_FILES['form']['size'] > 0) {
            $filename = strtolower($_FILES['form']['name']);
            $parts = explode(".", $filename);
            $n = count($parts) - 1;
            $ext = $parts[$n];

            $attach = FileService::getInstance($w)->uploadAttachment("form", $task, null, $description);
            if (!$attach) {
                $message = "There was an error. The document could not be saved.";
            } else {
                $message = "The Document has been uploaded.";
            }
        }

        // create comment
        $comm = new TaskComment($w);
        $comm->obj_table = $task->getDbTableName();
        $comm->obj_id = $task->id;
        $comm->comment = "File Uploaded: " . $filename;
        $comm->insert();

        // add to context for notifications post listener
        $w->ctx("TaskComment", $comm);
        $w->ctx("TaskEvent", "task_documents");
    }

    // return
    $w->msg($message, "/task/edit/" . $task->id . "#attachments");
}

// @todo: PageService doesn't exist!
// function addpage_GET(Web &$w) {
//     $p = $w->pathMatch("id");

//     // get list of pages accessible to me
//     $pages = $w->Page->getUserPageTitles();

//     // create form
//     $f = array(
//         array("Select a Page", "section"),
//         array("Page", "autocomplete", "page", null, $pages)
//     );

//     $form = HtmlBootstrap5::form($f, $w->localUrl("/task/addpage/" . $p['id']), "POST", "Save");

//     // return and display form
//     $w->setLayout(null);
//     $w->out($form);
// }

// function addpage_POST(Web &$w) {
//     $p = $w->pathMatch("id");

//     if ($_POST['page'] == "0") {
//         // 'blank' selected so return
//         $w->msg("Please select a PAGE", "/task/edit/" . $p['id'] . "#documents");
//     } else {
//         // get relevant task
//         $task = TaskService::getInstance($w)->getTask($p['id']);
//         // get page
//         $page = $w->Page->getPage($w, $_REQUEST['page']);
//         // get first 100 characters, minus HTML tags, for display as 'description'
//         $content = substr(strip_tags($page->body), 0, 100);

//         // create Task Object
//         $obj = new TaskObject($w);
//         $obj->task_id = $p['id'];
//         $obj->key = $content;
//         $obj->table_name = "page";
//         $obj->object_id = $_REQUEST['page'];
//         $obj->insert();

//         // create comment
//         $comm = new TaskComment($w);
//         $comm->obj_table = $task->getDbTableName();
//         $comm->obj_id = $task->id;
//         $comm->comment = "Page Attached to Task: " . $page->subject;
//         $comm->insert();

//         // add to context for notifications post listener
//         $w->ctx("TaskComment", $comm);
//         $w->ctx("TaskEvent", "task_pages");

//         // return
//         $w->msg("Page Added to Task", "/task/edit/" . $p['id'] . "#documents");
//     }
// }

//////////////////////////////////
//			TIME LOG			//
//////////////////////////////////

function addtime_GET(Web &$w) {
    $p = $w->pathMatch("taskid", "log_id");

    $hours = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24");
    $mins = array("00", "05", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55");

    // get the relevant comment
    $log = TaskService::getInstance($w)->getTimeLogEntry($p['log_id']);
    $task = TaskService::getInstance($w)->getTask($p['taskid']);

    // if log entry exists, populate form with values
    if ($log) {
        $who = $log->user_id;
        $s = date("d/m/Y g:i a", $log->dt_start);
        $e = date("d/m/Y g:i a", $log->dt_end);

        $comm = TaskService::getInstance($w)->getComment($log->comment_id);
        $comment = $comm->comment;
    }
    // if new entry, set current date and time
    else {
        $who = $_SESSION["user_id"];
        $s = $e = date("d/m/Y g:i a");
    }
    
    // picklist of time types from tasktype
    $timeTypes=array();
    $to = $task->getTaskTypeObject();
    if (!empty($to)) {
    	$timeTypes = $to->getTimeTypes();
    }
    
    $f = array(
        array("Add Time Log Entry", "section"),
        array("Assignee", "select", "user_id", $who, TaskService::getInstance($w)->getMembersBeAssigned($task->task_group_id)),
        array("Select the Start Date & Time", "section"),
        array("Date/Time", "datetime", "dt_start", $s),
        array("Select the End Date & Time, or Period worked", "section"),
        array("Date/Time", "datetime", "dt_end", $e),
        array("Or Period:", "static", "OR", "<b>Below select the period worked since the Start Date/Time</b>"),
        array("Hours", "select", "per_hour", null, array_slice($hours, 0, 11)),
        array("Min", "select", "per_minute", null, $mins),
    	array("Time Type", "select", "time_type", (!empty($log)) ? $log->time_type : "", $timeTypes),
        array("Comments", "section"),
        array("Comments", "textarea", "comments", !empty($comment) ? $comment : null, "40", "10"),
    );

    $form = HtmlBootstrap5::form($f, $w->localUrl("/task/edittime/" . $p['taskid'] . "/" . $p['log_id']), "POST", "Save");

    $w->setLayout(null);
    $w->out($form);
}

function edittime_POST(Web $w) {
    $p = $w->pathMatch("taskid", "log_id");

    // lets set some defaults if no selections are made
    $_REQUEST["dt_start"] = ($_REQUEST["dt_start"] != "") ? $_REQUEST["dt_start"] : date("d/m/Y G:i");
    $_REQUEST["dt_end"] = ($_REQUEST["dt_end"] != "") ? $_REQUEST["dt_end"] : date("d/m/Y G:i");

    // get the relevant log entry
    $log = TaskService::getInstance($w)->getTimeLogEntry($p['log_id']);

    // set time log values
    $arr["task_id"] = $p["taskid"];
    $arr["creator_id"] = $_SESSION["user_id"];
    $arr["dt_created"] = date("d/m/Y");
    $arr["user_id"] = $_REQUEST["user_id"];
    $arr["time_type"] = $_REQUEST["time_type"];

    list ($date, $time, $ampm) = preg_split("/\s/", $_REQUEST['dt_start']);
    $start = TaskService::getInstance($w)->date2db($date) . " " . $time . " " . $ampm;
    $arr["dt_start"] = date("Y-m-d G:i", strtotime($start));

    if (($_REQUEST['per_hour'] != "") || ($_REQUEST['per_minute'] != "")) {
        $s = strtotime($arr["dt_start"]);
        $phour = ($_REQUEST["per_hour"] != "") ? $_REQUEST["per_hour"] : 0;
        $pmin = ($_REQUEST["per_minute"] != "") ? $_REQUEST["per_minute"] : 0;
        $arr["dt_end"] = date("Y-m-d G:i", mktime(date("G", $s) + $phour, date("i", $s) + $pmin, 0, date("m", $s), date("d", $s), date("Y", $s)));
    } else {
        list ($date, $time, $ampm) = preg_split("/\s/", $_REQUEST['dt_end']);
        $end = TaskService::getInstance($w)->date2db($date) . " " . $time . " " . $ampm;
        $arr["dt_end"] = date("Y-m-d G:i", strtotime($end));
    }

    // check that end time is later than start time
    if (strtotime($arr["dt_start"]) > strtotime($arr["dt_end"])) {
        $logmsg = "Start is greater than End. Please enter again.";
    } else {
        $logmsg = ($log) ? "Time Log Entry updated." : "Time Log Entry created.";

        // add comment
        $comm = ($log) ? TaskService::getInstance($w)->getObject("Comment", $log->comment_id) : new TaskComment($w);
        $comm->obj_table = "Task";
        $comm->obj_id = $arr["task_id"];
        $comm->comment = $_REQUEST['comments'];
        // I'm not sure whether I want comments in the system!
        $comm->insertOrUpdate();

        // add to context for notifications post listener
        $w->ctx("TaskComment", $comm);
        $w->ctx("TaskEvent", "time_log");

        $arr["comment_id"] = $comm->id;

        // if log entry exists, update it. if not, create it.
        if ($log) {
            $log->fill($arr);
            $log->update();
        } else {
            $log = new TaskTime($w);
            $log->fill($arr);
            $log->insert();
        }
    }

    // return
    $w->msg($logmsg, "/task/edit/" . $p['taskid'] . "#timelog");
}

function suspecttime_ALL(Web &$w) {
    $p = $w->pathMatch("taskid", "log_id");

    // get the relevant log entry
    $log = TaskService::getInstance($w)->getTimeLogEntry($p['log_id']);

    // toggle database field based on current setting
    if ($log->is_suspect == "0") {
        $log->is_suspect = 1;
        $logmsg = "Time Log entry marked for review";
    } else {
        $log->is_suspect = 0;
        $logmsg = "Time Log entry accepted";
    }
    $log->update();

    // add comment
    $comm = new TaskComment($w);
    $comm->obj_table = "Task";
    $comm->obj_id = $log->task_id;
    $comm->comment = $logmsg . " - " . formatDateTime($log->dt_start) . " to " . formatDateTime($log->dt_end);
    $comm->insert();

    // add to context for notifications post listener
    $w->ctx("TaskComment", $comm);
    $w->ctx("TaskEvent", "time_log");

    $w->msg($logmsg, "/task/edit/" . $p['taskid'] . "#timelog");
}

function deletetime_ALL(Web &$w) {
    $p = $w->pathMatch("taskid", "log_id");

    // get the relevant log entry
    $log = TaskService::getInstance($w)->getTimeLogEntry($p['log_id']);

    // if log entry exists, continue
    if ($log) {
        $arr['is_deleted'] = 1;
        $log->fill($arr);
        $log->update();

        // add comment
        $comm = new TaskComment($w);
        $comm->obj_table = "Task";
        $comm->obj_id = $log->task_id;
        $comm->comment = "Time Log Entry deleted: " . TaskService::getInstance($w)->getUserById($log->user_id) . " - " . formatDateTime($log->dt_start) . " to " . formatDateTime($log->dt_end);
        $comm->insert();

        // add to context for notifications post listener
        $w->ctx("TaskComment", $comm);
        $w->ctx("TaskEvent", "time_log");

        $w->msg("Time Log entry has been deleted.", "/task/edit/" . $p['taskid'] . "#timelog");
    } else {
        $w->msg("Time Log entry could not be found.", "/task/edit/" . $p['taskid'] . "#timelog");
    }
}

// popup task time logger
function starttimelog_ALL(Web &$w) {
    $p = $w->pathMatch("id");

    if (!empty($_POST['started']) && ($_POST["started"] == "yes")) {
        // get time log
        $log = TaskService::getInstance($w)->getTimeLogEntry($_POST['logid']);

        // update time log entry
        $log->dt_end = date("Y-m-d G:i");
        $log->update();

        // set page variables
        $start = date("Y-m-d G:i", $log->dt_start);
        $end = $log->dt_end;
        $taskid = $_POST['taskid'];
        $tasktitle = $_POST['tasktitle'];
        $logid = $_POST['logid'];
    } else {
        // get the task
        $task = TaskService::getInstance($w)->getTask($p['id']);

        // set time log values
        $arr["task_id"] = $task->id;
        $arr["creator_id"] = $_SESSION["user_id"];
        $arr["dt_created"] = date("d/m/Y");
        $arr["user_id"] = $_SESSION["user_id"];

        // format start and end times for database
        $start = $arr["dt_start"] = date("Y-m-d G:i");
        $end = $arr["dt_end"] = date("Y-m-d G:i");

        // add time log entry
        $log = new TaskTime($w);
        $log->fill($arr);
        $log->insert();

        // set page variables
        $taskid = $task->id;
        $tasktitle = $task->title;
        $logid = $log->id;
    }

    // create page
    $html = "<html><head><title>Task Time Log - " . $task->title . "</title>" .
            "<style type=\"text/css\">" .
            "body { background-color: #8ad228; }" .
            "td { background-color: #ffffff; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .8em; }" .
            "td.startend { background-color: #d2efab; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .9em; }" .
            "td.timelog { background-color: #75ba4d; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .9em; }" .
            "td.tasktitle { background-color: #9fea72; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .8em; }" .
            "a { text-decoration: none; } " .
            "a:hover { color: #ffffff; } " .
            "</style>" .
            "<script language=\"javascript\">" .
            "var thedate = new Date();" .
            "thedate.setDate(thedate.getDate()+1);" .
            "document.cookie = \"thiswin=true;expires=\" + thedate.toGMTString() + \";path=/\";" .
            "function doUnLoading() {" .
            "	var thedate = new Date();" .
            "	thedate.setDate(thedate.getDate()-1);" .
            "	document.cookie = \"thiswin=true;expires=\" + thedate.toGMTString() + \";path=/\";" .
            "	document.theForm.action = \"/task/endtimelog\";" .
            "	document.theForm.submit();" .
            "}" .
            "function beforeUnLoading() {" .
            "	document.theForm.restart.value = \"yes\";" .
            "	doUnLoading();" .
            "}" .
            "function goTask() {" .
            "	window.opener.location.href = \"/task/edit/" . $taskid . "\";" .
            "}" .
            "</script></head><body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onbeforeunload=\"javascript: doUnLoading();\">" .
            "<form name=theForm action=\"/task/starttimelog\" method=POST>" .
            "<input type=\"hidden\" name=\"" . CSRF::getTokenID() . "\" value=\"" . CSRF::getTokenValue() . "\" />" .
            "<table cellpadding=2 cellspacing=2 border=0 width=100%>" .
            "<tr align=center><td colspan=2 class=timelog>Task Time Log</td></tr>" .
            "<tr align=center><td colspan=2 class=tasktitle><a title=\"View Task\" href=\"javascript: goTask();\">" . $tasktitle . "</a></td></tr>" .
            "<tr align=center><td width=50% class=startend>Start</td><td width=50% class=startend>Stop</td></tr>" .
            "<tr align=center><td>" . date("g:i a", strtotime($start)) . "</td><td>" . date("g:i a", strtotime($end)) . "</td></tr>" .
            "<tr align=center><td colspan=2 class=timelog>&nbsp;</td></tr>" .
            "<tr><td colspan=2 class=startend>Comments</td></tr>" .
            "<tr><td colspan=2 align=center><textarea name=comments rows=4 cols=40>" . (!empty($_POST['comments']) ? $_POST['comments'] : '') . "</textarea></td></tr>" .
            "<tr align=center>" .
            "<td class=timelog align=right><button id=end onClick=\"javascript: beforeUnLoading();\">Save Comments</button></td>" .
            "<td class=timelog align=left><button id=end onClick=\"javascript: doUnLoading();\">Stop Time Now</button></td>" .
            "</tr>" .
            "</table>" .
            "<input type=hidden name=started value=\"yes\">" .
            "<input type=hidden name=restart value=\"no\">" .
            "<input type=hidden name=taskid value=\"" . $taskid . "\">" .
            "<input type=hidden name=tasktitle value=\"" . $tasktitle . "\">" .
            "<input type=hidden name=logid value=\"" . $logid . "\">" .
            "</form>" .
            "<script language=javascript>" .
            "document.theForm.comments.focus();" .
            "var r = setTimeout('theForm.submit()',1000*60*5);" .
            "</script>" .
            "</body></html>";

    // output page
    $w->setLayout(null);
    $w->out($html);
}

function endtimelog_ALL(Web &$w) {
    // get time log
    $log = TaskService::getInstance($w)->getTimeLogEntry($_REQUEST['logid']);
    // get the task
    $task = TaskService::getInstance($w)->getTask($_REQUEST["taskid"]);
    $tasktitle = $task->title;

    if ($log) {
        // set log end. used in comment
        $log->dt_end = date("Y-m-d G:i");

        // set comment
        $comment = "Time Log Entry: " . TaskService::getInstance($w)->getUserById($log->user_id) . " - " . formatDateTime($log->dt_start) . " to " . formatDateTime($log->dt_end);
        if ($_REQUEST['comments'] != "")
            $comment .= " - Comments: " . htmlspecialchars($_REQUEST['comments']);

        // add comment
        $comm = new TaskComment($w);
        $comm->obj_table = $task->getDbTableName();
        $comm->obj_id = $_REQUEST["taskid"];
        $comm->comment = $comment;
        $comm->insert();

        // add to context for notifications post listener
        $w->ctx("TaskComment", $comm);
        $w->ctx("TaskEvent", "time_log");

        // update time log entry
        $log->dt_end = date("Y-m-d G:i");
        $log->comment_id = $comm->id;
        $log->update();
    }

    // if 'Save Comment' display current entry and restart time log
    if ($_REQUEST['restart'] == "yes") {
        // create page
        $html = "<html><head><title>Task Time Log - " . $task->title . "</title>" .
                "<style type=\"text/css\">" .
                "body { background-color: #8ad228; }" .
                "td { background-color: #ffffff; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .8em; }" .
                "td.startend { background-color: #d2efab; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .9em; }" .
                "td.timelog { background-color: #75ba4d; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .9em; }" .
                "td.tasktitle { background-color: #9fea72; color: #000000; font-family: verdana, arial; font-weight: bold; font-size: .8em; }" .
                "a { text-decoration: none; } " .
                "a:hover { color: #ffffff; } " .
                "</style>" .
                "<script language=\"javascript\">" .
                "function reStart() {" .
                "	location.href = \"/task/starttimelog/" . $_REQUEST["taskid"] . "\";" .
                "}" .
                "var c = setTimeout('reStart()',2000);" .
                "</script></head><body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>" .
                "<table cellpadding=2 cellspacing=2 border=0 width=100%>" .
                "<tr align=center><td colspan=2 class=timelog>Task Time Log</td></tr>" .
                "<tr align=center><td colspan=2 class=tasktitle><a title=\"View Task\" href=\"javascript: goTask();\">" . $tasktitle . "</a></td></tr>" .
                "<tr align=center><td width=50% class=startend>Start</td><td width=50% class=startend>Stop</td></tr>" .
                "<tr align=center><td>" . date("g:i a", $log->dt_start) . "</td><td>" . date("g:i a", strtotime($log->dt_end)) . "</td></tr>" .
                "<tr align=center><td colspan=2 class=timelog>&nbsp;</td></tr>" .
                "<tr><td colspan=2 class=startend>Comments</td></tr>" .
                "<tr><td colspan=2>" . str_replace("\n", "<br>", $_POST['comments']) . "</td></tr>" .
                "</table>" .
                "</body></html>";
    } else {
        $html = "<html><head>" .
                "<script language=\"javascript\">" .
                "self.close();" .
                "</script></head></html>";
    }

    // output page
    $w->setLayout(null);
    $w->out($html);
}

//////////////////////////////////////
//		TASK NOTIFICATIONS			//
//////////////////////////////////////

function updateusergroupnotify_GET(Web &$w) {
    $p = $w->pathMatch("id");

    // get task title
    $title = TaskService::getInstance($w)->getTaskGroupTitleById($p['id']);

    // get member
    $member = TaskService::getInstance($w)->getMemberGroupById($p['id'], $_SESSION['user_id']);

    // get user notify settings for Task Group
    $notify = TaskService::getInstance($w)->getTaskGroupUserNotify($_SESSION['user_id'], $p['id']);
    if ($notify) {
        foreach ($notify as $n) {
            $v[$n->role][$n->type] = $n->value;
            $task_creation = $n->task_creation;
            $task_details = $n->task_details;
            $task_comments = $n->task_comments;
            $time_log = $n->time_log;
            $task_documents = $n->task_documents;
            $task_pages = $n->task_pages;
        }
    }
    // no user notify? get default group settings. set all task events on
    else {
        $notify = TaskService::getInstance($w)->getTaskGroupNotify($p['id']);
        if ($notify) {
            foreach ($notify as $n) {
                $v[$n->role][$n->type] = $n->value;
                $task_creation = 1;
                $task_details = 1;
                $task_comments = 1;
                $time_log = 1;
                $task_documents = 1;
                $task_pages = 1;
            }
        }
    }

    // if no user notifications and no group defaults
    // set blank form - all task events on - so user can create their user notifications
    if (!$v) {
        $v['guest']['creator'] = 0;
        $v['member']['creator'] = 0;
        $v['member']['assignee'] = 0;
        $v['owner']['creator'] = 0;
        $v['owner']['assignee'] = 0;
        $v['owner']['other'] = 0;
        $task_creation = 1;
        $task_details = 1;
        $task_comments = 1;
        $time_log = 1;
        $task_documents = 1;
        $task_pages = 1;
    }

    $f = array(array($title . " - Notifications", "section"));

    // so foreach role/type lets get the values and create  checkboxes
    foreach ($v as $role => $types) {
        if ($role == strtolower($member->role)) {
            foreach ($types as $type => $value) {
                $f[] = array(ucfirst($type), "checkbox", $role . "_" . $type, $value);
            }
        }
    }

    // add Task Events to form
    $f[] = array("For which events should you receive Notification?", "section");
    $f[] = array("Task Creation", "checkbox", "task_creation", $task_creation);
    $f[] = array("Task Details Update", "checkbox", "task_details", $task_details);
    $f[] = array("Comments Added", "checkbox", "task_comments", $task_comments);
    $f[] = array("Time Log Entry", "checkbox", "time_log", $time_log);
    $f[] = array("Documents Added", "checkbox", "task_documents", $task_documents);
    $f[] = array("Pages Added", "checkbox", "task_pages", $task_pages);

    $f = HtmlBootstrap5::form($f, $w->localUrl("/task/updateusergroupnotify/" . $p['id']), "POST", "Save");

    $w->setLayout(null);
    $w->out($f);
}

function updateusergroupnotify_POST(Web &$w) {
    $p = $w->pathMatch("id");

    // lets set some values knowing that only checked checkboxes return a value
    $arr['guest']['creator'] = $_REQUEST['guest_creator'] ? $_REQUEST['guest_creator'] : "0";
    $arr['member']['creator'] = $_REQUEST['member_creator'] ? $_REQUEST['member_creator'] : "0";
    $arr['member']['assignee'] = $_REQUEST['member_assignee'] ? $_REQUEST['member_assignee'] : "0";
    $arr['owner']['creator'] = $_REQUEST['owner_creator'] ? $_REQUEST['owner_creator'] : "0";
    $arr['owner']['assignee'] = $_REQUEST['owner_assignee'] ? $_REQUEST['owner_assignee'] : "0";
    $arr['owner']['other'] = $_REQUEST['owner_other'] ? $_REQUEST['owner_other'] : "0";

    // set task event notify values
    $task_creation = $_REQUEST['task_creation'] ? $_REQUEST['task_creation'] : "0";
    $task_details = $_REQUEST['task_details'] ? $_REQUEST['task_details'] : "0";
    $task_comments = $_REQUEST['task_comments'] ? $_REQUEST['task_comments'] : "0";
    $time_log = $_REQUEST['time_log'] ? $_REQUEST['time_log'] : "0";
    $task_documents = $_REQUEST['task_documents'] ? $_REQUEST['task_documents'] : "0";
    $task_pages = $_REQUEST['task_pages'] ? $_REQUEST['task_pages'] : "0";

    // so foreach role/type lets put the values in the database
    foreach ($arr as $role => $types) {
        foreach ($types as $type => $value) {
            // is there a record for this user > taskgroup > role > type?
            $notify = TaskService::getInstance($w)->getTaskGroupUserNotifyType($_SESSION['user_id'], $p['id'], $role, $type);

            // if yes, update, if no, insert
            if ($notify) {
                $notify->value = $value;
                $notify->task_creation = $task_creation;
                $notify->task_details = $task_details;
                $notify->task_comments = $task_comments;
                $notify->time_log = $time_log;
                $notify->task_documents = $task_documents;
                $notify->task_pages = $task_pages;
                $notify->update();
            } else {
                $notify = new TaskGroupUserNotify($w);
                $notify->task_group_id = $p['id'];
                $notify->user_id = $_SESSION['user_id'];
                $notify->role = $role;
                $notify->type = $type;
                $notify->value = $value;
                $notify->task_creation = $task_creation;
                $notify->task_details = $task_details;
                $notify->task_comments = $task_comments;
                $notify->time_log = $time_log;
                $notify->task_documents = $task_documents;
                $notify->task_pages = $task_pages;
                $notify->insert();
            }
        }
    }

    // return
    $w->msg("Notifications Updated", "/task/tasklist/?taskgroups=" . $p['id'] . "&tab=2");
}

function updateusertasknotify_POST(Web &$w) {
    $p = $w->pathMatch("id");

    // set task event notify values
    $task_creation = $_REQUEST['task_creation'] ? $_REQUEST['task_creation'] : "0";
    $task_details = $_REQUEST['task_details'] ? $_REQUEST['task_details'] : "0";
    $task_comments = $_REQUEST['task_comments'] ? $_REQUEST['task_comments'] : "0";
    $time_log = $_REQUEST['time_log'] ? $_REQUEST['time_log'] : "0";
    $task_documents = $_REQUEST['task_documents'] ? $_REQUEST['task_documents'] : "0";
    $task_pages = $_REQUEST['task_pages'] ? $_REQUEST['task_pages'] : "0";

    // is there a record for this user > task?
    $notify = TaskService::getInstance($w)->getTaskUserNotify($_SESSION['user_id'], $p['id']);

    // if yes, update, if no, insert
    if ($notify) {
        $notify->task_creation = $task_creation;
        $notify->task_details = $task_details;
        $notify->task_comments = $task_comments;
        $notify->time_log = $time_log;
        $notify->task_documents = $task_documents;
        $notify->task_pages = $task_pages;
        $notify->update();
    } else {
        $notify = new TaskUserNotify($w);
        $notify->task_id = $p['id'];
        $notify->user_id = $_SESSION['user_id'];
        $notify->task_creation = $task_creation;
        $notify->task_details = $task_details;
        $notify->task_comments = $task_comments;
        $notify->time_log = $time_log;
        $notify->task_documents = $task_documents;
        $notify->task_pages = $task_pages;
        $notify->insert();
    }

    // return
    $w->msg("Notifications Updated", "/task/edit/" . $p['id'] . "#notification");
}

?>
