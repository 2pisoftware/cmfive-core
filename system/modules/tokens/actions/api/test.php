<?php
/**@author Derek Crannaford */

function test_ALL(Web $w)
{
    ApiOutputService::getInstance($w)->useNoTemplate($w);
    //echo "A B C";

    $attributes = array('text' => "A B C");
    $data = array(  'type'=> "title",
                    'id' => "1",
                    'attributes' => $attributes,
                    'endpoint' => "tokens-api/test");

    $response = array('data' => [$data]);

    echo json_encode($response);
}
