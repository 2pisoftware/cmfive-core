<?php

function test_GET(Web $w)
{
    list($form_id) = $w->pathMatch("id");
    $form = $w->Form->getForm($form_id);
    $w->ctx("form", $form);
}
