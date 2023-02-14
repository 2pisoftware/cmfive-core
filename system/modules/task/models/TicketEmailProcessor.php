<?php

//require_once(SYSTEM_LIBPATH . "/plancake-parser/PlancakeEmailParser.php");

class TicketEmailProcessor extends ProcessorType {

    public function getSettingsForm($current_settings = null, $w = null) {
        // Check if json
        if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        return ["Settings" => [
            [["Target Notify Email Address", "text", "target_email_address", @$current_settings->target_email_address]],
            [["Support reply-to email", "text", "support_email_address", @$current_settings->support_email_address]],
            [["Authenticate as", "autocomplete", "authenticate_as", @$current_settings->authenticate_as, !empty($w) ? AuthService::getInstance($w)->getUsers() : array()]],
            [["Default Task Name (when no subject given)", "text", "default_task_name", @$current_settings->default_task_name]],
            [['Default Task Description (when no email body given)', 'text', 'default_task_description', @$current_settings->default_task_description]]
        ]];
    }

    /**
     * create HelpdeskTicket from an email
     * 
     * @see ProcessorType::process()
     */
    public function process($processor) {
        if (empty($processor->id)) {
            return;
        }
        
        $settings = null;
        if (!empty($processor->settings)) {
            $settings = json_decode($processor->settings);
        }
        
        if (!empty($settings->authenticate_as)) {
            AuthService::getInstance($processor->w)->forceLogin($settings->authenticate_as);
        }
        
        LogService::getInstance($processor->w)->debug("Running ticket processor");

        $messages = ChannelService::getInstance($processor->w)->getNewMessages($processor->channel_id, $processor->id);
        if (!empty($messages)) {
            foreach ($messages as $message) {
                // Get a message status or create a new one
				$messagestatus = new ChannelMessageStatus($processor->w);
				$messagestatus->message_id = $message->id;
				$messagestatus->processor_id = $processor->id;

				// process the email body
				$rawdata = $message->getData();
				if (empty($rawdata)) {
					continue;
				}

				$email = unserialize($rawdata);

				// Blank subject has size = 1. If blank we use default
				$subject = $email->subject;
				if (strlen($subject) < 2) {
					$subject = !empty($settings->default_task_name) ? $settings->default_task_name : 'No ticket name provided';
				}

				$from = $email->from;
				// Validate email
				if (strpos($from ?? "", '@') === FALSE) {
					LogService::getInstance($processor->w)->error("From field is not an email address");
					continue;
				}

				// $to = $email->to;
				// $cc = $email->cc;

				// When adding default message for email without real body, 
				// it was decided to swap from using the html to the plain
				// version of the email body.
				$body = $email->body['plain'];
				if (strlen($body) < 1) {
					$body = !empty($settings->default_task_description) ? $settings->default_task_description : 'No ticket body provided';
				}
				// Get between in system/functions.php
				$from_email_address = getBetween($from, "<", ">");

				// Try and get contact from CRM
				$contact = AuthService::getInstance($processor->w)->getContactByEmail($from_email_address);

                // Find or create external user if no contact
                $user = !empty($contact->id) ? $contact->getUser() : null;

                $contact_name = '';
                if (empty($contact->id)) {
                	// Find or create an external user
                	echo "Creating external user<br/>";
                	LogService::getInstance($processor->w)->setLogger("HELPDESK")->info("Could not find existing contact for email, creating a new external user");

                	$user = new User($processor->w);
                	$contact = new Contact($processor->w);

                	// Get login from contacts name, or email address if it doesn't exist
                	if (strpos($from ?? "", '<') !== FALSE) {
                		$contact_name = @trim(explode('<', $from)[0]);
                	} else {
                		$contact_name = @trim(explode('@', $from_email_address)[0]);
                	}

                	// Get lastname
                	$exploded_name = explode(' ', $contact_name);
                	$lastname = array_filter($exploded_name);
                	array_shift($lastname);
                   	$lastname = implode(' ', $lastname);

                	$contact->firstname = array_filter($exploded_name)[0];
                	$contact->lastname = trim($lastname);

                	$contact->email = $from_email_address;
                	$contact->insert();

                	$user->login = $from_email_address;
                	$user->is_external = 1;
                	$user->contact_id = $contact->id;
                	$user->insert();

                	LogService::getInstance($processor->w)->setLogger("HELPDESK")->info("External user created, id: " . $user->id);
                	echo "External user created, id: " . $user->id . "<br/>";

                } else {
                	// Ensure a user object exists for the contact
                	AuthService::getInstance($processor->w)->createExernalUserForContact($contact->id);
                }

                if (empty($contact->id) || empty($user->id)) {
                	// Something else went wrong
                	LogService::getInstance($processor->w)->setLogger("HELPDESK")->error("Failed to find or create a contact or user, in TicketEmailProcessor");
                }

                // Find the right taskgroup and find/create the task
                $task = null;
                $new_task = true;

                $task_id = getBetween($subject, '[', ']');
				if (!empty($task_id)) {
					// Try and find the task
					$task = TaskService::getInstance($processor->w)->getTask($task_id);
					echo "Email is a reply to existing task: {$task_id}<br/>";

					if (!empty($task)) {
						// Add the reply to the task as a comment
						$comment = new Comment($processor->w);
						$comment->obj_table = "task";
						$comment->obj_id = $task->id;
						$comment->comment = $email->body['plain'];
						$comment->insert();

						if (!empty($contact->id)) {
							$comment->creator_id = $contact->getUser()->id;
						} else if (!empty($user)) {
							$comment->creator_id = $user->id;
						}
						$comment->update();

						echo "Found task {$task->id} and added comment<br/>";
						$new_task = false;
					}
				}

				$support_taskgroup = TaskTicketService::getInstance($processor->w)->getTaskGroup();

                // Create task if necessary
				if ($new_task) {
					echo "Creating new task<br/>";
					$task = TaskService::getInstance($processor->w)->createTask(TaskTicketService::getInstance($processor->w)->task_type, $support_taskgroup->id, $subject, "From: $from<br/>\nBody: $body", "Normal", null, $support_taskgroup->default_assignee_id);

					// Send email if its a new task
					// Get notify email address
					$email_address_notify = (!empty($settings->target_email_address) ? $settings->target_email_address : null);

					// Get support email template
					$support_template = TemplateService::getInstance($processor->w)->findTemplate("task", "accepted_ticket");
					if (!empty($support_template->id)) {
						// Send mail

						if (!empty($contact->id)) {
							// Render email template as Body field
							$template_body = TemplateService::getInstance($processor->w)->render($support_template, array("task" => $task, "contact" => $contact, "email" => $email));
						} else {
							$template_body = TemplateService::getInstance($processor->w)->render($support_template, array("task" => $task, "email" => $email));
						}
						if (!empty($from_email_address)) {
							// Send accepted ticket email
							LogService::getInstance($processor->w)->debug("Emailing sender");
							MailService::getInstance($processor->w)->sendMail($from_email_address, !empty($settings->support_email_address) ? $settings->support_email_address : "support@2pisoftware.com", "Accepted Ticket [{$task->id}] {$subject}", $template_body);

							// Send notification email
							if (!empty($email_address_notify)) {
								LogService::getInstance($processor->w)->debug("Sending notification email to " . $email_address_notify);
								// Get attachments
								$attachments = FileService::getInstance($processor->w)->getAttachments($task, (!empty($task->id) ? $task->id : null));
								$attachments_to_email = array();
								if (!empty($attachments)) {
									foreach ($attachments as $a) {
										// ignore email.txt
										if ($a->title === 'email.txt' || $a->title === 'rawemail.txt') {
											continue;
										}

										$attachments_to_email[] = FileService::getInstance($processor->w)->getFilePath($a->fullpath);
									}
								}   
								LogService::getInstance($processor->w)->debug(json_encode($attachments_to_email));

								MailService::getInstance($processor->w)->sendMail($email_address_notify, $email_address_notify, "Ticket: $from_email_address - $subject", 
										"Ticket [{$task->id}]: {$subject}<br/><br/>Email:<br/>{$body}<br/><a href='" . $processor->w->localUrl("/task/edit/" . $task->id) . "'>View the Task</a>", null, null, $attachments_to_email);
							}
						} else {
							LogService::getInstance($processor->w)->setLogger('crm')->error("No from email address found in support ticket");
						}
					} else {
						LogService::getInstance($processor->w)->error("Support template not found, reply email for task {$task->id} not sent");
					}
				}

				if (!empty($contact->id) || !empty($user->id)) {
					if (empty($user->id)) {
						$user = $contact->getUser();
					}

					if (empty($user->id)) {
						AuthService::getInstance($processor->w)->createExternalUserForContact($contact->id);
					}

					if (!empty($user->id)) {
						// Add external user as subscriber to task if an instance doesn't already exist
						$existing_subscriber = TaskService::getInstance($processor->w)->getObject('TaskSubscriber', ['user_id' => $user->id, 'task_id' => $task->id, 'is_deleted' => 0]);

						if (empty($existing_subscriber->id)) {
							$subscriber = new TaskSubscriber($processor->w);
							$subscriber->task_id = $task->id;
							$subscriber->user_id = $user->id;
							$subscriber->insert();
						} else {
							echo "Found existing subscriber<br/>";
						}
					} else {
						echo "Could not find user object for contact {$contact->id}<br/>";						
					}
				} else {
					echo "Both contact and user could not be found<br/>";
				}

				// Move Attachments to Task
				$attachments = FileService::getInstance($processor->w)->getAttachments($message, (!empty($message->id) ? $message->id : null));
				if (!empty($attachments) && !empty($task->id)) {
					echo "Moving attachments to task<br/>";
					foreach ($attachments as $a) {
						// Save them to the task instead
						$a->parent_table = "Task";
						$a->parent_id = $task->id;
						$a->update();
					}
				}
				echo "</pre><br/>";

				$messagestatus->is_successful = 1;
                $messagestatus->insert();
            }
        }
    }
}
