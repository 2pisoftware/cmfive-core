<?php
// provide form by which to add members to a report
function addmembers_GET(Web &$w)
{
    $p = $w->pathMatch("id");

    $editors = $w->Auth->getUsersForRole("report_editor");
    $users = $w->Auth->getUsersForRole("report_user");

    $possibleMembers = array_unique(array_merge($users, $editors));
    $currentReportMembers = $w->Report->getReportMembers($p["id"]);
    $currentMembers = [];

    for ($i = 0; $i <= count($currentReportMembers) - 1; $i++) {
        $currentMembers[] = AuthService::getInstance($w)->getUser($currentReportMembers[$i]->user_id);
    }

    $members = array_diff($possibleMembers, $currentMembers);

    // build form
    $addUserForm = array(
        array("", "hidden", "report_id", $p['id']),
        array("Add Member", "select", "member", null, $members),
        array("With Role", "select", "role", "", $w->Report->getReportPermissions()),
    );

    $w->setLayout(null);
    $w->ctx("addmembers", Html::form($addUserForm, $w->localUrl("/report/updatemembers/"), "POST", " Submit "));
}
