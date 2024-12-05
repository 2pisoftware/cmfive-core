<?php

use Html\Form\InputField\Checkbox;
use Html\Form\InputField\Hidden;
use Html\Form\Select;

// edit a member
function editmember_GET(Web &$w)
{
    $p = $w->pathMatch("repid", "userid");
    // get member details for edit
    $member = ReportService::getInstance($w)->getReportMember($p['repid'], $p['userid']);

    // build editable form for a member allowing change of membership type
    $f = HtmlBootstrap5::multiColForm([
        "Edit Member" => [
            [new Hidden([
                "id|name" => "report_id",
                "value" => $p["id"]
            ])],
            [
                (new Select([
                    "id|name" => "member",
                    "label" => "Member",
                    "options" => [ReportService::getInstance($w)->getUserById($member->user_id)],
                    "disabled" => true,
                ]))
                    ->setSelectedOption(ReportService::getInstance($w)->getUserById($member->user_id))
            ],
            [
                (new Select([
                    "id|name" => "role",
                    "label" => "Role",
                    "options" => ReportService::getInstance($w)->getReportPermissions()
                ]))
                    ->setSelectedOption($member->role)
            ],
            [new Checkbox(["id|name" => "is_email_recipient", "label" => "Is email recipient", "value" => $member->is_email_recipient])]
        ]
    ], $w->localUrl("/report/editmember/" . $p['userid']), "POST", " Update ");

    // display form
    $w->setLayout(null);
    $w->ctx("editmember", $f);
}

function editmember_POST(Web &$w)
{
    $p = $w->pathMatch("id");
    $member = ReportService::getInstance($w)->getReportMember($_POST['report_id'], $p['id']);

    $member->fill($_POST);
    $member->is_email_recipient = intval(!empty($_POST['is_email_recipient']));
    $member->update();

    $w->msg("Member updated", "/report/edit/" . $_POST['report_id'] . "#members");
}
