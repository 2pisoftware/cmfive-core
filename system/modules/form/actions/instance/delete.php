<?php

function delete_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $redirect_url = Request::string("redirect_url");

    if (empty($p['id'])) {
        $w->error("Form instance not found", $redirect_url . "#" . toSlug($form->title));
        return;
    }

    $instance = FormService::getInstance($w)->getFormInstance($p['id']);
    if (empty($instance->id)) {
        $w->error("Form instance not found", $redirect_url . "#" . toSlug($form->title));
        return;
    }
    $form = FormService::getInstance($w)->getForm($instance->form_id);
    $instance->delete();
    $w->msg("Form instance deleted", $redirect_url . "#" . toSlug($form->title));
}
