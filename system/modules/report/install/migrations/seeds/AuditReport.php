<?php

class AuditReport extends CmfiveSeedMigration {

	public $name = "Audit Report";
	public $description = "Audit log report";

	public function seed() {
		$report = new Report($this->w);
		$report->title = "Audit report";
    	$report->module = "Audit";
    	$report->description = "Shows audit information";
    	$report->report_code = "[[dt_from||date||Date From]]

[[dt_to||date||Date To]]

[[user_id||select||User||select u.id as value, concat(c.firstname,' ',c.lastname) as title from user u, contact c where u.contact_id = c.id order by title]]

[[module||select||Module||select distinct module as value, module as title from audit order by module asc]]

[[action||select||Action||select distinct action as value, concat(module,'/',action) as title from audit order by title]]

@@Audit Report||

select 
a.dt_created as Date, 
concat(c.firstname,' ',c.lastname) as User,  
a.module as Module,
a.path as Url,
a.db_class as 'Class',
a.db_action as 'Action',
a.db_id as 'DB Id'

from audit a

left join user u on u.id = a.creator_id
left join contact c on c.id = u.contact_id

where 
a.dt_created >= '{{dt_from}} 00:00:00' 
and a.dt_created <= '{{dt_to}} 23:59:59' 
and ('{{module}}' = '' or a.module = '{{module}}')
and ('{{action}}' = '' or a.action = '{{action}}') 
and ('{{user_id}}' = '' or a.creator_id = '{{user_id}}')

@@
";
    	$report->is_approved = 1;
    	$report->insert();

    	$member = new ReportMember($this->w);
    	$member->report_id = $report->id;
    	$member->user_id = AuthService::getInstance($this->w)->user()->id;
    	$member->role = 'OWNER';
    	$member->insert();
    }

}