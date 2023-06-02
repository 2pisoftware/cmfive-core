<?php
/**@author Alice Hutley <alice@2pisoftware.com> */

function deleteMembers_ALL(Web $w)
{

    //retrieve correct insight to delete member from and redirect to
    $insight_class_name = Request::string('insight_class');

    // start by finding the member id included in the URL
    $p = $w->pathMatch('id');
    // check to see if the id has been found
    if (empty($p['id'])) {
        // if no id found use the 'error' function to redirect the user to a safe page and display a message.
        $w->error('No id found for member', '/insights/manageMembers');
    }
    // use the id to retrieve the member
    $member = InsightService::getInstance($w)->GetMemberForId($p['id']);
    // check to see if the member was found
    if (empty($member)) {
        // no member found so let the user know
        $w->error('No member found for id', '/insights/manageMembers');
        LogService::getInstance($w)->setLogger("INSIGHTS")->error("No member found for that id", "/insights/manageMembers");
    }
    // delete the member
    $member->delete();
    // redirect the user back to the Member list with a message
    $w->msg('Member deleted', '/insights/manageMembers?insight_class='.$member->insight_class_name);
}
