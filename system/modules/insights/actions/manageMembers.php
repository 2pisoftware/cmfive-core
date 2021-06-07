<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

function manageMembers_ALL(Web $w)
{


    $insight_class = $w->request('insight_class');
    //var_dump($insight_class);
    //die;

    //define member id for edit and delete buttons
    $p = $w->pathMatch('id');
    $member = InsightService::getInstance($w)->GetMemberForId($p['id']);
    $insight = InsightService::getInstance($w)->getInsightInstance($insight_class);
    if (empty($insight)) {
        $w->error('Insight does not exist', '/insights');
    }
    $w->ctx("title", "Manage Members for $insight->name");

    // access service functions using the Web $w object and the module name
    $memberList = InsightService::getInstance($w)->getAllMembersForInsightClass($insight_class);
    //var_dump($modules);

    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Memeber', 'Is Email Recipient', 'Role', 'Actions'];
    if (!empty($memberList)) {
        foreach ($memberList as $member) {
            $row = [];
            // add values to the row in the same order as the table headers
            $row[] = AuthService::getInstance($w)->getUser($member->user_id)->getContact()->getFullName();
            $row[] = $member->recieves_emails;
            $row[] = $member->type;
            // the actions column is used to hold buttons that link to actions per item. Note the item id is added to the href on these buttons.
            $actions = [];
            $actions[] = Html::box("/insights-members/editMembers/$member->id?" . $member->insight_class, 'Edit', true);
            $actions[] = Html::b("/insights-members/deleteMembers/$member->id?" . $member->insight_class, 'Delete', 'Are you sure you want to delete this member?');
            $row[] = implode('', $actions);
            $table[] = $row;
        }
    }

    //send the table to the template using ctx
    $w->ctx('membersTable', Html::table($table, 'membersTable', 'tablesorter', $tableHeaders));
}
