<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

function index_ALL(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    // $w->setLayout('layout-2021');
    $w->ctx("title", "Insights List");

    //get userId for logged in user
    $user_id = AuthService::getInstance($w)->user()->id;

    // access service functions using the Web $w object and the module name
    $modules = InsightService::getInstance($w)->getAllInsights('all');

    // Display a list of all the insights this user can see
    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Name', 'Module', 'Description', 'Actions'];
    if (!empty($modules)) {
        foreach ($modules as $modulename => $insights) {
            if (!empty($insights)) {
                foreach ($insights as $insight) {
                    $userHasAccess = false;
                    $userHasAccess = false;
                    $userHasAccess = false;
                    if (InsightService::getInstance($w)->IsMember(Get_class($insight), $user_id)) {
                        $userHasAccess = true;
                    } else {
                        // check if this user is a member of a group (or parent group) with access to this insight report
                        $allMembers = InsightService::getInstance($w)->getAllMembersForInsightClass(Get_class($insight));
                        foreach ($allMembers as $member) {
                            $userHasAccess = checkUserAccess($w, $member->user_id, $user_id);  // $member->user_id may be a user or a group
                            if ($userHasAccess) {
                                break;
                            };
                        }
                    }
                    if ($userHasAccess) {
                        $row = [];
                        // add values to the row in the same order as the table headers
                        $row[] = HtmlBootstrap5::a('/insights/viewInsight/' . Get_class($insight), $insight->name);
                        $row[] = $modulename;
                        $row[] = $insight->description;
                        // the actions column is used to hold buttons that link to actions per insight. Note the insight id is added to the href on these buttons.
                        $actions = [];
                        $button_group = HtmlBootstrap5::b("/insights/viewInsight/" . Get_class($insight), "View", null, "viewbutton", false, 'btn-sm btn-primary');
                        if (InsightService::getInstance($w)->isInsightOwner($user_id, get_class($insight))) {
                            $button_group .= HtmlBootstrap5::b("/insights/manageMembers?insight_class=" . Get_class($insight), "Manage Members", null, " viewbutton", false, "btn-sm btn-secondary");
                        }
                        $actions[] =  HtmlBootstrap5::buttonGroup($button_group);
                        $row[] = implode('', $actions);
                        $table[] = $row;
                    }
                }
            }
        }
    }

    //send the table to the template using ctx
    $w->ctx('insightTable', HtmlBootstrap5::table($table, 'insight_table', 'tablesorter', $tableHeaders));
}
