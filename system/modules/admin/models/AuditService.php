<?php
class AuditService extends DbService
{

    /**
     *
     * Adds an entry to the audit table
     *
     * The blacklist is a simple array of the form:
     * array(
     *         array("<module>","<action>"),
     *         array("<module>","<action>"),
     *         ...
     * )
     *
     * @param $blacklist
     */
    public function addAuditLogEntry($blacklist = null)
    {
        // if blacklist exists
        // then bail out if the current module and action
        // is in the list
        if ($blacklist) {
            foreach ($blacklist as $line) {
                if (
                    $line[0] == $this->w->currentModule() &&
                    ($line[1] == $this->w->currentAction() || $line[1] == "*")
                ) {
                    return;
                }
            }
        }
        $log = new Audit($this->w);
        $log->module = $this->w->currentModule();
        $log->submodule = $this->w->currentSubModule();
        $log->action = $this->w->currentAction();
        $log->path = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '';
        $log->ip = $this->w->requestIpAddress();
        $log->insert();
    }

    public function addDbAuditLogEntry($action, $class, $id)
    {
        if ($class != "Audit") {
            $log = new Audit($this->w);
            $log->module = $this->w->currentModule();
            $log->submodule = $this->w->currentSubModule();
            $log->action = $this->w->currentAction();
            $log->path = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '';
            $log->ip = $this->w->requestIpAddress();
            $log->db_action = $action;
            $log->db_class = $class;
            $log->db_id = $id;
            $log->insert();
        }
    }

    public function getLoggedUsers()
    {
        $ids = $this->_db->sql("select distinct creator_id from audit")->fetch_all();
        $users = array();
        foreach ($ids as $id) {
            $users[] = $this->getObject("User", $id["creator_id"]);
        }
        return $users;
    }

    public function getLoggedModules()
    {
        $modules = $this->_db->sql("select distinct module from audit order by module")->fetch_all();
        foreach ($modules as $m) {
            $list[] = $m['module'];
        }
        return $list;
    }

    public function getLoggedActions()
    {
        $actions = $this->_db->sql("select distinct action from audit order by action")->fetch_all();
        foreach ($actions as $m) {
            $list[] = $m['action'];
        }
        return $list;
    }

    /**
     * Return a list of user objects for
     * all users that have interacted with the
     * system in the last number of minutes
     * as defined by $idleMinutes
     *
     * @param $idleMinutes (0..59)
     */
    public function getLoggedInUsers($idleMinutes = 10)
    {
        $users = [];
        $stmt = "SELECT distinct creator_id FROM audit where timediff(now(), dt_created) < " . $this->_db->quote("00:" . $idleMinutes  . ":00") . " and creator_id > 0";
        $res = $this->_db->sql($stmt)->fetch_all();
        if ($res && sizeof($res)) {
            foreach ($res as $row) {
                $users[] = $this->getObject("User", $row['creator_id']);
            }
        }
        return $users;
    }

    public function getAudits($dt_from = null, $dt_to = null, $user_id = null, $module = null, $action = null)
    {
        //build where array
        $where = [];
        if (!empty($user_id)) {
            $where['creator_id'] = $user_id;
        }
        if (!empty($module)) {
            $where['module'] = $module;
        }
        if (!empty($action)) {
            $where['action'] = $action;
        }
        var_dump($where);
        $results = $this->getObjects('Audit', $where);
        var_dump($results);

        //filter results by date
        $filteredResults = [];
        //convert dates to and from to DD-MM-YYYY HH:ii:ss format. Name $formatdt_from and $formatdt_to
        //Convert dates to and from to timestamp
        // $from = DateTime::createFromFormat('!d-m-Y H:i:s', $formatdt_from);
        // if ($from === false) {
        //     die(LogService::getInstance($w)->setLogger("Admin")->error("formatdt_from failed in AuditService"));
        // } else {
        //     $tsFrom = $from->getTimestamp();
        // }
        // $to = DateTime::createFromFormat('!d-m-Y H:i:s', $formatdt_to);
        // if ($to === false) {
        //     die(LogService::getInstance($w)->setLogger("Admin")->error("formatdt_to failed in AuditService"));
        // } else {
        //     $tsTo = $to->getTimestamp();
        // }
        //then check the date created ($dt_created) for each result against the two from dates
        foreach ($results as $result) {
        }
    }
}
