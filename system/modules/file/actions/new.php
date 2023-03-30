<?php

function new_GET(Web $w)
{
    $redirect_url = Request::string("redirect_url");
    $redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));

    $p = $w->pathMatch("class", "class_id");
    if (empty($p['class']) || empty($p['class_id'])) {
        $w->error("Missing class parameters", $redirect_url);
    }

    $object = FileService::getInstance($w)->getObject($p["class"], $p["class_id"]);
    $users = AuthService::getInstance($w)->getUsers();
    $viewers = [];

    if (!empty($object)) {
        foreach (empty($users) ? [] : $users as $user) {
            if ($user->id === AuthService::getInstance($w)->user()->id) {
                continue;
            }

            if ($object->canView($user)) {
                $viewers[] = [
                    "id" => $user->id,
                    "name" => $user->getFullName(),
                    "can_view" => false
                ];
            }
        }
    }

    usort($viewers, function ($a, $b) {
        return strcmp($a["name"], $b["name"]);
    });

    $w->ctx("redirect_url", WEBROOT . "/" . $redirect_url);
    $w->ctx("class", $p["class"]);
    $w->ctx("class_id", $p["class_id"]);
    $w->ctx("viewers", json_encode($viewers));
    $w->ctx("can_restrict", property_exists(new Attachment($w), "_restrictable") && AuthService::getInstance($w)->user()->hasRole("restrict") ? "true" : "false");
}
