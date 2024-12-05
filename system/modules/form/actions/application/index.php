<?php

function index_GET(Web $w)
{
    $w->ctx('title', 'Form Applications');
    $applications = FormService::getInstance($w)->getFormApplications();

    $application_table_data = [];
    if (!empty($applications)) {
        foreach ($applications as $application) {
            $application_table_data[] = [
                HtmlBootstrap5::a("/form-application/show/$application->id", $application->title, "View application $application->title", "text-break"),
                '<span class="text-break">' . $application->description . '</span>',
                $application->is_active == 1 ? 'Active' : 'Inactive',
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/form-application/manage/$application->id", 'Manage', class: "btn-primary") .
                        HtmlBootstrap5::b("/form-application/export/$application->id", 'Export', class: "btn-secondary") .
                        HtmlBootstrap5::b("/form-application/delete/$application->id", 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', null, false, class: "btn-danger")
                )
            ];
        }
    }

    $w->ctx('application_table_header', ['Title', 'Description', 'Active', 'Actions']);
    $w->ctx('application_table_data', $application_table_data);
}
