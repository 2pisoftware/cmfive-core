<?php

use Html\Form\InputField;
use Html\Form\Textarea;
use Html\Form\InputField\Checkbox;

function edit_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');

    list($id) = $w->pathMatch('id');

    $w->enqueueScript(['name' => 'vue-js', 'uri' => '/system/templates/js/vue.js', 'weight' => 200]);

    $application = null;
    if (empty($id)) {
        $application = new FormApplication($w);
        $application->insert();
        $w->redirect('/form-application/edit/' . $application->id);
    } else {
        $application = FormService::getInstance($w)->getFormApplication($id);
    }

    $w->ctx(
        'form', 
        HtmlBootstrap5::multiColForm(
            [
                "Application" => [
                    [new InputField(
                        [
                            'id|name' => 'title',
                            'label' => 'Title',
                            'value' => $application->title,
                            'required' => true
                        ]
                    )],
                    [new Textarea(
                        [
                            'id|name' => 'description',
                            'label' => 'Description',
                            'value' => $application->description
                        ]
                    )],
                    [new Checkbox(
                        [
                            'id|name' => 'is_active',
                            'label' => 'Active',
                            'checked' => $application->is_active
                        ]
                    )]
                ]
            ],
            "/form-application/edit/$application->id"
        )
    );

    $available_forms = FormService::getInstance($w)->getForms();

    $w->ctx('available_forms', $available_forms);
    $w->ctx('application', $application);
    $w->ctx('new_application', !empty($application->id));
    // $w->ctx('form', HtmlBootstrap5::multiColForm($form, '/form-application/edit/' . $application->id));
}

function edit_POST(Web $w)
{
    $w->setLayout(null);
    list($id) = $w->pathMatch('id');

    $application = !empty($id) ? FormService::getInstance($w)->getFormApplication($id) : new FormApplication($w);
    $application->fill($_POST);
    $application->is_active = !empty($_POST['is_active']);
    $application->insertOrUpdate();

    $w->msg('Application ' . (!empty($id) ? 'updated' : 'created'), '/form-application/show/' . $application->id);
}
