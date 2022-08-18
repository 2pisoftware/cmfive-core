<?php

//load form elements for required feilds
use \Html\Form\InputField as InputField;
use \Html\Form\Select as Select;
use \Html\Form\Autocomplete as Autocomplete;

function edit_GET($w)
{
    $p = $w->pathMatch("id");
    $task_checklist = (!empty($p["id"]) ? TaskService::getInstance($w)->getTask($p["id"]) : new TaskChecklist($w));

    if ($task_checklist->is_deleted == 1) {
        $w->error('Task checklist not found', "/task/tasklist/");
    }

    // Register for timelog if not new task
    if (!empty($task_checklist->id)) {
        TimelogService::getInstance($w)->registerTrackingObject($task_checklist);
    }

    if (!empty($task_checklist->id) && !$task_checklist->canView(AuthService::getInstance($w)->user())) {
        $w->error("You do not have permission to edit this Task checklist", "/task/tasklist");
    }


    $checklist_templates = ['null', 'blanck', 'undefined'];

    // $data = [];
    // $data['title'] = "hello";


    // $rowcontent = ' 
    //     <td id="ServiceDetailColumn"><input type="text" name="' . $data['title'] . '" id="' . $data['title'] . '" size="60""></td><br> 
    // ';

    // if(array_key_exists('addButton', $_POST)) {
    //     $rowcontent .= '<td id="ServiceDetailColumn"><input type="text" name="' . $data['title'] . '" id="' . $data['title'] . '" size="60""></td><br>';

    // }

    // $addButton = '<form method="post">
    //     <input type="submit" name="addButton"
    //             class="button" value="+" />
    // </form>';

    // $rowcontent .= $addButton;


    // Create form
    $hform = [
        (!empty($p["id"]) ? 'Edit task' : "Create a new task checklist") => [
            [
                ["Task Title", "text", "title", $task_checklist->title],
                (new Select([
                    "template" => "checklist_template"
                ]))->setLabel("Checklist Template")
                    ->setDisabled(!empty($p["id"]) ? "true" : null)
                    ->setOptions($checklist_templates)
                // ->setSelectedOption(!empty($p["id"]) ? $task->task_type : (is_array($tasktypes) && count($tasktypes) === 1 ? $tasktypes[0] : null))
            ],
            // !empty($p['id']) ? [["Task ID", "hidden", "task_id", $task->task_id]] : null
        ]

    ];


    // print_r($form['Create a new task checklist']['0']['1']);
    // var_dump($form['Create a new task checklist']['0']['1']);

    if (empty($p['id'])) {
        History::add("New Task Checklist");
    } else {
        History::add("Task Checklist: {$task_checklist->title}", null, $task_checklist);
    }

    $w->ctx("task_checklist", $task_checklist);
    $w->ctx("form", Html::multiColForm($hform, $w->localUrl("/task-checklist/edit/{$task_checklist->id}"), "POST", "Save", "edit_form", "prompt", null, "_self", true, Task::$_validation));

    $createdDate = '';
    if (!empty($task_checklist->id)) {
        $creator = $task_checklist->_modifiable->getCreator();
        $createdDate =  formatDate($task_checklist->_modifiable->getCreatedDate()) . (!empty($creator) ? ' by <strong>' . @$creator->getFullName() . '</strong>' : '');
    }
    $w->ctx('createdDate', $createdDate);

    ///////////////////
    // Notifications //
    ///////////////////

    // $notify = null;
    // // If I am assignee, creator or task group owner, I can get notifications for this task
    // if (!empty($task_checklist->id) && $task_checklist->getCanINotify()) {
    //     // get User set notifications for this Task
    //     $notify = TaskService::getInstance($w)->getTaskUserNotify(AuthService::getInstance($w)->user()->id, $task_checklist->id);
    //     if (empty($notify)) {
    //         $logged_in_user_id = AuthService::getInstance($w)->user()->id;
    //         // Get my role in this task group
    //         $me = TaskService::getInstance($w)->getMemberGroupById($task_checklist->task_group_id, $logged_in_user_id);

    //         $type = "";
    //         if ($task_checklist->assignee_id == $logged_in_user_id) {
    //             $type = "assignee";
    //         } elseif ($task_checklist->getTaskCreatorId() == $logged_in_user_id) {
    //             $type = "creator";
    //         } elseif (TaskService::getInstance($w)->getIsOwner($task_checklist->task_group_id, $logged_in_user_id)) {
    //             $type = "other";
    //         }

    //         if (!empty($type) && !empty($me)) {
    //             $notify = TaskService::getInstance($w)->getTaskGroupUserNotifyType($logged_in_user_id, $task_checklist->task_group_id, strtolower($me->role), $type);
    //         }
    //     }

    //     // create form. if still no 'notify' all boxes are unchecked
    //     $form = [
    //         "Notification Events" => [
    //             [
    //                 ["", "hidden", "task_creation", "0"]
    //             ],
    //             [
    //                 ["Task Details Update", "checkbox", "task_details", !empty($notify->task_details) ? $notify->task_details : null],
    //                 ["Comments Added", "checkbox", "task_comments", !empty($notify->task_comments) ? $notify->task_comments : null]
    //             ],
    //             [
    //                 ["Time Log Entry", "checkbox", "time_log", !empty($notify->time_log) ? $notify->time_log : null],
    //                 ["Task Data Updated", "checkbox", "task_data", !empty($notify->task_data) ? $notify->task_data : null]
    //             ],
    //             [
    //                 ["Documents Added", "checkbox", "task_documents", !empty($notify->task_documents) ? $notify->task_documents : null]
    //             ]
    //         ]
    //     ];

    //     $w->ctx("tasknotify", Html::multiColForm($form, $w->localUrl("/task/updateusertasknotify/" . $task_checklist->id), "POST"));

    ///////////////////
    // Top Banners   //
    ///////////////////

    $banners = $w->callHook('task_checklist', 'extra_messages', $task_checklist);
    // success , warning , info , alert , secondary
    // dismissable : true / false
    // eg: $banners[] = ["message" => "HELLO" , "dismiss" => true , "style" => "info"];

    $taskbanners = "";
    foreach ($banners ?? [] as $banner) {
        if (isset($banner["message"])) {
            $taskbanners .= "<div data-alert class='alert-box "
                . ($banner["style"] ?? "secondary") . "'>"
                . $banner["message"]
                . ((isset($banner["dismiss"]) && $banner["dismiss"]) ? "<a href='#' class='close'>&times;</a>" : "")
                . "</div>";
        }
    }
    $w->ctx("taskbanners", $taskbanners);
}

function edit_POST($w)
{
    $p = $w->pathMatch("id");
    $task_checklist = (!empty($p["id"]) ? TaskChecklistService::getInstance($w)->getTaskChecklist($p["id"]) : new TaskChecklist($w));
    $taskdata = null;
    if (!empty($p["id"])) {
        $taskdata = TaskChecklistService::getInstance($w)->getTaskChecklistData($p['id']);
    }

    // $task_checklist->fill($_POST['edit']);

    $task_checklist->insertOrUpdate(true);

    // Tell the template what the task id is (this post action is being called via ajax)
    $w->setLayout(null);
    $w->out($task_checklist->id);

    // Get existing task_data objects for this task and update them
    $existing_task_data = TaskChecklistService::getInstance($w)->getTaskChecklistData($p['id']);
    // if (!empty($existing_task_data)) {
    //     foreach ($existing_task_data as $e_task_data) {
    //         // Autocomplete fields
    //         if (strpos($e_task_data->data_key, \Html\Form\Autocomplete::$_prefix) === 0) {
    //             $e_task_data->delete();
    //             continue;
    //         }

    //         foreach ($_POST["extra"] as $key => $data) {
    //             if ($key == \CSRF::getTokenId()) {
    //                 unset($_POST["extra"][\CSRF::getTokenID()]);
    //                 continue;
    //             }

    //             if ($e_task_data->data_key == $key) {
    //                 $e_task_data->value = $data;
    //                 $e_task_data->update();

    //                 unset($_POST["extra"][$key]);
    //                 continue;
    //             }
    //         }
    //     }
    // }

    // Insert data that didn't exist above as new task_data objects
    // if (!empty($_POST["extra"])) {
    //     foreach ($_POST["extra"] as $key => $data) {
    //         if (strpos($key, \Html\Form\Autocomplete::$_prefix) !== 0) {
    //             $tdata = new TaskData($w);
    //             $tdata->task_id = $task_checklist->id;
    //             $tdata->data_key = $key;
    //             $tdata->value = $data;
    //             $tdata->insert();
    //         }
    //     }
    // }

    // if (empty($p['id']) && Config::get('task.ical.send') == true) {
    //     $data = $task->getIcal();
    //     $user = AuthService::getInstance($w)->getUser($task_checklist->assignee_id);
    //     $contact = !empty($user->id) ? $user->getContact() : AuthService::getInstance($w)->user()->getContact();

    //     $messageObject = new Swift_Message("Invite to: " . $task_checklist->title);
    //     $messageObject->setTo([$contact->email]);
    //     $messageObject->setReplyTo([AuthService::getInstance($w)->user()->getContact()->email])
    //     ->setFrom(Config::get("main.company_support_email"));

    //     $messageObject->addPart("Your iCal is attached<br/><br/><a href='http://www.google.com/calendar/event?action=TEMPLATE&text={$task->title}" .
    //         "&dates=" . date("Ymd", strtotime(str_replace('/', '-', $task_checklist->dt_due))) . "/" . date("Ymd", strtotime(str_replace('/', '-', $task_checklist->dt_due))) .
    //         "&details=" . htmlentities($task_checklist->description) .
    //         "&trp=false target='_blank' rel='nofollow'>Add to Google calendar</a><br/><br/>View the Task at: " . $task_checklist->toLink(null, null, $user), "text/html");

    //     $ics_content = $data;
    //     $messageObject->addPart($ics_content, "text/calendar");

    //     file_put_contents(FILE_ROOT . "invite.ics", $data);

    //     $ics_attachment = new Swift_Attachment(trim($ics_content), "invite.ics", "application/ics");
    //     $messageObject->attach($ics_attachment);

    //     $email_layer = Config::get('email.layer');
    //     $swiftmailer_transport = new SwiftMailerTransport($w, $email_layer);
    //     $mailObject = new Swift_Mailer($swiftmailer_transport->getTransport($email_layer));
    //     $mailObject->send($messageObject);

    //     unlink(FILE_ROOT . "invite.ics");
    // }
}
