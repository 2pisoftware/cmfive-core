<?php

function ajax_save_form_instance_POST(Web $w)
{
    $w->setLayout(null);
    $request_data = json_decode(file_get_contents("php://input"));

    FormService::getInstance($w)->saveForm($request_data->form_id, $request_data->field_results, [], null, $request_data->object_class, $request_data->object_id);

    $response = (new AxiosResponse())->setSuccessfulResponse("OK", null);
    $w->out($response);
}
