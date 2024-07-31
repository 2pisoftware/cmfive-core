<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

function index_ALL(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    $w->ctx("title", "Insights List");

    //get userId for logged in user
    $user_id = AuthService::getInstance($w)->user()->id;

    // access service functions using the Web $w object and the module name
    $modules = InsightService::getInstance($w)->getAllInsights('all');

    // Display a list of all the insights this user can see
    // Display a list of all the insights this user can see
    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Name', 'Module', 'Description', 'Actions'];
    if (!empty($modules)) {
        foreach ($modules as $modulename => $insights) {
            if (!empty($insights)) {
                foreach ($insights as $insight) {
                    $insight_class = get_class($insight);
                    $userHasAccess = false;
                    if (InsightService::getInstance($w)->IsMember($insight_class, $user_id)) {
                        $userHasAccess = true;
                    } else {
                        // check if this user is a member of a group (or parent group) with access to this insight report
                        $allMembers = InsightService::getInstance($w)->getAllMembersForInsightClass($insight_class);
                        foreach ($allMembers as $member) {
                            $userHasAccess = AuthService::getInstance($w)->isUserGroupMemberRecursive($member->user_id, $user_id);  // $member->user_id may be a user or a group
                            if ($userHasAccess) {
                                break;
                            }
                        }
                    }
                    if ($userHasAccess) {
                        // add values to the row in the same order as the table headers
                        $row = [
                            Html::a('/insights/viewInsight/' . $insight_class, $insight->name),
                            $modulename,
                            $insight->description
                        ];

                        // the actions column is used to hold buttons that link to actions per insight. Note the insight id is added to the href on these buttons.
                        $button_group = Html::b('/insights/viewInsight/' . $insight_class, 'View');
                        if (InsightService::getInstance($w)->isInsightOwner($user_id, $insight_class)) {
                            $button_group .= Html::b(
                                href: '/insights/manageMembers?insight_class=' . $insight_class,
                                title: 'Manage Members',
                                class: 'btn-secondary'
                            );
                        }
                        
                        $row[] = HtmlBootstrap5::buttonGroup($button_group);
                        $table[] = $row;
                    }
                }
            }
        }
    }

    //send the table to the template using ctx
    $w->ctx('insightTable', HtmlBootstrap5::table($table, 'insight_table', 'tablesorter', $tableHeaders));
}
