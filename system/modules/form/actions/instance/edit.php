<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $form_id = $w->request("form_id");
    $redirect_url = $w->request("redirect_url");
    $object_class = $w->request("object_class");
    $object_id = $w->request("object_id");

    if (empty($form_id) && empty($p['id'])) {
        $w->msg("Form instance data missing");
        return;
    }

    $instance = null;
    $form = null;
    if (!empty($p['id'])) {
        $instance = $w->Form->getFormInstance($p['id']);
        $form = $instance->getForm();
    } else {
        $form = $w->Form->getForm($form_id);
        $instance = new FormInstance($w);
        $instance->form_id = $form_id;
    }

    $w->out(Html::multiColForm(
        $instance->getEditForm($form),
        '/form-instance/edit/' . $instance->id . "?form_id=" . $form_id . "&redirect_url=" . $redirect_url . "&object_class=" . $object_class . "&object_id=" . $object_id
    ));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $form_id = $w->request("form_id");
    $redirect_url = $w->request("redirect_url");
    $object_class = $w->request("object_class");
    $object_id = $w->request("object_id");
    $form = null;
    if (empty($form_id) && empty($p['id'])) {
        $w->msg("Form instance data missing");
        return;
    }

    // Remove CSRF if it exists
    if (array_key_exists(CSRF::getTokenID(), $_POST)) {
        unset($_POST[CSRF::getTokenID()]);
    }

    try {
        $form_instance = $w->Form->saveForm($form_id, $_POST, $_FILES, $p['id'], $object_class, $object_id);
    } catch (Exception $e) {
        echo "<pre>";
        var_dump($e);
        die;
    }
    $form = $form_instance->getForm();

    $w->msg($form->title . (!empty($p['id']) ? " updated" : " created"), $redirect_url . "#" . toSlug($form->title));
}
