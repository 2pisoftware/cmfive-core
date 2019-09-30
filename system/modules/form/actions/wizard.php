<?php

function wizard_GET(Web $w)
{
    list($form_id) = $w->pathMatch("id");
    if (empty($form_id)) {
        return;
    }

    $form = $w->Form->getForm($form_id);
    $w->ctx("form", $form);
}
