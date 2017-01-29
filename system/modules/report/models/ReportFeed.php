<?php
class ReportFeed extends DBObject {
    public $report_id;		// source report id
    public $title;			// feed title
    public $description;	// feed description
    public $report_key;			// special feed key
    public $url;			// url to access feed
    public $dt_created;	// date created
    public $is_deleted;	// is deleted flag

    // get feed key upon insert of new feed
    function insert($force_validation = false) {
        if (!$this->report_key)
        $this->report_key = uniqid();

        // insert feed into database
        parent::insert();
    }
}