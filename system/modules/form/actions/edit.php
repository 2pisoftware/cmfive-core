<?php

function edit_GET(Web $w)
{
    $w->setLayout(null);

    list($form_id) = $w->pathMatch("form_id");
    $_form_object = $form_id ? FormService::getInstance($w)->getForm($form_id) : new Form($w);

    $form = [
        "Form" => [
            [
                ["Title", "text", "title", StringSanitiser::sanitise($_form_object->title)],
            ],
            [
                ["Description", "text", "description", StringSanitiser::sanitise($_form_object->description)],
            ],
        ]
    ];

    $validation = ['title' => ['required']];

    $w->out(HtmlBootstrap5::multiColForm(
        data: $form,
        action: '/form/edit/' . $_form_object->id,
        method: "POST",
        submitTitle: "Save",
        target: "_self",
        includeFormTag: true,
        validation: $validation
    ));
}

function edit_POST(Web $w)
{
    list($form_id) = $w->pathMatch("form_id");
    $_form_object = $form_id ? FormService::getInstance($w)->getForm($form_id) : new Form($w);

    $_form_object->fill($_POST);

    $_form_object->insertOrUpdate();

    $redirect_url = Request::string("redirect_url");
    $w->msg("Form " . ($form_id ? 'updated' : 'created'), !empty($redirect_url) ? $redirect_url : "/form");
}
