<?php

function edit_member_GET(Web $w)
{
    $w->setLayout(null);

    list($id) = $w->pathMatch('id');

    if(empty($id)) {
        $w->error("No Application found", '/form-application');
        return;
    }

    /** @var FormApplication */
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if(empty($application->id)) {
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

    $memberId = Request::int('member', count($members));

    $w->ctx('memberId', $memberId);
}