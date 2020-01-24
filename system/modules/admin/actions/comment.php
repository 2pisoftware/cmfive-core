<?php

function comment_GET(Web $w){
    $p = $w->pathMatch("comment_id", "object_class", "object_id");
    $is_internal_only = intval($w->request("internal_only", 0));

    $comment_id = intval($p["comment_id"]);
    $comment = $comment_id > 0 ? $w->Comment->getComment($comment_id) : new Comment($w);

    $is_restricted = false;
    $is_parent_restricted = false;

    // Setup for comment notifications.
    $parent_object_class_name = $p["object_class"];
    $parent_object_id = $p["object_id"];
    $root_object = null;
    $parent_comment = null;

    if (strtolower($parent_object_class_name) == "comment") {
        $parent_comment = $w->Comment->getComment($p["object_id"]);
        if (!empty($parent_comment)) {
            $root_object = $parent_comment->getParentObject();
        }

        if ($parent_comment->isRestricted()) {
            $is_parent_restricted = true;
        }
    } else {
        $root_object = $w->Comment->getObject(str_replace(" ", "", ucwords(str_replace("_", " ", $parent_object_class_name))), $parent_object_id);
    }

    if ($is_parent_restricted || (!empty($comment->id) && $comment->isRestricted())) {
        $is_restricted = true;
    }

    $get_recipients = $w->callHook("comment", "get_notification_recipients_" . $root_object->getDbTableName(), ["object_id" => $root_object->id, "internal_only" => $is_internal_only === 1 ? true : false]);

    $notify_recipients = [];

    // Add checkboxes to the form for each notification recipient.
    if (!empty($get_recipients)) {
        foreach($get_recipients as $recipients) {
            foreach ($recipients as $user_id => $is_notify) {

                if(!array_key_exists($user_id, $notify_recipients)) {
                    $notify_recipients[$user_id] = ["is_notify" => $is_notify];
                } else {
                    if ($is_notify != $notify_recipients[$user_id]) {
                        $notify_recipients[$user_id] = ["is_notify" => true];
                    }
                }
            }
        }
    }

    $viewers = [];

    if (!empty($root_object) && $is_internal_only) {
        $users = $w->Auth->getUsers();

        foreach (empty($users) ? [] : $users as $user) {
            $link = $w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $comment->id, "user_id" => $user->id, "type" => "viewer"]);

            if ($root_object->canView($user)) {
                if (!empty($parent_comment) && !$parent_comment->canView($user)) {
                    continue;
                }
                $is_notify = false;

                foreach ($notify_recipients as $key => $notify_recipient) {
                    if ($key == $user->id && !$is_restricted && $w->Auth->user()->id != $user->id) {
                        $is_notify = true;
                    }
                }

                $viewers[] = [
                    "id" => $user->id,
                    "name" => $user->getFullName(),
                    "can_view" => (!empty($link) || $user->id === $w->Auth->user()->id) ? true : false,
                    "is_notify" => $is_notify,
                    "is_original_notify" => $is_notify
                ];
            }
        }
    } else {
        $users = $w->Admin->getObjects("User");
        $notify_recipients[$w->Auth->user()->id] = false;

        foreach (empty($users) ? [] : $users as $user) {
            foreach ($notify_recipients as $key => $notify_recipient) {
                if ($key == $user->id && !$is_restricted) {
                    $viewers[] = [
                        "id" => $user->id,
                        "name" => $user->getFullName() . ($user->is_external ? " (EXTERNAL)" : ""),
                        "can_view" => true,
                        "is_notify" => $w->Auth->user()->id != $user->id ? true : false,
                        "is_original_notify" => empty($is_notify) ? null : $is_notify,
                    ];
                }
            }
        }
    }

    usort($viewers, function($a, $b) {
        return strcmp($a["name"], $b["name"]);
    });

    $user = $w->Auth->user();
    $new_owner = [
        "id" => $user->id,
        "name" => $user->getFullName()
    ];

    // make sure line breaks are escape for correct processing in js
    $comment_text = str_replace("\n", "\\n", $comment->comment);
    $w->ctx("comment", $comment_text);

    $w->ctx("comment_id", $p["comment_id"] == "{0}" ? "0" : $p["comment_id"]);
    $w->ctx("viewers", json_encode($viewers));
    $w->ctx("top_object_class_name", strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $parent_object_class_name)));
    $w->ctx("top_object_id", $parent_object_id);
    $w->ctx("new_owner", json_encode($new_owner));
    $w->ctx("is_new_comment", empty($p["comment_id"]) || $p["comment_id"] == 0 ? "true" : "false");
    $w->ctx("is_internal_only", $is_internal_only);
    $w->ctx("is_restricted", json_encode($is_restricted));
    $w->ctx("is_parent_restricted", json_encode($is_parent_restricted));
    $w->ctx("can_restrict", property_exists($comment, '_restrictable') && $is_internal_only && $w->Auth->user()->hasRole("restrict") ? "true" : "false");
}