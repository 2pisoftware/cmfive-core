<?php

function manageMembers_ALL(Web $w) {
    

    $insight_class = $w->request('insight_class');
    //var_dump($insight_class);
      //die;
      
      $w->ctx("title", "Manage Members");

    // access service functions using the Web $w object and the module name
    $memberList = InsightService::getInstance($w)->getAllMembersForInsightClass($insight_class);
    //var_dump($modules);

    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Memeber','Is Email Recipient','Role','Actions'];
    if (!empty($memberList)) {
        foreach ($memberList as $member) {
            //$membername = $member->user_id->getContact();
            $row = [];
            // add values to the row in the same order as the table headers
            //$row[] = echo memberName;
            $row[] = AuthService::getInstance($w)->getUser($member->user_id)->getContact()->getFullName();
            $row[] = $member->recieves_emails;
            $row[] = $member->type;
            // the actions column is used to hold buttons that link to actions per item. Note the item id is added to the href on these buttons.
            $actions = [];
            $actions[] = Html::b('/insights-members/edit/' . $member->insight_class,'Edit');
            $actions[] = Html::b('/insights-members/delete/' . $member->insight_class, 'Delete', 'Are you sure you want to delete this member?');
            $row[] = implode('',$actions);
            $table[] = $row;
        }
    }

    //send the table to the template using ctx
    $w->ctx('membersTable', Html::table($table,'membersTable','tablesorter',$tableHeaders));

}


