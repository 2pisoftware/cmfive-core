<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $form_id = Request::int("form_id");
    $redirect_url = Request::string("redirect_url");
    $object_class = Request::string("object_class");
    $object_id = Request::int("object_id");

    if (empty($form_id) && empty($p['id'])) {
        $w->msg("Form instance data missing");
        return;
    }

    $instance = null;
    $form = null;
    if (!empty($p['id'])) {
        $instance = FormService::getInstance($w)->getFormInstance($p['id']);
        $form = $instance->getForm();
    } else {
        $form = FormService::getInstance($w)->getForm($form_id);
        $instance = new FormInstance($w);
        $instance->form_id = $form_id;
    }

    $w->out(HtmlBootstrap5::multiColForm(
        $instance->getEditForm($form),
        '/form-instance/edit/' . $instance->id . "?form_id=" . $form_id . "&redirect_url=" . $redirect_url . "&object_class=" . $object_class . "&object_id=" . $object_id
    ));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $form_id = Request::int("form_id");
    $redirect_url = Request::string("redirect_url");
    $object_class = Request::string("object_class");
    $object_id = Request::int("object_id");
    $form = null;

    if (empty($form_id) && empty($p['id'])) {
        $w->msg("Form instance data missing");
        return;
    }

    // Remove CSRF if it exists
    if (array_key_exists(CSRF::getTokenID(), $_POST)) {
        unset($_POST[CSRF::getTokenID()]);
    }

    $form_instance = FormService::getInstance($w)->saveForm($form_id, $_POST, $_FILES, $p['id'], $object_class, $object_id);

    if (empty($p["id"])) {
        $w->callHook("form", "after_create_form", $form_instance);
    }

    $form = $form_instance->getForm();

    $w->msg($form->title . (!empty($p['id']) ? " updated" : " created"), $redirect_url . "#" . toSlug($form->title));
}
