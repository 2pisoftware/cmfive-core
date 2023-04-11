<?php

/* 
 * duplicates a task allong with comments and attachments
 */
function duplicatetask_GET(Web $w) {
    //make a copy of the task
    $p = $w->pathMatch("id");
    $old_task_id = $p['id'];
    $old_task = TaskService::getInstance($w)->getTask($old_task_id);
    $new_task = $old_task->copy(false);
    $new_task->title = $old_task->title . " -Copy";
    $new_task->insert();
	
	$object_modification = new ObjectModification($w);
	$object_modification->table_name = 'task';
	$object_modification->object_id = $new_task->id;
	$object_modification->insert();
	
    //copy the task data
    $old_task_data = TaskService::getInstance($w)->getTaskData($old_task_id);
    if(!empty($old_task_data[0])) {
        $new_task_data = $old_task_data[0]->copy(false);
        $new_task_data->task_id = $new_task->id;
        $new_task_data->insert();
    }
    //copy the task user notify
    $task_group_members =  TaskService::getInstance($w)->getMembersInGroup($old_task->task_group_id);
    foreach($task_group_members as $member){
        $old_task_user_notify = TaskService::getInstance($w)->getTaskUserNotify($member[1], $old_task_id);
        if(!empty($old_task_user_notify)) {
            //print_r($old_task_user_notify); die;
            $new_task_user_notify = $old_task_user_notify->copy(false);
            $new_task_user_notify->user_id = AuthService::getInstance($w)->loggedIn();
            $new_task_user_notify->task_id = $new_task->id;
            $new_task_user_notify->insert();
        }
    }
    //copy the task comments
    $old_task_comments = CommentService::getInstance($w)->getCommentsForTable("task", $old_task_id);
    if(!empty($old_task_comments)) {
        foreach ($old_task_comments as $old_comment) {
			if ($old_comment->is_system == 0) {
				$new_comment = $old_comment->copy(false);
				$new_comment->obj_id = $new_task->id;
				$new_comment->insert();
				$new_comment->creator_id = $old_comment->creator_id;
				$new_comment->update();
			}
        }
    }
    //copy the task attachments
    $old_task_attachments = FileService::getInstance($w)->getAttachments("task", $old_task_id);
    if(!empty($old_task_attachments)) {
        foreach ($old_task_attachments as $old_attachment) {
            $new_attachment = $old_attachment->copy(false);
            $new_attachment->parent_id = $new_task->id;
            $new_attachment->insert();
        }
    }
    $w->msg("Task duplicated","/task/edit/" . $new_task->id);
}
