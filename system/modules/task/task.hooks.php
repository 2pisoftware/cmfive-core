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
function task_timelog_type_options_for_Task(Web $w, $object) {
	if (!empty($object)) {
		$task_type = $w->Task->getTaskTypeObject($object->task_type);
		$time_types = $task_type->getTimeTypes();
		
		$required = null;
		if (!empty(Timelog::$_validation["time_type"])) {
			if (in_array("required", Timelog::$_validation["time_type"])) {
				$required = "required";
			} 
		}
		
		if (!empty($time_types)) {
			return [(new \Html\Form\Select([
				"name" => "time_type",
				"id" => "time_type",
				"options" => $time_types,
				"label" => "Task time",
				"required" => $required
			]))->setSelectedOption($object->time_type)]; //[["Task time", "select", "time_type", $object->time_type, $time_types]];
		}
	}
}

/**
 * Hook to notify relevant people when a task has been created
 * 
 * Task template email parameters:
 * message
 *		status = "A new task has been created"
 *		action_url = "http://github.com/2pisoftware/cmfive"
 *		footer = "Task description"
 * fields[]
 *		key
 *		value
 *	
 * 
 * @param Web $w
 * @param Task $object
 */
function task_core_dbobject_after_insert_Task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_insert_Task");
    
    $subject = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_CREATION) . "[" . $object->id . "]: " . $object->title;
    $users_to_notify = $w->Task->getNotifyUsersForTask($object, TASK_NOTIFICATION_TASK_CREATION);

    $w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->Auth->user(), $users_to_notify, function($user, $existing_template_data) use ($object, $w) {
    	$template_data = $existing_template_data;
		$template_data['status']		= "[{$object->id}] New task created";
		$template_data['footer']		= $object->description;
		$template_data['action_url']	= $w->localUrl('/task/edit/' . $object->id);
		$template_data['logo_url']		= Config::get('main.application_logo');

		$w->Log->debug("Logo: " . $template_data['logo_url']);

		$template_data['fields'] = [
			"Assigned to"	=> !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '',
			"Type"			=> $object->getTypeTitle(),
			"Title"			=> $object->title,
			"Taskgroup"		=> $object->getTaskGroup()->title,
			"Due"			=> !empty($object->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $object->dt_due))) : '',
			"Status"		=> $object->status,
			"Priority"		=> $object->isUrgent() ? "<b style='color: orange;'>{$object->priority}</b>" : $object->priority
		];

		$template_data['can_view_task'] = $user->is_external == 0;
		
		// Get additional details
		if ($user->is_external == 0) {
			$additional_details = $w->Task->getNotificationAdditionalDetails($object);
			if (!empty($additional_details)) {
				$template_data['footer'] .= $additional_details;
			}
		}

		if (!empty($object->assignee_id)) {
			if ($user->id == $object->assignee_id) {
				$template_data['fields']["Assigned to"] = "You (" . $object->getAssignee()->getFullName() . ")";
			} else {
				$template_data['fields']["Assigned to"] = !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '';
			}
		} else {
			$template_data['fields']["Assigned to"] = "No one";
		}

		return new NotificationCallback($user, $template_data, $w->File->getAttachmentsFileList($object));
    });
}

/**
 * Hook to notify relevant people when a task has been update
 * 
 * @param Web $w
 * @param Task $object
 */
function task_core_dbobject_after_update_Task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_update_Task");
    
    $subject = "Task " . $object->title . " [" . $object->id . "][" . $object->status . "] - " . $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DETAILS);
    $users_to_notify = $w->Task->getNotifyUsersForTask($object, TASK_NOTIFICATION_TASK_DETAILS);
    
	// Only send emails where the status has changed
	if ($object->status == $object->__old['status']) {
		return;
	}

    $w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->Auth->user(), $users_to_notify, function($user, $existing_template_data) use ($object, $w) {
    	$template_data = $existing_template_data;
		$template_data['status']		= "[{$object->id}] Status change";
		$template_data['footer']		= $object->description;
		$template_data['action_url']	= $w->localUrl('/task/edit/' . $object->id);
		$template_data['logo_url']		= defaultVal(Config::get('main.application_logo'), '');

		$template_data['fields'] = [
			"Assigned to"	=> !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '',
			"Type"			=> $object->getTypeTitle(),
			"Title"			=> $object->title,
			"Taskgroup"		=> $object->getTaskGroup()->title,
			"Due"			=> !empty($object->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $object->dt_due))) : '',
			"Status"		=> '<b>' . $object->__old['status'] . ' => ' . $object->status . '</b>',
			"Priority"		=> $object->isUrgent() ? "<b style='color: orange;'>{$object->priority}</b>" : $object->priority
		];

		$template_data['can_view_task'] = $user->is_external == 0;
		
		// Get additional details
		if ($user->is_external == 0) {
			$additional_details = $w->Task->getNotificationAdditionalDetails($object);
			if (!empty($additional_details)) {
				$template_data['footer'] .= $additional_details;
			}
		}

		if (!empty($object->assignee_id)) {
			if ($user->id == $object->assignee_id) {
				$template_data['fields']["Assigned to"] = "You (" . $object->getAssignee()->getFullName() . ")";
			} else {
				$template_data['fields']["Assigned to"] = !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '';
			}
		} else {
			$template_data['fields']["Assigned to"] = "No one";
		}

		return new NotificationCallback($user, $template_data, $w->File->getAttachmentsFileList($object));
    });
}

function task_attachment_attachment_added_task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_attachment_attachment_added_task");
    
    $task = $w->Task->getTask($object->parent_id);
    
    if (empty($task->id)) {
        return;
    }

    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DOCUMENTS);
    $subject = "Task - " . $task->title . ' [' . $task->id . ']: ' . $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DOCUMENTS);

    $w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->Auth->user(), $users_to_notify, function($user, $existing_template_data) use ($task, $w) {
    	$template_data = $existing_template_data;
		$template_data['status']		= "[{$task->id}] New attachment";
		$template_data['footer']		= $task->description;
		$template_data['action_url']	= $w->localUrl('/task/edit/' . $task->id);
		$template_data['logo_url']		= defaultVal(Config::get('main.application_logo'), '');

		$template_data['fields'] = [
			"Assigned to"	=> !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
			"Type"			=> $task->getTypeTitle(),
			"Title"			=> $task->title,
			"Taskgroup"		=> $task->getTaskGroup()->title,
			"Due"			=> !empty($task->dt_due) ? date('d-m-Y', !is_numeric($task->dt_due) ? strtotime(str_replace('/', '-', $task->dt_due)) : $task->dt_due) : '',
			"Status"		=> $task->status,
			"Priority"		=> $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority
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

		return new NotificationCallback($user, $template_data, $w->File->getAttachmentsFileList($task));
    });
}

// Admin user remove hook
function task_admin_remove_user(Web $w, User $user) {
	return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "task");
}

/*
 * Sends a list of potential notification receivers to the comment partial
 * must return array in this format:
 * array = [
 *      'user_id'=>boolean (sets the default option for user. i.e. are they to receive notifications by default)
 * ]
 */
function task_comment_get_notification_recipients_task(Web $w, $params) {
    $results = [];
    $internal_only = array_key_exists('internal_only', $params) ? $params['internal_only'] : false;

    $task = $w->task->getTask($params['object_id']);
    if (!empty($task)) {
        $subscribers = $task->getSubscribers();

        //add subscribers to users array
        if (!empty($subscribers)) {
            foreach($subscribers as $subscriber) {
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
 *      'commentor_id=>int (user_id of comment author),
 *      'object_id'=>int (id of object comment is attached to. class included in funtion name),
 *      'comment'=>comment Object
 * )
 */
function task_comment_send_notification_recipients_task(Web $w, $params) {
    
    $task = $w->task->getTask($params['object_id']);
	$subject = (!empty($commentor->id) ? $commentor->getFullName() : 'Someone') . ' has commented on a task that you\'re a part of ('.$task->title . ' [' . $task->id . '])';

	$w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $w->auth->getUser($params['commentor_id']), $params['recipients'], function($user, $existing_template_data) use ($params, $task, $w) {
    	$template_data = $existing_template_data;
		$template_data['status']		= "[{$task->id}] New comment";
		$template_data['footer']		= $task->description;
		$template_data['action_url']	= $w->localUrl('/task/edit/' . $task->id);
		$template_data['logo_url']		= defaultVal(Config::get('main.application_logo'), '');

		$template_data['fields'] = [
			"Assigned to"	=> !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
			"Type"			=> $task->getTypeTitle(),
			"Title"			=> $task->title,
			"Taskgroup"		=> $task->getTaskGroup()->title,
			"Due"			=> !empty($task->dt_due) ? date('d-m-Y', !is_numeric($task->dt_due) ? strtotime(str_replace('/', '-', $task->dt_due)) : $task->dt_due) : '',
			"Status"		=> $task->status,
			"Priority"		=> $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority
		];

		$template_data['can_view_task'] = $user->is_external == 0;
		
		$template_data['footer'] .= $w->partial("displaycomment", array("object" => $params['comment'], "displayOnly" => true, 'redirect' => '/inbox'), "admin");

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

		return new NotificationCallback($user, $template_data, $w->File->getAttachmentsFileList($task));
    });

}
