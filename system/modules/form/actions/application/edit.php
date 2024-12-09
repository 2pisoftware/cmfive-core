<?php

use Html\Form\InputField;
use Html\Form\InputField\Checkbox;

// modal to edit application title/description/active status
function edit_GET(Web $w)
{
    $w->setLayout(null);

    list($id) = $w->pathMatch('id');

    /**
     * Required to type $application correctly
     * 
     * @var FormApplication
     **/
    $application = FormApplicationService::getInstance($w)->getFormApplication($id);
    $is_new = empty($application->id);

    $form = [
        ($is_new ? 'Create' : 'Edit') . ' Application' => [
            [
                new InputField(
                    [
                        'label' => 'Title',
                        'id|name' => 'title',
                        'required' => 'required',
                        'value' => $is_new ? '' : $application->title,
                    ]
                ),
                new InputField(
                    [
                        'label' => 'Description',
                        'id|name' => 'description',
                        'value' => $is_new ? '' : $application->description,
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
                )->setChecked($is_new ? false : $application->is_active),

                new InputField(
                    [
                        'type' => 'hidden',
                        'id|name' => 'is_new',
                        'value' => empty($application->id) ? 'true' : 'false',
                    ]
                ),
            ]
        ]
    ];

    $w->out(HtmlBootstrap5::multiColForm($form, "/form-application/edit/$id", "POST"));
}

function edit_POST(Web $w)
{
    if (Request::string('is_new') === 'true') {
        $application = new FormApplication($w);

        $application->title = Request::string('title');
        $application->description = Request::string('description');
        $application->is_active = !(Request::string('is_active') == '') ? 1 : 0;

        $application->insert();
        $w->msg('Application created', "/form-application/manage/$application->id");
        return;
    }

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

    $application->title = Request::string('title');
    $application->description = Request::string('description');
    $application->is_active = !(Request::string('is_active') == '') ? 1 : 0;

    $application->update();

    $w->msg('Application updated', "/form-application/manage/$application->id");
}
