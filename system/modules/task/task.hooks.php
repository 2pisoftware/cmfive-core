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
				$message_struct['can_view_task'] = $user->is_external == 0;
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
            $subject = "Task " . $object->title . " [" . $object->id . "][" . $object->status . "] - " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $object->title . " details has been updated</p>";
			$message .= "<p>Status: " . $object->__old['status'] . " => " . $object->status . "</p>";
            
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($object);
			
            $user_object = $w->Auth->getUser($user);
            $message .= $object->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            if (Config::get('inbox.active') === true) {
                $w->Inbox->addMessage($subject, $message, $user);
            }
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
            $subject = "Task - " . $task->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $task->title . " has had a new time log entry</p>";
            
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($task);
			
            $user_object = $w->Auth->getUser($user);
            $message .= $task->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

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
            $subject = "Task - " . $task->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $task->title . " has got a new attachment</p>";
            
			// Get additional details
			$message .= $w->Task->getNotificationAdditionalDetails($task);
			
            $user_object = $w->Auth->getUser($user);
            $message .= $task->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            $w->Inbox->addMessage($subject, $message, $user);
        }
    }
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
    
    if (!empty($params['recipients'])) {
        $commentor = $w->auth->getUser($params['commentor_id']);
        if (!empty($commentor)) {
            foreach($params['recipients'] as $key => $user_id) {
                $user = $w->auth->getUser($user_id);
                if (!empty($user)) {
                    //$booking = $w->booking->getBookingForId($params['object_id']);
                    $task = $w->task->getTask($params['object_id']);
                    if (!empty($task)) {
                        // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
                        if ($params['is_new'] == true) {
                            $subject = (!empty($commentor->id) ? $commentor->getFullName() : 'Someone') . ' has commented on a task that you\'re apart of ('.$task->title.')';
                        } else {
                            $subject = (!empty($commentor->id) ? $commentor->getFullName() : 'Someone') . ' has edited commented on a task that you\'re apart of ('.$task->title.')';
                        }

					    $template = $w->Template->findTemplate("task", "notification_email");
					    $message = '';
						if (!empty($template->id)) {
							$message_struct = [];
							$message_struct['status']		= "[{$object->id}] A new task has been created";
							$message_struct['footer']		= $object->description;
							$message_struct['action_url']	= $w->localUrl('/task/edit/' . $object->id);
							
							$message_struct['fields'] = [
								"Assigned to"		=> !empty($object->assignee_id) ? $object->getAssignee()->getFullName() : '',
								"Title"				=> $object->title,
								"Status"			=> $object->status,
								"Comment"       	=> $w->partial("displaycomment", array("object" => $params['comment'], "displayOnly" => true, 'redirect' => '/inbox'), "admin")
							];

							foreach ($users_to_notify as $user) {
								$message_struct['can_view_task'] = $user->is_external == 0;
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
							}
							$message = $w->Template->render($template, $message_struct);
						} else {
							$message = $task->toLink(null, null, $user_object);
	                        $message .= $w->partial("displaycomment", array("object" => $params['comment'], "displayOnly" => true, 'redirect' => '/inbox'), "admin");
	                        // Get additional details
	                        $message .= $w->Task->getNotificationAdditionalDetails($task);
	                        if ($user->is_external == 0) {
		                        $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";
		                    }
	                    }

                        $w->Mail->sendMail($user->getContact()->email, $commentor->getContact()->email, $subject, $message);
                    } else {
                        //no task
                        $w->log->error("Task: No task found for comment notifications");
                    }
                } else {
                    //no user for recipient
                    $w->log->error("Task: No user found for recipient in comment notifications");
                }
            }
        } else {
            //no commentor
            $w->log->error("Task: No user found for commentor in comment notifications");
        }
    } else {
        //no recipients
        $w->log->error("Task: No recipients found for comment notifications");
    }
}
