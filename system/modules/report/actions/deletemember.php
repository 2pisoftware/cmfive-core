<?php
function deletemember_GET(Web &$w)
{
    $p = $w->pathMatch("report_id", "user_id");

    // get details of member to be deleted
    $member = ReportService::getInstance($w)->getReportMember($p['report_id'], $p['user_id']);

    if ($member) {
        // build a static form displaying members details for confirmation of delete
        $f = HtmlBootstrap5::form([
            ["Confirm Delete Member", "section"],
            ["", "hidden", "is_deleted", "1"],
            ["Name", "static", "name", ReportService::getInstance($w)->getUserById($member->user_id)],
        ], $w->localUrl("/report/deletemember/" . $member->report_id . "/" . $member->user_id), "POST", " Delete ");
    } else {
        $f = "No such member?";
    }
    // display form
    $w->setLayout(null);
    $w->ctx("deletemember", $f);
}

function deletemember_POST(Web &$w)
{
    $p = $w->pathMatch("report_id", "user_id");
    // get the details of the person to delete
    $member = ReportService::getInstance($w)->getReportMember($p['report_id'], $p['user_id']);
    $_POST['id'] = $member->id;

    // if member exists, delete them
    if ($member) {
        $member->fill($_POST);
        $member->update();

        $w->msg("Member deleted", "/report/edit/" . $p['report_id'] . "#members");
    } else {
        // if member somehow no longer exists, say as much
        $w->msg("Member no longer exists?", "/report/edit/" . $p['report_id'] . "#members");
    }
}
