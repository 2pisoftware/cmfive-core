<?php
/**@author Derek Crannaford */

function info_ALL(Web $w)
{
    ApiOutputService::getInstance($w)->useNoTemplate($w);
    //echo "test | loopback | info";

    $attributes1 = array('text' => "test | loopback | info");
    $data1 = array(  'type'=> "title",
                    'id' => "1",
                    'attributes' => $attributes1,
                    'endpoint' => "tokens-api/info");

    $attributes = array('text' => "A temporary endpoint to verify that we are hitting the tokens-api/info endpoint");
    $data2 = array(  'type'=> "info",
                    'id' => "42",
                    'attributes' => $attributes);

    $response = array('data' => [$data1, $data2]);

    echo json_encode($response);
}
