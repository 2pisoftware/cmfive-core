<?php
//////////////////////////////////////////////////
//			DELETE REPORT						//
//////////////////////////////////////////////////

function deletereport_ALL(Web &$w) {
	$p = $w->pathMatch("id");

	// if there is  report ID in the URL ...
	if ($p['id']) {
		// get report details
		$rep = ReportService::getInstance($w)->getReportInfo($p['id']);

		// if report exists, delete
		if ($rep) {
			$rep->is_deleted = 1;
			$rep->update();

			// need to check if there is a feed associated with this report
			$feed = ReportService::getInstance($w)->getFeedInfobyReportId($rep->id);
				
			// if feed exists, set is_deleted flag. ie. delete feed as well as report
			if ($feed) {
				$feed->is_deleted = 1;
				$feed->update();
			}
			// return
			$w->msg("Report deleted","/report/index/");
		}
		// if no report, say as much
		else {
			$w->msg("Report no longer exists?","/report/index/");
		}
	}
}