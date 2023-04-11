<?php
class ReportLib {
	
	static function viewMemberstab(Web &$w, $id) {
		// return list of members of given report
		$members = ReportService::getInstance($w)->getReportMembers($id);
		// get report details
		$report = ReportService::getInstance($w)->getReportInfo($id);
	
		// set columns headings for display of members
		$line[] = array("Member","Role","");
	
		// if there are members, display their full name, role and button to delete the member
		if ($members) {
			foreach ($members as $member) {
				$line[] = array(
				ReportService::getInstance($w)->getUserById($member->user_id),
				$member->role,
				Html::box("/report/editmember/".$report->id . "/". $member->user_id," Edit ", true) .
					"&nbsp;&nbsp;" . 
				Html::box("/report/deletemember/".$report->id."/".$member->user_id," Delete ", true)
				);
			}
		}
		else {
			// if there are no members, say as much
			$line[] = array("Group currently has no members. Please Add New Members.", "", "");
		}
	
		$w->ctx("reportid",$report->id);
	
		// display list of group members
		$w->ctx("viewmembers",Html::table($line,null,"tablesorter",true));
	}	
}