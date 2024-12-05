<?php

function delete_form_GET(Web $w)
{
    $w->setLayout(null);

    list($id) = $w->pathMatch('id');
    $form = Request::int('form', null);

    if (empty($id)) {
        $w->error('No Application found', '/form-application');
        return;
    }

    if (empty($form)) {
        $w->error('No Form found', "/form-application/manage/$id");
        return;
    }

    /**
     * Required to type $application correctly
     * 
     * @var FormApplication
     */
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if (empty($application->id)) {
        $w->error('Application not found', '/form-application');
        return;
    }

    /**
     * Required to type $existing_mapping correctly
     * 
     * @var FormApplicationMapping
     */
    $existing_mapping = FormApplicationService::getInstance($w)->getFormApplicationMapping($application->id, $form);
    if (!empty($existing_mapping->id)) {
        $existing_mapping->delete();
    }

    $w->msg('Form removed from application', "/form-application/manage/$id");
}