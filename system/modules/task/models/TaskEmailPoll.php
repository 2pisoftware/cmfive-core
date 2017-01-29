<?php
/**
 * 
 * Enable creation of new tasks via email
 * 
 * @author carsten
 *
 */
class TaskEmailPoll extends DbObject {
	public $task_group_id;
	public $email_address;
	public $pop_login;
	public $pop_password;
	public $pop_host;
	public $pop_port;
	public $pop_method; 			// SSL, PLAIN, TFL
	public $default_task_type;
	public $default_task_priority;
	public $default_task_status;
	public $default_assignee_id;
	public $accept_non_users;		// if set, then anyone can post to this group, even non Flow users
	public $default_non_user_id;	// select a user who appears as sender, when accepting posts from non-users
	public $reply_non_user_on_new_task;		// 0 / 1 whether to send a task creation email to non-flow users!
}
