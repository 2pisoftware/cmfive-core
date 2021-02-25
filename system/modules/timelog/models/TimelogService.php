<?php

/**
 * This service class aids in the registration and usage of timelog objects
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class TimelogService extends DbService {
    private $_trackObject = null;

    /**
     * Returns all time logs for a given user
     *
     * @param User $user
     * @param boolean $includeDeleted
     * @return Timelog
     */
    public function getTimelogsForUser(User $user = null, $include_deleted = false, $time_start = null, $time_end = null) {
       
        if ($user === null) {
            $user = $this->w->Auth->user();
        }

        $where = ['user_id' => $user->id];
        if (!$include_deleted) {
            $where['is_deleted'] = 0;
        }

        if (!empty($time_start) && !empty($time_end)) {
            $bracket_start = $time_start->dt_start;
            $start_condition = date('Y-m-d H:i:s', $bracket_start);
            $where['dt_start >= ?'] = $start_condition;
            
            $bracket_end = $time_end->dt_end;
            $end_condition = date('Y-m-d H:i:s', $bracket_end);
            $where['dt_start <= ?'] = $end_condition;
        }

        return $this->getObjects("Timelog", $where, false, true, "dt_start DESC", null, 100);
    }

    public function countTotalTimelogsForUser(User $user = null, $include_deleted = false) {
        if ($user === null) {
            $user = $this->w->Auth->user();
        }

        $where = ['user_id' => $user->id];
        if (!$include_deleted) {
            $where['is_deleted'] = 0;
        }

        return $this->db->get("timelog")->where($where)->count();
    }

    public function getTimelogsForObject($object) {
        if (!empty($object->id)) {
            return $this->getObjects("Timelog", ["object_class" => get_class($object), "object_id" => $object->id, "is_deleted" => 0]);
        }
    }

    /**
     * Returns number of timelogs for a given object
     *
     * @param DbObject $object
     * @return int
     */
    public function countTimelogsForObject($object) {
        if (!empty($object->id)) {
            return $this->w->db->get('timelog')->where("object_class", get_class($object))->where("object_id", $object->id)
                        ->where('is_deleted', 0)->count();
        }
        return 0;
    }

    public function getTimelogsForObjectByClassAndId($object_class, $object_id) {
        if (!empty($object_class) || !empty($object_id)) {
            return $this->getObjects("Timelog", ["object_class" => $object_class, "object_id" => $object_id, "is_deleted" => 0], false, true, "dt_start ASC");
        }
    }

    public function countTimelogsForUserAndObject($user, $object) {
        if (!empty($user) && !empty($object) && is_a($object, 'DbObject')) {
            return $this->w->db->get('timelog')->where('user_id', $user->id)
                    ->where("object_class", get_class($object))
                    ->where("object_id", $object->id)
                    ->where('is_deleted', 0)->count();
        }
        return 0;
    }

    /**
     * Returns all non deleted timelogs
     *
     * @return Array<Timelog>
     */
    public function getTimelogs() {
        return $this->getObjects("Timelog", ["is_deleted" => 0]);
    }

    public function getTimelog($id) {
        return $this->getObject("Timelog", $id);
    }

    public function getActiveTimeLogForUser() {
        return $this->getObject("Timelog", ["is_deleted" => 0, "dt_end" => null, "user_id" => $this->w->Auth->user()->id]);
    }

    public function hasActiveLog() {
        $timelog = $this->getActiveTimeLogForUser();
        return !empty($timelog);
    }

    public function hasTrackingObject() {
        return !empty($this->_trackObject);
    }

    public function registerTrackingObject($object) {
        $this->_trackObject = $object;
    }

    public function getTrackingObject() {
        return $this->_trackObject;
    }

    public function getTrackingObjectClass() {
        if ($this->hasTrackingObject()) {
            return get_class($this->_trackObject);
        }
    }

    public function getJSTrackingObject() {
        if ($this->hasTrackingObject()) {
            $class = new stdClass();
            $class->class = get_class($this->_trackObject);
            $class->id = $this->_trackObject->id;
            return json_encode($class);
        }
    }

    public function shouldShowTimer() {
        // Check if tracking object set or existing timelog is running        
        return ((!empty(AuthService::getInstance($this->w)->user())) && AuthService::getInstance($this->w)->user()->hasRole("timelog_user") && ($this->hasTrackingObject() || $this->hasActiveLog()));
    }

    /**
     * returns a list of objects to which you can attach timelogs
     * @return type array
     */
    public function getLoggableObjects() {
        //get a list of all active modules
        $objects = [];
        $modules = array_filter(Config::keys() ? : [], function($module) {
            return Config::get("$module.active") === true;
        });

        if (!empty($modules)) {
            foreach ($modules as $key => $module) {
                $timelog = Config::get("$module.timelog");
                //check module config for timelog enabled objects
                if ($timelog !== null && is_array($timelog)) {
                    foreach ($timelog as $value) {
                        $objects[$value] = $value;
                    }
                }

            }
        }
        return $objects;
    }

    public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? : array();

        $tracking_object = $w->Timelog->getTrackingObject();

        if ($w->Auth->loggedIn()) {
            $w->menuLink("timelog/index", "Timelog Dashboard", $nav);
            $w->menuBox("timelog/edit" . (!empty($tracking_object) && !empty($tracking_object->id) ? "?class=" . get_class($tracking_object) . "&id=" . $tracking_object->id : ''), "Add Timelog", $nav);
        }

        $w->ctx("navigation", $nav);
        return $nav;
    }

    //Returns an array of timelogs in groups of 10 days
    public function daysForTimelogs($user) {
        $timelogs = $this->timelog->getTimelogsForUser($user);
        $previous_timelog = null;
        $current_timelog = null;

        $days_with_logs = [];
        $i = 0;
        $subset_count = -1;

        foreach ($timelogs as $timelog) {
            $current_timelog = $timelog;
            if ($previous_timelog != null) {
                $date = $current_timelog->dt_start;
                $previous_date = $previous_timelog->dt_start;

                $result = date('d', $date) === date('d', $previous_date);

                if ($result == false) {
                    if ($i % 10 == 0) {
                        $subset_count += 1;
                    }

                    $days_with_logs[$subset_count][] = $timelog;
                    $i += 1;
                }
            }
            $previous_timelog = $current_timelog;
        }
        return $days_with_logs;
    }
}