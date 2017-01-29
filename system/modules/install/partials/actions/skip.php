<?php

function skip_ALL(Web $w, $params)
{
    $skip_clazz = !empty($params['clazz']) ? $params['clazz'] : 'secondary';
    $skip_button_text = !empty($params['button']) ? $params['button'] : "Skip";
    $skip_step = !empty($params['skip']) ? $params['skip'] : 1;
    $skip_step_name = $w->Install->findInstallStepName($skip_step);
    if(empty($skip_step_name))
    {
        $skip_step_name = 'general';
        $skip_step = 1;
    }
    
    $w->ctx("skip_button_text", $skip_button_text);
    $w->ctx("skip_step", $skip_step);
    $w->ctx("skip_step_name", $skip_step_name);
    $w->ctx("skip_clazz", $skip_clazz);
}