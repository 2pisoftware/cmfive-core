<?php

use Html\Form\Html5Autocomplete;

function attach_form_GET(Web $w)
{
    $w->setLayout(null);

    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        $w->out(HtmlBootstrap5::alertBox("No Application found", "alert-warning", false));
        return;
    }

    /**
     * Required to type $application correctly
     * 
     * @var FormApplication
     * */
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if (empty($application->id)) {
        $w->out(HtmlBootstrap5::alertBox("Application not found", "alert-warning", false));
        return;
    }

    $available_forms = FormService::getInstance($w)->getForms();

    if (empty($available_forms)) {
        $w->out(HtmlBootstrap5::alertBox("There are no forms to attach", "alert-warning", false));
        return;
    }

    $form = [
        'Attach Form' => [
            [
                new Html5Autocomplete(
                    [
                        "label" => "Form",
                        "id|name" => "form",
                        "required" => "required",
                        "options" => $available_forms,
                        "value" => empty($form) ? null : $form->form_id,
                        "placeholder" => "Search for a form",
                        "maxItems" => 1
                    ]
                )
            ]
        ]
    ];

    $w->out(HtmlBootstrap5::multiColForm($form, "/form-application/attach_form/$id", "POST"));
}

function attach_form_POST(Web $w)
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
     * */
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if (empty($application->id)) {
        $w->error("Application not found", '/form-application');
        return;
    }

    $form_id = Request::int('form', null);

    if (empty($form_id)) {
        $w->error("No form selected", "/form-application/manage/$id");
        return;
    }

    $form = FormService::getInstance($w)->getForm($form_id);

    if (empty($form->id)) {
        $w->error("Form not found", "/form-application/manage/$id");
        return;
    }

    $existing_mapping = FormApplicationService::getInstance($w)->getFormApplicationMapping($id, $form_id);
    if (empty($existing_mapping->id)) {
        $mapping = new FormApplicationMapping($w);
        $mapping->application_id = $id;
        $mapping->form_id = $form_id;

        if (array_key_exists("type", $_POST) && $_POST["type"] === "single") {
            $mapping->is_singleton = true;
        }

        $mapping->insert();
    } else {
        $w->error("Form already attached", "/form-application/manage/$id");
        return;
    }

    $w->msg("Form attached", "/form-application/manage/$id");
}