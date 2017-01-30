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
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($object, TASK_NOTIFICATION_TASK_CREATION);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_CREATION);
        
        // send it to the inbox of the user's on our send list
		// prepare our message, add heading, add URL to task, add notification advice in messgae footer 
		$subject = $event_title . "[" . $object->id . "]: " . $object->title;
        $logged_in_user = $w->Auth->user();
		
		// Get template
		$template = $w->Template->findTemplate("task", "notification_email");
		if (!empty($template->id)) {
			$message_struct = [];
			$message_struct['status']		= "[{$object->id}] A new task has been created";
			$message_struct['footer']		= $object->description;
			$message_struct['action_url']	= $w->localUrl('/task/edit/' . $object->id);
			
			$message_struct['fields'] = [
				"Assigned to"	=> !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '',
				"Type"			=> $object->getTypeTitle(),
				"Title"			=> $object->title,
				"Due"			=> !empty($object->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $object->dt_due))) : '',
				"Status"		=> $object->status,
				"Priority"		=> $object->isUrgent() ? "<b style='color: orange;'>{$object->priority}</b>" : $object->priority
			];

			foreach ($users_to_notify as $user) {
				// Get additional details
				$additional_details = $w->Task->getNotificationAdditionalDetails($object);
				if (!empty($additional_details)) {
					$message_struct['footer'] .= $additional_details;
				}

				$user_object = $w->Auth->getUser($user);
				if (!empty($object->assignee_id)) {
					if ($user_object->id == $object->assignee_id) {
						$message_struct['fields']["Assigned to"] = "You (" . $object->getAssignee()->getFullName() . ")";
					} else {
						$message_struct['fields']["Assigned to"] = !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '';
					}
				} else {
					$message_struct['fields']["Assigned to"] = "No one";
				}

				$attachments = $w->File->getAttachmentsFileList($object);
				$message = $w->Template->render($template, $message_struct);
				if (!$logged_in_user || $logged_in_user->id !== $user_object->id) {
					$w->Mail->sendMail(
						$user_object->getContact()->email, 
						!empty($logged_in_user->id) ? $logged_in_user->getContact()->email : Config::get('main.company_support_email'),
						$subject, $message, null, null, $attachments
					);
				}

				// Add message to inbox (needed?) but dont send an email
                                if (Config::get('inbox.active') === true) {
                                    $w->Inbox->addMessage($subject, $message, $user, null, null, false);
                                }
			}
		} else {
			// Fallback to old style
			$w->Log->error("Task notification email template not found (Category: 'notificiation_email')");
			foreach ($users_to_notify as $user) {
				$message = "<b>" . $event_title . " [" . $object->id . "]</b><br/>\n";
				$message .= "<p>A new task has been created</p>";

				$message .= "<p><b>Subject:</b> " . $object->title . "</p>";
				$message .= "<p><b>Body:</b>" . $object->description . "</p>";

				// Get additional details
				$message .= $w->Task->getNotificationAdditionalDetails($object);

				$user_object = $w->Auth->getUser($user);
				$message .= "<br/><p>Access the task here: " . $object->toLink(null, null, $user_object) . "</p>";
				$message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";
							$attachments = $w->File->getAttachmentsFileList($object);
			
				if (!$logged_in_user || $logged_in_user->id !== $user_object->id) {
					$w->Mail->sendMail(
						$user_object->getContact()->email, 
						!empty($logged_in_user->id) ? $logged_in_user->getContact()->email : Config::get('main.company_support_email'),
						$subject, $message, null, null, $attachments
					);
				}
				
				// Add message to inbox (needed?) but dont send an email
                                if (Config::get('inbox.active') === true) {
                                    $w->Inbox->addMessage($subject, $message, $user, null, null, false);
                                }
			}
			
			// Add message to inbox (needed?) but dont send an email
            if (Config::get('inbox.active') === true) {            
                $w->Inbox->addMessage($subject, $message, $user, null, null, false);
            }
        }
    }
}

/**
 * Hook to notify relevant people when a task has been update
 * 
 * @param Web $w
 * @param Task $object
 */
function task_core_dbobject_after_update_Task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_update_Task");
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($object, TASK_NOTIFICATION_TASK_DETAILS);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
	// Only send emails where the status has changed
	if ($object->status == $object->__old['status']) {
		return;
	}
	
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DETAILS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = __("Task ") . $object->title . " [" . $object->id . "][" . $object->status . "] - " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $object->title . __(" details has been updated")."</p>";
			$message .= "<p>".__("Status: ") . $object->__old['status'] . " => " . $object->status . "</p>";
            
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($object);
			
            $user_object = $w->Auth->getUser($user);
            $message .= $object->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>".__("Note")."</b>: ".__("Go to ") . Html::a(WEBROOT . "/task/tasklist#notifications", __("Task > Task List > Notifications")) . __(", to edit the types of notifications you will receive.");

            if (Config::get('inbox.active') === true) {
                $w->Inbox->addMessage($subject, $message, $user);
            }
        }
    }
}

/**
 * Hook to notify relevant people when a task has been updated
 * 
 * @param Web $w
 * @param Task $object
 */
function task_comment_comment_added_task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_comment_comment_added_task");
    
    $task = $w->Task->getTask($object->obj_id);
    
    if (empty($task->id) || (!empty($object->is_system) && $object->is_system == 1)) {
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_COMMENTS);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    $comment_user = $w->Auth->getUser($object->creator_id);
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_COMMENTS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = (!empty($comment_user->id) ? $comment_user->getFullName() : __('Someone')) . __(' has commented on a task that you\'re a part of').' ('.$task->title.')';

            $user_object = $w->Auth->getUser($user);
            $message = $task->toLink(null, null, $user_object);
            $message .= $w->partial("displaycomment", array("object" => $object, "displayOnly" => true, 'redirect' => '/inbox'), "admin");

			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($task);
			
            $message .= "<br/><br/><b>".__("Note")."</b>: ".__("Go to ") . Html::a(WEBROOT . "/task/tasklist#notifications", __("Task > Task List > Notifications")) . __(", to edit the types of notifications you will receive.");

            $w->Inbox->addMessage($subject, $message, $user, null, null, true);
        }
    }
}

/**
 * Hook to notify relevant people when a task has been updated
 * 
 * @param Web $w
 * @param Task $object
 */
function task_comment_comment_added_comment(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_comment_comment_added_comment");
    
    // Check if the parent comment is attached to a task
    $comment = $object;
    while(strtolower($comment->obj_table) == "comment" && $comment->obj_id != NULL) {
        $comment = $w->Comment->getComment($comment->obj_id);
        
        // Check if the comment could not be found
        if (empty($comment->id)) {
            $w->Log->setLogger("TASK")->debug("Comment not found");
            return;
        }
    }
    
    // We only want task comments!
    if (strtolower($comment->obj_table) != "task") {
        $w->Log->setLogger("TASK")->debug("Comment parent not a task");
        return;
    }
    
    $task = $w->Task->getTask($comment->obj_id);
    if (empty($task->id)) {
        $w->Log->setLogger("TASK")->debug("Task not found");
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_COMMENTS);
    if (!in_array($task->assignee_id, $users_to_notify)) {
        $users_to_notify[$task->assignee_id] = $task->assignee_id;
    }
    
    // Add all users in comment thread to the notification
    $reply_comment = $object;
    $comment_thread_users = array();
    while(strtolower($reply_comment->obj_table) == "comment" && $comment->obj_id != NULL) {
        if (!in_array($reply_comment->creator_id, $users_to_notify)) {
            $comment_thread_users[$reply_comment->creator_id] = $reply_comment->creator_id;
        }
        $reply_comment = $w->Comment->getComment($comment->obj_id);
        
        // Check if the comment could not be found
        if (empty($comment->id)) {
            return;
        }
    }
    $users_to_notify = array_merge($comment_thread_users, $users_to_notify);
    $comment_user = $w->Auth->getUser($object->creator_id);
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_COMMENTS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = $comment_user->getFullName() . __(" replied to a comment ") . (in_array($w->Auth->user()->id, $comment_thread_users) ? __("that you're a part of ") : "") . __("for "). $task->title;
            $message = "<p>".__("Comment")."</p>";
            $message .= $w->partial("displaycomment", array("object" => $object, "displayOnly" => true, 'redirect' => '/inbox'), "admin");
            
            $user_object = $w->Auth->getUser($user);
            if ($task->canView($user_object)) {
                $message .= "<a href='" .  $w->localUrl("/task/edit/" . $task->id . "?scroll_comment_id=" . $object->id . "#comments") . "'><p>".__("Click here to view the comment")."</p></a>";            
            } else {
                $message .= "<p><b>".__("You are unable to view this task")."</b></p>";
            }
			
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($task);
            
            $message .= "<br/><br/><b>".__("Note")."</b>: ".__("Go to ") . Html::a(WEBROOT . "/task/tasklist#notifications", __("Task > Task List > Notifications")) . __(", to edit the types of notifications you will receive.");

            $w->Inbox->addMessage($subject, $message, $user, null, null, true);
        }
    }
}

function task_core_dbobject_after_insert_TaskTime(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_insert_TaskTime");
    
    $task = $object->getTask();
    
    if (empty($task->id)) {
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TIME_LOG);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TIME_LOG);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = __("Task - ") . $task->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $task->title . __(" has had a new time log entry")."</p>";
            
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($task);
			
            $user_object = $w->Auth->getUser($user);
            $message .= $task->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>".__("Note")."</b>: ".__("Go to ") . Html::a(WEBROOT . "/task/tasklist#notifications", __("Task > Task List > Notifications")) . __(", to edit the types of notifications you will receive.");

            $w->Inbox->addMessage($subject, $message, $user);
        }
    }
}

function task_attachment_attachment_added_task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_attachment_attachment_added_task");
    
    $task = $w->Task->getTask($object->parent_id);
    
    if (empty($task->id)) {
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DOCUMENTS);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DOCUMENTS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = __("Task - ") . $task->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $task->title . __(" has got a new attachment")."</p>";
            
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($task);
			
            $user_object = $w->Auth->getUser($user);
            $message .= $task->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>".__("Note")."</b>: ".__("Go to ") . Html::a(WEBROOT . "/task/tasklist#notifications", __("Task > Task List > Notifications")) . __(", to edit the types of notifications you will receive.");

            $w->Inbox->addMessage($subject, $message, $user);
        }
    }
}

// Admin user remove hook
function task_admin_remove_user(Web $w, User $user) {
	return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "task");
}
