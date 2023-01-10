<?php

function ajax_set_setting_GET(Web $w)
{
    $w->setLayout(null);

    $key = Request::string('key');
    $value = Request::string('value');

    $existing_setting = AuthService::getInstance($w)->getSettingByKey($key);

    if (!empty($existing_setting->id)) {
        $existing_setting->setting_value = $value;
        $existing_setting->update();

        $w->out((new JsonResponse())->setSuccessfulResponse('Setting updated', $existing_setting->toArray()));
    } else {
        $setting = new UserSetting($w);
        $setting->user_id = AuthService::getInstance($w)->user()->id;
        $setting->setting_key = $key;
        $setting->setting_value = $value;
        $setting->insert();
        
        $w->out((new JsonResponse())->setSuccessfulResponse('Setting set', $setting->toArray()));
    }
}
