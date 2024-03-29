<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $_form_object = $p['id'] ? FormService::getInstance($w)->getForm($p['id']) : new Form($w);

    $form = [
        "Form" => [
            [
                ["Title", "text", "title", $_form_object->title],
            ],
            [
                ["Description", "text", "description", $_form_object->description],
            ],
        ]
    ];

    $validation = ['title' => ['required']];

    $w->out(Html::multiColForm($form, '/form/edit/' . $_form_object->id, "POST", "Save", null, null, null, "_self", true, $validation));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $_form_object = $p['id'] ? FormService::getInstance($w)->getForm($p['id']) : new Form($w);

    $_form_object->fill($_POST);

    $_form_object->insertOrUpdate();

    $redirect_url = Request::string("redirect_url");
    $w->msg("Form " . ($p['id'] ? 'updated' : 'created'), !empty($redirect_url) ? $redirect_url : "/form");
}
