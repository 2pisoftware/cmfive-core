<?php
//////////////////////////////////////////////////
//			EXECUTE REPORT						//
//////////////////////////////////////////////////

// display the form allowing users to set report parameters
function runreport_ALL(Web &$w) {
	$w->Report->navigation($w, "Generate Report");
	$p = $w->pathMatch("id");

	// if there is a report ID in the URL ...
	if (!empty($p['id'])) {		
		// get member
		$member = $w->Report->getReportMember($p['id'],$w->session('user_id'));

		// get the relevant report
		$rep = $w->Report->getReportInfo($p['id']);

		// if report exists, first check status and user role before displaying
		if (!empty($rep)) {
			if (($rep->is_approved == "0") && ($member->role != "EDITOR") && (!$w->Auth->user()->hasRole("report_admin"))) {
				$w->msg($rep->title . ": Report is yet to be approved","/report/index/");
			}
			else {
				// display form
				$w->Report->navigation($w, $rep->title);
				History::add("Report: ".$rep->title);

				// get the form array
				$form = $rep->getReportCriteria();
                                
				// if there is a form display it, otherwise say as much
				if ($form) {
					$theform = Html::form($form,$w->localUrl("/report/exereport/".$rep->id),"POST"," Display Report ");
				} else {
					$w->redirect($w->localUrl("/report/exereport/".$rep->id));
				}

				// display
				$w->ctx("rep",$rep);
				$w->ctx("report",$theform);
			}
		}
	}
}
