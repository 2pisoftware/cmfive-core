<?php

use Html\Form\InputField\Hidden;
use Html\Form\Select;

// provide form by which to add members to a report
function addmembers_GET(Web &$w)
{
    $p = $w->pathMatch("id");

    $editors = AuthService::getInstance($w)->getUsersForRole("report_editor");
    $users = AuthService::getInstance($w)->getUsersForRole("report_user");

    $possibleMembers = array_unique(array_merge($users, $editors));
    $currentReportMembers = ReportService::getInstance($w)->getReportMembers($p["id"]);
    $currentMembers = [];

    for ($i = 0; $i <= count($currentReportMembers) - 1; $i++)
    {
        $currentMembers[] = AuthService::getInstance($w)->getUser($currentReportMembers[$i]->user_id);
    }

    $members = array_diff($possibleMembers, $currentMembers);

    // build form
    $addUserForm = [
        "Add Member" => [
            [new Hidden([
                "id|name" => "report_id",
                "value" => $p["id"]
            ])],
            [new Select(["id|name" => "member", "label" => "Member", "options" => $members])],
            [new Select(["id|name" => "role", "label" => "Role", "options" => ReportService::getInstance($w)->getReportPermissions()])]
        ]
    ];

    $w->setLayout(null);
    $w->ctx("addmembers", HtmlBootstrap5::multiColForm($addUserForm, $w->localUrl("/report/updatemembers/"), "POST", " Submit "));
}
