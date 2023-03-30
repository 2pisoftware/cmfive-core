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

    $task_type = TaskService::getInstance($w)->getTaskTypeObject($object->task_type);
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
        "label" => "Task time",
        "required" => $required,
    ]))->setOptions($time_types, true)->setSelectedOption($object->time_type)];
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
    LogService::getInstance($w)->setLogger("TASK")->debug("task_core_dbobject_after_insert_Task");
    $task->addTaskGroupAsSubscribers();
    if (!$task->_skip_creation_notification) {
        TaskService::getInstance($w)->sendCreationNotificationForTask($task);
    } else {
        LogService::getInstance($w)->setLogger("TASK")->debug("Task creation notification skipped because _skip_creation_notification was set on the task object");
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
    LogService::getInstance($w)->setLogger("TASK")->debug("task_core_dbobject_after_update_Task");
    $subject = "Task " . $task->title . " [" . $task->id . "][" . $task->status . "] - " . $task->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DETAILS);
    $users_to_notify = TaskService::getInstance($w)->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DETAILS);

    // Only send emails where the status has changed
    if ($task->status == $task->__old['status']) {
        return;
    }

    NotificationService::getInstance($w)->sendToAllWithCallback($subject, "task", "notification_email", AuthService::getInstance($w)->user(), $users_to_notify, function ($user, $existing_template_data) use ($task, $w) {
        if ($user->is_external) {
            return null;
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
            $additional_details = TaskService::getInstance($w)->getNotificationAdditionalDetails($task);
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
        return new NotificationCallback($user, $template_data, FileService::getInstance($w)->getAttachmentsFileList($task, null, ['channel_email_raw']));
    });
}

function task_task_subscriber_notification(Web $w, $params)
{
    $task = TaskService::getInstance($w)->getTask($params["task_id"]);
    $user = AuthService::getInstance($w)->getUser($params["user_id"]);

    TaskService::getInstance($w)->sendSubscribeNotificationForTask($task, $user);
}

function task_attachment_attachment_added_task(Web $w, $attachment)
{
    LogService::getInstance($w)->setLogger("TASK")->debug("task_attachment_attachment_added_task");
    if (!$attachment->_skip_added_notification) {
        $task = TaskService::getInstance($w)->getTask($attachment->parent_id);

        if (empty($task->id)) {
            return;
        }

        $users_to_notify = TaskService::getInstance($w)->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DOCUMENTS);
        $subject = "Task - " . $task->title . ' [' . $task->id . ']: ' . $attachment->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DOCUMENTS);

        NotificationService::getInstance($w)->sendToAllWithCallback($subject, "task", "notification_email", AuthService::getInstance($w)->user(), $users_to_notify, function ($user, $existing_template_data) use ($task, $w) {
            if ($user->is_external) {
                return null;
            }

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
                $additional_details = TaskService::getInstance($w)->getNotificationAdditionalDetails($task);
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
            return new NotificationCallback($user, $template_data, FileService::getInstance($w)->getAttachmentsFileList($task, null, ['channel_email_raw']));
        });
    } else {
        LogService::getInstance($w)->setLogger("TASK")->debug("Task Attachment added notification skipped because _skip_added_notification was set on the attachment object");
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

    $task = TaskService::getInstance($w)->getTask($params['object_id']);
    if (!empty($task)) {
        $subscribers = $task->getSubscribers();

        //add subscribers to users array
        if (!empty($subscribers)) {
            foreach ($subscribers as $subscriber) {
                //check if subscriber is active
                if (!empty($subscriber->user_id)) {
                    $user = $subscriber->getUser();
                    if ($internal_only === false || $internal_only === true && $user->is_external == 0) {
                        $results[$subscriber->user_id] = (AuthService::getInstance($w)->user()->id != $subscriber->user_id);
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

    $task = TaskService::getInstance($w)->getTask($params['object_id']);
    $commenter = AuthService::getInstance($w)->getUser($params['commenter_id']);

    // make sure that people who comment become subscribers
    $task->addSubscriber($commenter);

    $subject = (!empty($commenter->id) ? $commenter->getFullName() : 'Someone') . ' has commented on a task that you\'re a part of (' . $task->title . ' [' . $task->id . '])';

    NotificationService::getInstance($w)->sendToAllWithCallback($subject, "task", "notification_email", AuthService::getInstance($w)->getUser($params['commenter_id']), $params['recipients'], function ($user, $existing_template_data) use ($params, $task, $w) {
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

        $template_data['footer'] .= $w->partial("displaycomment", ["object" => $params['comment'], "displayOnly" => true, 'redirect' => '/inbox', "is_outgoing" => true], "admin");

        // Get additional details
        if ($user->is_external == 0) {
            $additional_details = TaskService::getInstance($w)->getNotificationAdditionalDetails($task);
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
        return new NotificationCallback($user, $template_data, FileService::getInstance($w)->getAttachmentsFileList($task, null, ['channel_email_raw']));
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

    //Remove any subscribed users if they no longer have the sufficient view task permissions
    $members = $object->getMembers();
    foreach ($members as $member) {
        $user = AuthService::getInstance($w)->getUser($member->user_id);
        $tasks = $object->getTasks();
        foreach ($tasks as $task) {
            //Check if user is subscribed to the task & can no longer view it
            if ($task->isUserSubscribed($user->id) && !$task->canView($user)) {
                //If so, remove subscription
                TaskService::getInstance($w)->getSubscriberForUserAndTask($user->id, $task->id)->delete();
            }
        }
    }


}
