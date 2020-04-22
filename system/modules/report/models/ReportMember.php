<?php

class ReportMember extends DbObject
{
    public $report_id;      // report id
    public $user_id;        // user id
    public $role;           // user role: user, editor
    public $is_email_recipient;
    public $is_deleted;     // deleted flag

    // Dont remove this, used in ReportService
    public static $_db_table = "report_member";

    public function getReport()
    {
        return $this->getObject("Report", $this->report_id);
    }

    public function getUser()
    {
        return $this->getObject("User", ['id' => $this->user_id, "is_deleted" => 0]);
    }
}
