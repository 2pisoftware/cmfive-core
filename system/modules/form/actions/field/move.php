<?php

function move_POST(Web $w)
{
    $w->setLayout(null);
    list($form_id) = $w->pathMatch();

    $form = FormService::getInstance($w)->getForm($form_id);

    if (empty($form->id)) {
        return;
    }

    $fields = $form->getFields();

    $input = json_decode(file_get_contents('php://input'));
    $ordering = $input->ordering;
    if (empty($fields) || empty($ordering)) {
        return;
    }


    foreach ($ordering as $order_index => $order) {
        foreach ($fields as $index => $field) {
            if ($field->id == $order) {
                $field->ordering = $order_index;
                $field->update();
            }
        }
    }

    $w->out("OK");
}
