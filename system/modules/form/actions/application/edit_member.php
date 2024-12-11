<?php


use Html\Form\Html5Autocomplete;
use Html\Form\Select;
use Html\Form\InputField;

function edit_member_GET(Web $w)
{
    $w->setLayout(null);

    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        $w->error("No Application found", '/form-application');
        return;
    }

    /**
     * Required to type $application correctly
     * 
     * @var FormApplication
     **/
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if (empty($application->id)) {
        $w->error("Application not found", '/form-application');
        return;
    }

    $members = $application->getMembers();
    if (!empty($members)) {
        foreach ($members as $member) {
            $output['data'][] = [
                'id' => $member->id,
                'member_user_id' => $member->member_user_id,
                'name' => $member->getName(),
                'role' => $member->role,
                'application_id' => $member->application_id
            ];
        }
    }

    $memberId = Request::int('member', null);

    /**
     * Required to type $member correctly
     * 
     * @var FormApplicationMember
     */
    $member = empty($memberId) ? null : FormApplicationService::getInstance($w)->getObject("FormApplicationMember", $memberId);

    $form = [
        (!empty($memberId) ? 'Edit member' : "Add a new member") => [
            [
                new Html5Autocomplete(
                    [
                        "label" => "User",
                        "id|name" => "user",
                        "required" => "required",
                        "value" => !empty($member) ? $member->member_user_id : null,
                        "options" => AuthService::getInstance($w)->getUsers(),
                        "placeholder" => "Search for a user",
                        "maxItems" => 1
                    ]
                ),

                (
                    new Select(
                        [
                            "label" => "Role",
                            "id|name" => "role",
                            "required" => "required",
                            "options" => FormApplicationMember::$_roles
                        ]
                    )
                )->setSelectedOption(!empty($member) ? $member->role : null),

                new InputField(
                    [
                        "type" => "hidden",
                        "name" => "member",
                        "value" => $memberId
                    ]
                )
            ]
        ]
    ];

    $w->ctx('form', HtmlBootstrap5::multiColForm($form, "/form-application/edit_member/$id"));
}

function edit_member_POST(Web $w)
{
    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        $w->error("No Application found", '/form-application');
        return;
    }

    /**
     * Required to type $application correctly
     * 
     * @var FormApplication
     */
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if (empty($application->id)) {
        $w->error("Application not found", '/form-application');
        return;
    }

    $userId = Request::int('user', null);
    $memberId = Request::int('member', null);
    $role = Request::string('role', null);

    if (empty($userId) || empty($role)) {
        $w->error("Missing data required to save member", "/form-application/manage/$id");
        return;
    }

    $user = AuthService::getInstance($w)->getUser($userId);
    if (empty($user->id)) {
        $w->error("User not found", "/form-application/manage/$id");
        return;
    }

    if (!in_array($role, FormApplicationMember::$_roles)) {
        $w->error("Invalid role", "/form-application/manage/$id");
        return;
    }

    if (!empty($memberId)) {
        // update member info

        /**
         * Required to type $member correctly
         * 
         * @var FormApplicationMember
         */
        $member = FormApplicationService::getInstance($w)->getObject("FormApplicationMember", $memberId);
        if (empty($member)) {
            $w->error("Member not found", "/form-application/manage/$id");
            return;
        }

        $member->member_user_id = $userId;
        $member->role = $role;
        $member->update();
    } else {
        // check if user is already a member
        $members = $application->getMembers();
        if (!empty($members)) {
            foreach ($members as $member) {
                if ($member->member_user_id == $userId) {
                    $w->error("User is already a member of this application", "/form-application/manage/$id");
                    return;
                }
            }
        }


        // add new member
        $member = new FormApplicationMember($w);
        $member->application_id = $id;
        $member->member_user_id = $userId;
        $member->role = $role;
        $member->insert();
    }

    $w->msg("Member saved", "/form-application/manage/$id");
}