<?php
class GanttChart extends DbObject {
	public $title;
	public $_modifiable;		// modifiable aspect
	public $task_group_id;		// a Gantt chart is always linked to a task group
	public $can_view;			// PRIVATE/OWNER/MEMBER/GUEST/ALL , if PRIVATE only the creator can view!
	public $can_edit;			// PRIVATE/OWNER/MEMBER/GUEST/ALL
}
