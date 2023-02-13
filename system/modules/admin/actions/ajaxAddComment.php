<?php

function ajaxAddComment_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"));
    if (empty($request_data)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing comment data"]));
        return;
    }

    $user = AuthService::getInstance($w)->user();
    if ($request_data->is_restricted && !$user->hasRole("restrict")) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
        return;
    }

    $comment = null;
    if (!empty($request_data->comment_id)) {
        $comment = CommentService::getInstance($w)->getComment($request_data->comment_id);
    }

    $is_new = false;

    if (empty($comment)) {
        $comment = new Comment($w);
        $comment->is_internal = $request_data->is_internal_only;
        $is_new = true;
    } else {
        $current_viewers = RestrictableService::getInstance($w)->getViewerLinks($comment);
        foreach (empty($current_viewers) ? [] : $current_viewers as $current_viewer) {
            $current_viewer->delete();
        }
    }

    $top_object_table_name = $request_data->top_object_class_name;
    $top_object_id = $request_data->top_object_id;

    $comment->obj_table = $top_object_table_name;
    $comment->obj_id = $request_data->top_object_id;
    $comment->comment = strip_tags($request_data->comment ?? "");
    $comment->creator_id = $request_data->new_owner->id;
    $comment->insertOrUpdate();


    if ($request_data->is_restricted) {
        RestrictableService::getInstance($w)->setOwner($comment, $request_data->new_owner->id);

        foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
            if ($viewer->id == AuthService::getInstance($w)->user()->id) {
                continue;
            }

            if ($viewer->can_view) {
                RestrictableService::getInstance($w)->addViewer($comment, $viewer->id);
            }
        }
    } elseif (!$request_data->is_restricted && RestrictableService::getInstance($w)->isRestricted($comment)) {
        RestrictableService::getInstance($w)->unrestrict($comment);
    }

    if ($top_object_table_name === "comment") {
        $top_object = CommentService::getInstance($w)->getComment($top_object_id)->getParentObject();
        $top_object_table_name = $top_object->getDbTableName();
        $top_object_id = $top_object->id;
    }

    $notify_recipient_ids = [];
    foreach ($request_data->viewers as $viewer) {
        if ($request_data->is_restricted) {
            if ($viewer->is_notify && $viewer->can_view) {
                $notify_recipient_ids[] = $viewer->id;
            }
            continue;
        }

        if ($viewer->is_notify) {
            $notify_recipient_ids[] = $viewer->id;
        }
    }

    $w->callHook("comment", "send_notification_recipients_" . $top_object_table_name, [
        "object_id" => $top_object_id,
        "recipients" => $notify_recipient_ids,
        "commenter_id" => $user->id,
        "comment" => $comment,
        "is_new" => $is_new,
    ]);

    $w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}
