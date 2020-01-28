<?php
/**
 * This role is called when no user is logged in!
 *
 * Control access by IP, Module or Action in the
 * global /config.php using the global parameters provided.
 *
 * $ALLOW_FROM_IP
 *
 * $ALLOW_ACTION
 *
 * $ALLOW_MODULE
 *
 * @param Web $w
 * @param string $path
 * @return bool
 */
function anonymous_allowed(Web $w, string $path): bool
{
    // array("127.0.0.1" => array("action1","action2", ...), ...)
    $allow_from_ip = Config::get('system.allow_from_ip');
    if (!empty($allow_from_ip)) {
        if (array_key_exists($w->requestIpAddress(), $allow_from_ip) && in_array($path, $allow_from_ip[$w->requestIpAddress()])) {
            return true;
        }
    }
    $in_path = false;
    if (is_array(Config::get('system.allow_action'))) {
        $in_path = in_array($path, Config::get('system.allow_action'));
    }
    $allowed = false;

    if (strlen(trim($path)) > 0) {
        $path_explode = explode("/", $path);
        $module = $path_explode[0];
        // $action = $path_explode[1];
        if (is_array(Config::get('system.allow_module'))) {
            $allowed = in_array($module, Config::get('system.allow_module'));
        }
    }
    return $allowed || $in_path;
}
