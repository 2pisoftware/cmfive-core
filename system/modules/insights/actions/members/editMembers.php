<?php

use Html\Form\Select;

/**
 * Provide form by which to add members to an insight
 *
 * @author Alice Hutley <alice@2pisoftware.com>
 */
function editMembers_GET(Web &$w)
{
    //We now need to check if we are adding a new members or editing an existing member
    //We will use pathmatch to retrieve a member id from the url
    $p = $w->pathMatch('id');
    //if the id exists we will retrieve the data for that member. Otherwise we will add a new member
    $member = !empty($p['id']) ? InsightService::getInstance($w)->getMemberForId($p['id']) : new InsightMembers($w);

    //retrieve correct insight to add new member to
    $insight_class_name = !empty($member->id) ? $member->insight_class_name : Request::string('insight_class');

    //action title for adding new member and editing existing member
    $insight = InsightService::getInstance($w)->getInsightInstance($insight_class_name);
    $w->ctx('title', (!empty($p['id']) ? 'Edit member' : 'Add new member') . " for $insight->name");

    // get the list of users and/or groups that can be added to the insight
    $userstoadd = array_filter(AuthService::getInstance($w)->getUsersAndGroups(), function ($u) {
        return $u->hasAnyRole(['insights_user', 'insights_admin']);
    });
    $members = $insight->getMembers($w);

    // strip the dumplicates. dealing with an object so no quick solution
    $users = [];
    foreach ($userstoadd as $user) {
        if (!in_array($user, $users, true)) {
            if (array_search($user->id, array_column($members, 'user_id')) === false) {
                $users[] = $user;
            }
        }
    }
    
    $addMemberForm = [["", "hidden", "insight_class_name", $insight_class_name]];

    if (empty($p['id'])) {
        $addMemberForm[] = (new Select([
                    'id|name' => 'user_id',
                    'label' => 'Add Member',
                    'options' => $users,
                    'required' => true,
                ]));
    } else {
        $user = AuthService::getInstance($w)->getUser($member->user_id);
        $addMemberForm[] = ["Add Member", "text", "-user_id", $user->is_group ?  $user->login : $user->getContact()->getFullName()];
    }
    $addMemberForm[] = (new Select([
        'id|name' => 'type',
        'label' => 'With Role',
        'selected_option' => $member->type,
        'options' => InsightService::getInstance($w)->getInsightPermissions(),
        'required' => true,
    ]));;

    //if we are editing an existing member we need to send the id to the post method
    $postUrl = '/insights-members/editMembers/' . (!empty($member->id) ? $member->id : '');

    // sending the form to the 'out' function bypasses the template.
    $w->out(HtmlBootstrap5::multiColForm([(empty($p['id']) ? "Add new member" : "Edit member") . " for $insight->name" => [$addMemberForm]], $postUrl));
}

function editMembers_POST(Web $w)
{

    //As in the get function we need to check if we are editing an exisiting member
    $p = $w->pathMatch('id');
    $member = !empty($p['id']) ? InsightService::getInstance($w)->GetMemberForId($p['id']) : new InsightMembers($w);

    //use the fill function to fill input field data into properties with matching names
    if (empty($member->id)) {
        $member->fill($_POST);
    } else {
        $member->type = Request::string('type');
    }

    // function for saving to database
    $member->insertOrUpdate();

    // the msg (message) function redirects with a message box
    $w->msg('Member Permissions Saved', '/insights/manageMembers?insight_class=' . $member->insight_class_name);
}
