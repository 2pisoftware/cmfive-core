<?php

function ajax_regeneratecomposerjson_GET(Web $w)
{
    $w->setLayout(null);

    // Collect dependencies
    $dependencies_array = [];
    foreach ($w->modules() as $module) {
        $dependencies = Config::get("{$module}.dependencies");
        if (!empty($dependencies)) {
            $dependencies_array = array_merge($dependencies, $dependencies_array);
        }
    }

    $json_obj = [];
    $json_obj["config"] = [];
    $json_obj["config"]["vendor-dir"] = 'composer/vendor';
    $json_obj["config"]["cache-dir"] = 'composer/cache';
    $json_obj["config"]["bin-dir"] = 'composer/bin';
    $json_obj["require"] = $dependencies_array;

    // Create the JSON file
    file_put_contents(SYSTEM_PATH . "/composer.json", json_encode($json_obj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

    echo '{}';
}
