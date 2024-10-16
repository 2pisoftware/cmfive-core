<?php

use Html\Form\InputField;
use Html\Form\InputField\Checkbox;

// modal to edit application title/description/active status
function edit_GET(Web $w)
{
    $w->setLayout(null);

    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        $w->error('No Application found', '/form-application');
        return;
    }

    /**
     * Required to type $application correctly
     * 
     * @var FormApplication
     **/
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);

    if (empty($application->id)) {
        $w->error('Application not found', '/form-application');
        return;
    }

    $form = [
        'Edit Application' => [
            [
                new InputField(
                    [
                        'label' => 'Title',
                        'id|name' => 'title',
                        'required' => 'required',
                        'value' => $application->title,
                    ]
                ),
                new InputField(
                    [
                        'label' => 'Description',
                        'id|name' => 'description',
                        'value' => $application->description,
                    ]
                ),
            ],
            [
                (
                    new Checkbox(
                        [
                            'label' => 'Active',
                            'id|name' => 'is_active',
                        ]
                    )
                )->setChecked($application->is_active),
            ]
        ]
    ];

    $w->out(HtmlBootstrap5::multiColForm($form, "/form-application/edit/$id", "POST"));
}

function edit_POST(Web $w)
{
    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        $w->error('No Application found', '/form-application');
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

    $is_active = Request::string('is_active');
    $application->title = Request::string('title');
    $application->description = Request::string('description');
    $application->is_active = !($is_active == '') ? 1 : 0;

    $application->update();

    $w->msg('Application updated', "/form-application/manage/$application->id");
}
