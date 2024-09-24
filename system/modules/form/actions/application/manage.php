<?php

function manage_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    list($id) = $w->pathMatch('id');

    $application = null;
    if (empty($id)) {
        $application = new FormApplication($w);
        $application->insert();
        $w->redirect('/form-application/edit/' . $application->id);
    } else {
        $application = FormService::getInstance($w)->getFormApplication($id);
    }

    $available_forms = FormService::getInstance($w)->getForms();

    $w->ctx('application', $application);
    $w->ctx('members', $application->getMembers());
    $w->ctx('available_forms', $available_forms);
}