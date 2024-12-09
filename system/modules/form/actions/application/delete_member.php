<?php

function delete_member_GET(Web $w) {
    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        $w->error("No Application found", '/form-application');
        return;
    }

    $memberId = Request::int('member', null);
    if (empty($memberId)) {
        $w->error("No member found", "/form-application/manage/$id");
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

    if (!empty($member->id) && $member->application_id == $application->id && $member->is_deleted == 0) {
        $member->delete();
    }

    $w->msg("Member deleted", "/form-application/manage/$id");
}