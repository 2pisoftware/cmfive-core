<?php

/**
 * Action to email reports, will send to users that are marked in the ReportMember
 * object, templates are enabled in the ReportTemplate object and one attachment
 * per enabled template is created per member.
 *
 * The report should make use of the current_user_id field, of which will be faked
 * by this action.
 *
 * @param Web $w
 */
function email_GET(Web $w): void
{
    // Get report
    @list($report_id) = $w->pathMatch();
    if (empty($report_id)) {
        LogService::getInstance($w)->setLogger("AUTOMATED_REPORT")->error("Report ID not given");
        return;
    }

    $report = ReportService::getInstance($w)->getReport($report_id);
    if (empty($report->id)) {
        LogService::getInstance($w)->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} not found");
        return;
    }

    // Get members list
    $members = array_filter($report->getMembers() ?: [], function ($member) {
        return $member->is_email_recipient == 1;
    });

    if (empty($members)) {
        LogService::getInstance($w)->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} has no recipient members");
        return;
    }

    // Get templates list
    $templates = array_filter($report->getTemplates() ?: [], function ($template) {
        return $template->is_email_template == 1;
    });

    if (empty($templates)) {
        LogService::getInstance($w)->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} has no recipient templates");
        return;
    }

    // Normalise member list
    $recipients = [];
    foreach ($members as $recipient_member) {
        $user = $recipient_member->getUser();
        if ($user->is_group) {
            $recipients = array_merge($recipients, getGroupMembers($user->id, $w));
        } else {
            $recipients[$user->login] = $user;
        }
    }

    // Generate report attachments from templates
    $data = $report->getReportData();
    if (empty($data)) {
        LogService::getInstance($w)->setLogger("AUTOMATED_REPORT")->error("Report {$report_id} generated no data");
        return;
    }

    foreach ($templates as $template) {
    }

    // Send email
}

// Recursive function to get members of a group
function getGroupMembers($user_id, Web $w)
{
    $members = [];
    $groupmembers = AuthService::getInstance($w)->getGroupMembers($user_id);
    if (!empty($groupmembers)) {
        foreach ($groupmembers as $groupmember) {
            if ($groupmember->is_group) {
                $members = array_merge($members, getGroupMembers($groupmember->id, $w));
            } else {
                $members[$groupmember->login] = $groupmember;
            }
        }
    }
    return $members;
}
