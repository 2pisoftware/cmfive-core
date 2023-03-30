<?php
class Timelog extends DbObject
{
    public $object_class;
    public $object_id;

    public $user_id;
    public $dt_start;
    public $dt_end;
    public $time_type;
    public $is_suspect;

    public $creator_id;
    public $modifier_id;
    public $dt_created;
    public $dt_modified;
    public $is_deleted;

    public static $_validation = [
        "object_class" => ['required'],
        "object_id" => ['required'],
        "dt_start" => ['required'],
        "user_id" => ["required"],
    ];

    public function isRunning()
    {
        return !empty($this->dt_start) && empty($this->dt_end);
    }

    // Getters
    public function getDateStart()
    {
        if (!empty($this->dt_start)) {
            return date('Y-m-d', $this->dt_start);
        }
        return null;
    }

    public function getTimeStart()
    {
        if (!empty($this->dt_start)) {
            return date('H:i', $this->dt_start);
        }
        return null;
    }

    public function getTimeEnd()
    {
        if (!empty($this->dt_end)) {
            return date('H:i', $this->dt_end);
        }
        return null;
    }

    public function getHoursWorked()
    {
        if (!empty($this->dt_end)) {
            $date_time_diff = $this->dt_end - $this->dt_start;
            return intval($date_time_diff / 3600);
        }
        return null;
    }

    public function getMinutesWorked()
    {
        if (!empty($this->dt_end)) {
            $date_time_diff = $this->dt_end - $this->dt_start;
            $date_time_diff -= intval($date_time_diff / 3600) * 3600;
            return round($date_time_diff / 60);
        }
        return null;
    }

    public function getUser()
    {
        return $this->getObject("User", $this->user_id);
    }

    public function getFullName()
    {
        $user = $this->getUser();
        if (!empty($user->id)) {
            $contact = $user->getContact();
            if (!empty($contact->id)) {
                return $contact->getFullName();
            }
        }
        return '';
    }

    public function getDuration()
    {
        if (!empty($this->dt_start) && !empty($this->dt_end)) {
            return ($this->dt_end - $this->dt_start);
        }
    }

    // Only return the first comment (comments are 1 - many association but we want to emulate 1 - 1)
    public function getComment()
    {
        if ($this->id) {
            $comments = CommentService::getInstance($this->w)->getCommentsForTable($this, $this->id);
            return !empty($comments[0]->id) ? $comments[0] : new Comment($this->w);
        }
        return null;
    }

    public function setComment($comment)
    {
        if ($this->id) {
            $comment_object = $this->getComment();

            if (!empty($comment_object->id)) {
                $comment_object->comment = $comment;
                $comment_object->update();
            } else {
                CommentService::getInstance($this->w)->addComment($this, $comment);
            }
        }
    }

    public function getLinkedObject()
    {
        if (!empty($this->object_class) && !empty($this->object_id)) {
            if (class_exists($this->object_class)) {
                return $this->getObject($this->object_class, $this->object_id);
            }
        }
    }

    public function start($object, $start_time = null)
    {
        if (empty($object->id)) {
            return false;
        }

        $this->object_class = get_class($object);
        $this->object_id = $object->id;

        $this->dt_start = !empty($start_time) ? $start_time : time();
        $this->user_id = AuthService::getInstance($this->w)->user()->id;
        $this->insert(false);

        return true;
    }

    public function stop()
    {
        if (empty($this->dt_end)) {
            $this->dt_end = time();
            $this->update();
        }
    }

    public function canDelete(User $user)
    {
        $hook_results = $this->w->callHook('timelog', 'can_delete_timelog', $this);
        foreach ($hook_results as $result) {
            if (!$result) {
                return false;
            }
        }
        // user is admin
        if ($user->is_admin) {
            return true;
        }
        // user is the timelog assignee
        if ($this->user_id == $user->id) {
            return true;
        }
        // user is an owner of the taskgroup
        $object = TimelogService::getInstance($this->w)->getObject($this->object_class, $this->object_id);
        if (get_class($object) == 'Task') {
            if (!empty($object->task_group_id)) {
                $task_group = TaskService::getInstance($this->w)->getObject('TaskGroup', $object->task_group_id);
                if ($task_group->isOwner($user)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function canEdit(User $user)
    {
        return $this->canDelete($user);
    }
}
