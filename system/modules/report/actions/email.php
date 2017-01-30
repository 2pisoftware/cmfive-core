<?php

define("REPORT_CACHE_PATH", "/cache/report");

/** 
 * Action to email reports, will send to users that are marked in the ReportMember
 * object, templates are enabled in the ReportTemplate object and one attachment
 * per enabled template is created per member.
 * 
 * The report should make use of the current_user_id field, of which will be faked
 * by this action.
 * 
 * @param <Web> $w
 */
function email_GET(Web $w) {
	// sender config default empty
	if (Config::get("main.company_support_email") === null) {
		$w->Log->setLogger("AUTOMATED_REPORT")->error("No send from email address given");
		return;
	}
	
	// Get report
	@list($report_id) = $w->pathMatch();
	if (empty($report_id)) {
		$w->Log->setLogger("AUTOMATED_REPORT")->error("Report ID not given");
		return;
	}
	
	$report = $w->Report->getReport($report_id);
	if (empty($report->id)) {
		$w->Log->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} not found");
		return;
	}
	
	// Get members list
	$members = array_filter($report->getMembers() ? : [], function($member) {
		return $member->is_email_recipient == 1;
	});
	
	if (empty($members)) {
		$w->Log->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} has no recipient members");
		return;
	}
	
	// Get templates list
	$templates = array_filter($report->getTemplates() ? : [], function($template) {
		return $template->is_email_template == 1;
	});

	if (empty($templates)) {
		$w->Log->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} has no recipient templates");
		return;
	}
	
	// Normalise member list
	$recipients = [];
	foreach($members as $recipient_member) {
		$user = $recipient_member->getUser();
		if ($user->is_group) {
			$recipients = array_merge($recipients, getGroupMembers($user->id));
		} else {
			$recipients[$user->login] = $user;
		}
	}
	
	// Check of cache directory exists, create if not
	if (!is_dir(ROOT_PATH . REPORT_CACHE_PATH)) {
		mkdir(ROOT_PATH . REPORT_CACHE_PATH, 0777, true);
	}
	
	if (empty($recipients)) {
		$w->Log->setLogger("AUTOMATED_REPORT")->error("No recipients for report ID: " . $report_id);
		return;
	}
	
	// Generate report attachments from templates
	foreach ($recipients as $login => $user) {
		
		// Get user contact object
		$contact = $user->getContact();
		if (empty($contact->id) || empty($contact->email)) {
			$w->Log->setLogger("AUTOMATED_REPORT")->error("No contact object/email address for user: " . $login);
			continue;
		}
		
		// Generate report
		$templatedata = $report->getReportData($user->id);
		if (empty($templatedata)) {
			$w->Log->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} generated no data for user " . $login);
			continue;
		} else {
			$attachments = [];
			
			foreach($templates as $report_template) {
				$results = $w->Template->render($report_template->template_id, ["data" => $templatedata, "w" => $w]);   
				
				// Generate PDF
				$template = $report_template->getTemplate();
				if (empty($template->id)) {
					$w->Log->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} generated no data for user " . $login);
					continue;
				}
				
				
				$filename = ROOT_PATH . REPORT_CACHE_PATH . '/' . $template->title . "_" . date("Ymd-H-i") . ".pdf";

				// Using TCPDF library
				require_once('tcpdf/tcpdf.php');

				// Set up PDF
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetTitle($report->title);
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
				//$pdf->setLanguageArray($l);
				// no header, set font and create a page
				$pdf->setPrintHeader(false);
				$pdf->SetFont("helvetica", "B", 9);
				$pdf->AddPage();

				$pdf->writeHTML($results, true, false, true, false);
				$pdf->Output($filename, 'F');

				$attachments[] = $filename;
			}
			
			// Render body
			$body_template = $w->Template->findTemplate("report", "email");
			$body_content = '';
			if (!empty($body_template)) {
				$body_content = $w->Template->render($body_template, ["name" => $contact->getFullName(), "module" => $report->module, "report_name" => $report->title]);
			} else {
				// TODO - work out user language and set locale etc
				$body_content = __("Dear ") . $contact->getFullName() . ", <br/>".__("Please find attached your reports")."<br/>".__("Kind regards");
			}
			
			// Send email
			$w->Mail->sendMail($contact->email, Config::get("main.company_support_email"), $report->title, $body_content, null, null, $attachments);
			
			// Clear report cached files
			foreach ($attachments as $attachment) {
				unlink($attachment);
			}
			
			$w->Log->setLogger("AUTOMATED_REPORT")->info("Automated report send to " . $contact->email . " with " . count($attachments) . " attachment" . (count($attachments) == 1 ? '' : 's'));
		}
	}
	
	// $w->redirect("/report");
	
}

// Recursive function to get members of a group
function getGroupMembers($user_id) {
	$members = [];
	$groupmembers = $this->Auth->getGroupMembers($user_id);
	if (!empty($groupmembers)) {
		foreach($groupmembers as $groupmember) {
			if ($groupmember->is_group) {
				$members = array_merge($members, getGroupMembers($groupmember->id));
			} else {
				$members[$groupmember->login] = $groupmember;
			}
		}
	}
	return $members;
}
