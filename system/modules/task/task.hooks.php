<?php

// Different task notification events as defined in the database
define('TASK_NOTIFICATION_TASK_CREATION', 'task_creation');
define('TASK_NOTIFICATION_TASK_DETAILS', 'task_details');
define('TASK_NOTIFICATION_TASK_COMMENTS', 'task_comments');
define('TASK_NOTIFICATION_TIME_LOG', 'time_log');
define('TASK_NOTIFICATION_TASK_DOCUMENTS', 'task_documents');

/**
 * Add custom time type object to timelogs
 *
 * @param Web $w
 * @param Task $object
 */
function task_timelog_type_options_for_Task(Web $w, $object)
{
    if (empty($object)) {
        return;
    }

    $task_type = $w->Task->getTaskTypeObject($object->task_type);
    if (empty($task_type)) {
        return;
    }

    $time_types = $task_type->getTimeTypes();
    if (empty($time_types)) {
        return;
    }

    $required = null;
    if (!empty(Timelog::$_validation["time_type"])) {
        if (in_array("required", Timelog::$_validation["time_type"])) {
            $required = "required";
        }
    }

    return [(new \Html\Form\Select([
        "name" => "time_type",
        "id" => "time_type",
        "options" => $time_types,
        "label" => "Task time",
        "required" => $required,
    ]))->setSelectedOption($object->time_type)];
}

/**
 * Hook to notify relevant people when a task has been created
 *
 * Task template email parameters:
 * message
 *        status = "A new task has been created"
 *        action_url = "http://github.com/2pisoftware/cmfive"
 *        footer = "Task description"
 * fields[]
 *        key
 *        value
 *
 *
 * @param Web $w
 * @param Task $task
 */
function task_core_dbobject_after_insert_Task(Web $w, $task)
{
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_insert_Task");
    if (!$task->_skip_creation_notification) {
        $w->Task->sendCreationNotificationForTask($task);
    } else {
        $w->Log->setLogger("TASK")->debug("Task creation notification skipped because _skip_creation_notification was set on the task object");
    }
}

/**
 * Hook to notify relevant people when a task has been updated
 *
 * @param Web $w
 * @param Task $task
 */
function task_core_dbobject_after_update_Task(Web $w, $task)
{
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_update_Task");
    $subject = "Task " . $task->title . " [" . $task->id . "][" . $task->status . "] - " . $task->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DETAILS);
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DETAILS);

    // Only send emails where the status has changed
    if ($task->status == $task->__old['status']) {
        return;
    }

    $w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->Auth->user(), $users_to_notify, function ($user, $existing_template_data) use ($task, $w) {
        if ($user->is_external) {
            return false;
        }

        $template_data = $existing_template_data;
        $template_data['status'] = "[{$task->id}] Status change";
        $template_data['footer'] = $task->description;
        $template_data['action_url'] = $w->localUrl('/task/edit/' . $task->id);
        $template_data['logo_url'] = defaultVal(Config::get('main.application_logo'), '');

        $template_data['fields'] = [
            "Assigned to" => !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
            "Type" => $task->getTypeTitle(),
            "Title" => $task->title,
            "Due" => !empty($task->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $task->dt_due))) : '',
            "Status" => '<b>' . $task->__old['status'] . ' => ' . $task->status . '</b>',
            "Priority" => $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority,
        ];

        $template_data['can_view_task'] = $user->is_external == 0;

        // Get additional details
        if ($user->is_external == 0) {
            $additional_details = $w->Task->getNotificationAdditionalDetails($task);
            if (!empty($additional_details)) {
                $template_data['footer'] .= $additional_details;
            }
        }

        if (!empty($task->assignee_id)) {
            if ($user->id == $task->assignee_id) {
                $template_data['fields']["Assigned to"] = "You (" . $task->getAssignee()->getFullName() . ")";
            } else {
                $template_data['fields']["Assigned to"] = !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '';
            }
        } else {
            $template_data['fields']["Assigned to"] = "No one";
        }
        return new NotificationCallback($user, $template_data, $w->file->getAttachmentsFileList($task, null, ['channel_email_raw']));
    });
}

function task_attachment_attachment_added_task(Web $w, $attachment)
{
    $w->Log->setLogger("TASK")->debug("task_attachment_attachment_added_task");
    if (!$attachment->_skip_added_notification) {
        $task = $w->Task->getTask($attachment->parent_id);

        if (empty($task->id)) {
            return;
        }

        $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DOCUMENTS);
        $subject = "Task - " . $task->title . ' [' . $task->id . ']: ' . $attachment->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DOCUMENTS);

        $w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->Auth->user(), $users_to_notify, function ($user, $existing_template_data) use ($task, $w) {

            $template_data = $existing_template_data;
            $template_data['status'] = "[{$task->id}] New attachment";
            $template_data['footer'] = $task->description;
            $template_data['action_url'] = $w->localUrl('/task/edit/' . $task->id);
            $template_data['logo_url'] = defaultVal(Config::get('main.application_logo'), '');

            $template_data['fields'] = [
                "Assigned to" => !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
                "Type" => $task->getTypeTitle(),
                "Title" => $task->title,
                "Due" => !empty($task->dt_due) ? date('d-m-Y', !is_numeric($task->dt_due) ? strtotime(str_replace('/', '-', $task->dt_due)) : $task->dt_due) : '',
                "Status" => $task->status,
                "Priority" => $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority,
            ];

            if ($user->is_external) {
                $template_data['fields']['Due'] = '';
                $template_data['fields']['Priority'] = '';
                $template_data['fields']['Status'] = '';
            }

            $template_data['can_view_task'] = $user->is_external == 0;

            // Get additional details
            if ($user->is_external == 0) {
                $additional_details = $w->Task->getNotificationAdditionalDetails($task);
                if (!empty($additional_details)) {
                    $template_data['footer'] .= $additional_details;
                }
            }

            if (!empty($task->assignee_id)) {
                if ($user->id == $task->assignee_id) {
                    $template_data['fields']["Assigned to"] = "You (" . $task->getAssignee()->getFullName() . ")";
                } else {
                    $template_data['fields']["Assigned to"] = !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '';
                }
            } else {
                $template_data['fields']["Assigned to"] = "No one";
            }
            return new NotificationCallback($user, $template_data, $w->file->getAttachmentsFileList($task, null, ['channel_email_raw']));
        });
    } else {
        $w->Log->setLogger("TASK")->debug("Task Attachment added notification skipped because _skip_added_notification was set on the attachment object");
    }
}

// Admin user remove hook
function task_admin_remove_user(Web $w, User $user)
{
    return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "task");
}

/*
 * Sends a list of potential notification receivers to the comment partial
 * must return array in this format:
 * array = [
 *      'user_id'=>boolean (sets the default option for user. i.e. are they to receive notifications by default)
 * ]
 */
function task_comment_get_notification_recipients_task(Web $w, $params)
{
    $results = [];
    $internal_only = array_key_exists('internal_only', $params) ? $params['internal_only'] : false;

    $task = $w->task->getTask($params['object_id']);
    if (!empty($task)) {
        $subscribers = $task->getSubscribers();

        //add subscribers to users array
        if (!empty($subscribers)) {
            foreach ($subscribers as $subscriber) {
                //check if subscriber is active
                if (!empty($subscriber->user_id)) {
                    $user = $subscriber->getUser();
                    if ($internal_only === false || $internal_only === true && $user->is_external == 0) {
                        $results[$subscriber->user_id] = ($w->Auth->user()->id != $subscriber->user_id);
                    }
                }
            }
        }
        return $results;
    }
    return false;
}

/*
 * Receives parameters for users to notify from comments
 * $params = array(
 *      'recipients'=>['user_ids'],
 *      'commenter_id=>int (user_id of comment author),
 *      'object_id'=>int (id of object comment is attached to. class included in funtion name),
 *      'comment'=>comment Object
 * )
 */
function task_comment_send_notification_recipients_task(Web $w, $params)
{

    $task = $w->task->getTask($params['object_id']);
    $commenter = $w->Auth->getUser($params['commenter_id']);

    // make sure that people who comment become subscribers
    $task->addSubscriber($commenter);

    $subject = (!empty($commenter->id) ? $commenter->getFullName() : 'Someone') . ' has commented on a task that you\'re a part of (' . $task->title . ' [' . $task->id . '])';

    $w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->auth->getUser($params['commenter_id']), $params['recipients'], function ($user, $existing_template_data) use ($params, $task, $w) {
        $template_data = $existing_template_data;
        $template_data['status'] = "[{$task->id}] New comment";
        $template_data['footer'] = $task->description;
        $template_data['action_url'] = $w->localUrl('/task/edit/' . $task->id);
        $template_data['logo_url'] = defaultVal(Config::get('main.application_logo'), '');

        $template_data['fields'] = [
            "Assigned to" => !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
            "Type" => $task->getTypeTitle(),
            "Title" => $task->title,
            "Due" => !empty($task->dt_due) ? date('d-m-Y', !is_numeric($task->dt_due) ? strtotime(str_replace('/', '-', $task->dt_due)) : $task->dt_due) : '',
            "Status" => $task->status,
            "Priority" => $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority,
        ];

        if ($user->is_external) {
            $template_data['fields']['Due'] = '';
            $template_data['fields']['Priority'] = '';
            $template_data['fields']['Status'] = '';
        }

        $template_data['can_view_task'] = $user->is_external ? false : true;

        $template_data['footer'] .= $w->partial("displaycomment", array("object" => $params['comment'], "displayOnly" => true, 'redirect' => '/inbox', "is_outgoing" => true), "admin");

        // Get additional details
        if ($user->is_external == 0) {
            $additional_details = $w->Task->getNotificationAdditionalDetails($task);
            if (!empty($additional_details)) {
                $template_data['footer'] .= $additional_details;
            }
        }

        if (!empty($task->assignee_id)) {
            if ($user->id == $task->assignee_id) {
                $template_data['fields']["Assigned to"] = "You (" . $task->getAssignee()->getFullName() . ")";
            } else {
                $template_data['fields']["Assigned to"] = !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '';
            }
        } else {
            $template_data['fields']["Assigned to"] = "No one";
        }
        return new NotificationCallback($user, $template_data, $w->file->getAttachmentsFileList($task, null, ['channel_email_raw']));
    });
}

// if taskgroup has been made inactive, then deactivate all attached tasks
function task_core_dbobject_after_update_TaskGroup(Web $w, $object)
{
    // check if is_active flag has changed from 1 to 0
    if (isset($object->__old['is_active']) && $object->__old["is_active"] !== $object->is_active) {
        // get all attached tasks
        $tasks = $object->getTasks();
        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $task->is_active = $object->is_active;
                $task->update();
            }
        }
    }
}
