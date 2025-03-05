<?php

function multipart_uploader_ui(Web $w, array $params)
{
    CmfiveScriptComponentRegister::registerComponent(
        "MultipartUploaderComponent",
        new CmfiveScriptComponent(
            "/system/templates/base/dist/MultipartUploaderComponent.js",
            ["weight" => "200", "type" => "module"]
        )
    );

    $w->ctx("endpoint", empty($params["endpoint"]) ? null : $params["endpoint"]);
}
