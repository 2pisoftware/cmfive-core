<?php

//load form elements for required feilds
use Html\Cmfive\QuillEditor;
use Html\Form\Html5Autocomplete;
use \Html\Form\InputField as InputField;
use \Html\Form\Select as Select;

function edit_GET($w)
{
    $p = $w->pathMatch("id");
    $task = (!empty($p["id"]) ? TaskService::getInstance($w)->getTask($p["id"]) : new Task($w));

    if ($task->is_deleted == 1) {
        $w->error('Task not found', "/task/tasklist/");
    }

    // Register for timelog if not new task
    if (!empty($task->id)) {
        TimelogService::getInstance($w)->registerTrackingObject($task);
    }

    if (!empty($task->id) && !$task->canView(AuthService::getInstance($w)->user())) {
        $w->error("You do not have permission to edit this Task", "/task/tasklist");
    }

    // Get a list of the taskgroups and filter by what can be used
    $taskgroup_list = TaskService::getInstance($w)->getTaskGroups();
    if (empty($taskgroup_list)) {
        if ((new Taskgroup($w))->canEdit(AuthService::getInstance($w)->user())) {
            $w->msg('Please set up a taskgroup before continuing', '/task-group/viewtaskgrouptypes');
        } else {
            $w->error('There are no Tasks currently set up, please notify an Administrator', '/task');
        }
    }

    $taskgroups = array_filter($taskgroup_list, function ($taskgroup) {
        return $taskgroup->getCanICreate();
    });

    $tasktypes = [];
    $priority = [];
    $members = [];

    // Try and prefetch the taskgroup by given id
    $taskgroup = null;
    $taskgroup_id = Request::int("gid");
    // $assigned = 0;
    if (!empty($taskgroup_id) || !empty($task->task_group_id)) {
        $taskgroup = TaskService::getInstance($w)->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);

        if (!empty($taskgroup->id)) {
            $tasktypes = TaskService::getInstance($w)->getTaskTypes($taskgroup->task_group_type);
            $priority = TaskService::getInstance($w)->getTaskPriority($taskgroup->task_group_type);
            $members = TaskService::getInstance($w)->getMembersBeAssigned($taskgroup->id);
            sort($members);
            array_unshift($members, ["Unassigned", "0"]);
            // $assigned = (empty($task->assignee_id)) ? "unassigned" : $task->assignee_id;
        }
    }

    // Create form
    $form = [
        (!empty($p["id"]) ? 'Edit task' : "Create a new task") => [
            [
                new Html5Autocomplete([
                    "label" => "Task Group",
                    "id|name" => "task_group",
                    "required" => "required",
                    "value" => !empty($taskgroup) ? $taskgroup->id : null,
                    "disabled" => !empty($p["id"]) ? "true" : null,
                    "options" => $taskgroups,
                    "maxItems" => 1
                ]),

                (new Select([
                    "id|name" => "task_type",
                    "label" => "Task Type",
                    "disabled" => !empty($p["id"]) ? "true" : null,
                    "options" => $tasktypes,
                    "required" => "required",
                ]))->setSelectedOption(!empty($p["id"]) ? $task->task_type : (is_array($tasktypes) && count($tasktypes) === 1 ? $tasktypes[0] : null))
            ],
            [
                (new InputField([
                    "label" => "Task Title",
                    "id|name" => "title",
                    "value" => $task->title,
                    "required" => "required"
                ])),

                (new Select([
                    "label" => "Status",
                    "id|name" => "status",
                    "value" => $task->status,
                    "options" => $task?->getTaskGroupStatus(),
                    "required" => "required"
                ]))->setSelectedOption($task->status),
            ],
            [
                (new Select([
                    "label" => "Priority",
                    "id|name" => "priority",
                    "value" => $task->priority,
                    "options" => $priority,
                ]))->setSelectedOption($task->priority),

                (new InputField\Date([
                    "label" => "Date Due",
                    "id|name" => "dt_due",
                    "value" => formatDate($task->dt_due, "Y-m-d")
                ])),

                (new Select([
                    "id|name" => "assignee_id",
                    "label" => "Assigned To",
                    "options" => $members,
                    "disabled" => !empty($taskgroup) && $taskgroup->getCanIAssign() ? null : "disabled",
                    "required" => "required",
                    "value" => $task->assignee_id,
                ]))->setSelectedOption($task->assignee_id),
            ],
            [
                new InputField([
                    "id|name" => "estimate_hours",
                    "label" => "Estimated Hours",
                    "value" => $task->estimate_hours,
                ]),
                new InputField([
                    "id|name" => "effort",
                    "label" => "Effort",
                    "value" => $task->effort,
                ]),
            ],
            [
                new QuillEditor([
                    "label" => "Description",
                    "id|name" => "description",
                    "value" => $task->description,
                ])
            ],
            !empty($p['id']) ? [["Task Group ID", "hidden", "task_group_id", $task->task_group_id]] : null
        ]
    ];

    if (empty($p['id'])) {
        History::add("New Task");
    } else {
        History::add("Task: {$task->title}", null, $task);
    }

    //add task rate
    if (!empty($task->id) && $task->canISetRate()) {
        $form['Edit task'][3][] = (new InputField())->setName('rate')->setLabel('Rate ($)')->setValue($task->rate)->setPattern('^\d+(?:\.\d{1,2})?$')->setPlaceholder('0.00');
    }

    $w->ctx("task", $task);
    $w->ctx("form", HtmlBootstrap5::multiColForm($form, $w->localUrl("/task/edit/{$task->id}"), "POST", "Save", "edit_form", "prompt", null, "_self", true, Task::$_validation));

    $createdDate = '';
    if (!empty($task->id)) {
        $creator = $task->_modifiable->getCreator();
        $createdDate = formatDate($task->_modifiable->getCreatedDate()) . (!empty($creator) ? ' by <strong>' . @$creator->getFullName() . '</strong>' : '');
    }
    $w->ctx('createdDate', $createdDate);

    $w->ctx("timelog_count", TimelogService::getInstance($w)->countTimelogsForObject($task));
    $w->ctx("internal_comments_count", CommentService::getInstance($w)->countCommentsForTable("task", $task->id, true));
    $w->ctx("external_comments_count", CommentService::getInstance($w)->countCommentsForTable("task", $task->id, false, true));

    // Subscribers
    if (!empty($task->id)) {
        $task_subscribers = $task->getSubscribers();

        $w->ctx('subscribers', $task_subscribers);
    }

    ///////////////////
    // Notifications //
    ///////////////////

    $notify = null;
    // If I am assignee, creator or task group owner, I can get notifications for this task
    if (!empty($task->id) && $task->getCanINotify()) {
        // get User set notifications for this Task
        $notify = TaskService::getInstance($w)->getTaskUserNotify(AuthService::getInstance($w)->user()->id, $task->id);
        if (empty($notify)) {
            $logged_in_user_id = AuthService::getInstance($w)->user()->id;
            // Get my role in this task group
            $me = TaskService::getInstance($w)->getMemberGroupById($task->task_group_id, $logged_in_user_id);

            $type = "";
            if ($task->assignee_id == $logged_in_user_id) {
                $type = "assignee";
            } elseif ($task->getTaskCreatorId() == $logged_in_user_id) {
                $type = "creator";
            } elseif (TaskService::getInstance($w)->getIsOwner($task->task_group_id, $logged_in_user_id)) {
                $type = "other";
            }

            if (!empty($type) && !empty($me)) {
                $notify = TaskService::getInstance($w)->getTaskGroupUserNotifyType($logged_in_user_id, $task->task_group_id, strtolower($me->role), $type);
            }
        }

        // create form. if still no 'notify' all boxes are unchecked
        $form = [
            "Notification Events" => [
                [
                    ["", "hidden", "task_creation", "0"]
                ],
                [
                    ["Task Details Update", "checkbox", "task_details", !empty($notify->task_details) ? $notify->task_details : null],
                    ["Comments Added", "checkbox", "task_comments", !empty($notify->task_comments) ? $notify->task_comments : null]
                ],
                [
                    ["Time Log Entry", "checkbox", "time_log", !empty($notify->time_log) ? $notify->time_log : null],
                    ["Task Data Updated", "checkbox", "task_data", !empty($notify->task_data) ? $notify->task_data : null]
                ],
                [
                    ["Documents Added", "checkbox", "task_documents", !empty($notify->task_documents) ? $notify->task_documents : null]
                ]
            ]
        ];

        $w->ctx("tasknotify", Html::multiColForm($form, $w->localUrl("/task/updateusertasknotify/" . $task->id), "POST"));
    }

    ///////////////////
    // Top Banners   //
    ///////////////////

    $banners = $w->callHook('task', 'extra_messages', $task);
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
    $w->setLayout(null);
    list($task_id) = $w->pathMatch("id");
    $task = (!empty($task_id) ? TaskService::getInstance($w)->getTask($task_id) : new Task($w));
    $edit_array = Request::array('edit');
    $task->fill($edit_array);

    $task->assignee_id = intval($edit_array['assignee_id']);

    if (empty($task->dt_due)) {
        $task->dt_due = TaskService::getInstance($w)->getNextMonth();
    }
    $task->estimate_hours = !empty($task->estimate_hours) ? $task->estimate_hours : null;
    $task->effort = !empty($task->effort) ? floatval($task->effort) : null;
    $task->rate = !empty($task->rate) ? $task->rate : null;
    $task->insertOrUpdate(true);

    // Tell the template what the task id is (this post action is being called via ajax)
    $w->out($task->id);

    // Get existing task_data objects for this task and update them
    $existing_task_data = TaskService::getInstance($w)->getTaskData($task->id);
    if (!empty($existing_task_data)) {
        foreach ($existing_task_data as $e_task_data) {
            // Autocomplete fields
            if (strpos($e_task_data->data_key, \Html\Form\Autocomplete::$_prefix) === 0) {
                $e_task_data->delete();
                continue;
            }

            foreach ($_POST["extra"] as $key => $data) {
                if ($key == \CSRF::getTokenId()) {
                    unset($_POST["extra"][\CSRF::getTokenID()]);
                    continue;
                }

                if ($e_task_data->data_key == $key) {
                    $e_task_data->value = $data;
                    $e_task_data->update();

                    unset($_POST["extra"][$key]);
                    continue;
                }
            }
        }
    }

    // Insert data that didn't exist above as new task_data objects
    if (!empty($_POST["extra"])) {
        foreach ($_POST["extra"] as $key => $data) {
            if (strpos($key, \Html\Form\Autocomplete::$_prefix) !== 0) {
                $tdata = new TaskData($w);
                $tdata->task_id = $task->id;
                $tdata->data_key = $key;
                $tdata->value = $data;
                $tdata->insert();
            }
        }
    }

    if (empty($task_id) && Config::get('task.ical.send') == true) {
        $data = $task->getIcal();
        $user = AuthService::getInstance($w)->getUser($task->assignee_id);
        $contact = !empty($user->id) ? $user->getContact() : AuthService::getInstance($w)->user()->getContact();

        $messageObject = new Swift_Message("Invite to: " . $task->title);
        $messageObject->setTo([$contact->email]);
        $messageObject->setReplyTo([AuthService::getInstance($w)->user()->getContact()->email])
            ->setFrom(Config::get("main.company_support_email"));

        $messageObject->addPart("Your iCal is attached<br/><br/><a href='http://www.google.com/calendar/event?action=TEMPLATE&text={$task->title}" .
            "&dates=" . date("Ymd", strtotime(str_replace('/', '-', $task->dt_due))) . "/" . date("Ymd", strtotime(str_replace('/', '-', $task->dt_due))) .
            "&details=" . htmlentities($task->description) .
            "&trp=false target='_blank' rel='nofollow'>Add to Google calendar</a><br/><br/>View the Task at: " . $task->toLink(null, null, $user), "text/html");

        $ics_content = $data;
        $messageObject->addPart($ics_content, "text/calendar");

        file_put_contents(FILE_ROOT . "invite.ics", $data);

        $ics_attachment = new Swift_Attachment(trim($ics_content), "invite.ics", "application/ics");
        $messageObject->attach($ics_attachment);

        $email_layer = Config::get('email.layer');
        $swiftmailer_transport = new SwiftMailerTransport($w, $email_layer);
        $mailObject = new Swift_Mailer($swiftmailer_transport->getTransport($email_layer));
        $mailObject->send($messageObject);

        unlink(FILE_ROOT . "invite.ics");
    }
}
