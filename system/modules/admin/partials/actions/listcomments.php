<?php

namespace System\Modules\Admin;

use CmfiveScriptComponent;
use CmfiveScriptComponentRegister;

function listcomments(\Web $w, $params)
{
    CmfiveScriptComponentRegister::registerComponent("commentsModal", new CmfiveScriptComponent("/system/templates/base/dist/commentsModal.js", ["weight" => "200", "type" => "module"]));

    $object = $params['object'];
    $redirect = $params['redirect'];
    $internal_only = array_key_exists('internal_only', $params) ? $params['internal_only'] : true;
    $external_only = $internal_only === true ? false : (array_key_exists('external_only', $params) ? $params['external_only'] : false);
    $has_notification_selection = array_key_exists('has_notification_selection', $params) ? $params["has_notification_selection"] : true;

    $comments = \CommentService::getInstance($w)->getCommentsForTable($object->getDbTableName(), $object->id, $internal_only, $external_only);
    $user = \AuthService::getInstance($w)->user();
    foreach (empty($comments) ? [] : $comments as $key => $comment) {
        if (!$comment->canView($user)) {
            unset($comments[$key]);
        }
    }

    $w->ctx("comments", $comments);
    $w->ctx("internal_only", $internal_only);
    $w->ctx("external_only", $external_only);
    $w->ctx("redirect", $redirect);
    $w->ctx("object", $object);
    $w->ctx("has_notification_selection", $has_notification_selection);

    //get recipients for comment notifications
    $get_recipients = $w->callHook('comment', 'get_notification_recipients_' . $object->getDbTableName(), ['object_id' => $object->id, 'internal_only' => $internal_only]);
    //add checkboxes to the form for each notification recipient
    $recipients_form_html = '';
    if (!empty($get_recipients)) {
        $unique_recipients = [];
        foreach ($get_recipients as $recipients) {
            foreach ($recipients as $user_id => $is_notify) {
                if (!array_key_exists($user_id, $unique_recipients)) {
                    $unique_recipients[$user_id] = $is_notify;
                } else {
                    if ($is_notify != $unique_recipients[$user_id]) {
                        $unique_recipients[$user_id] = 1;
                    }
                }
            }
        }
        $recipients_form_html .= '<h4>Notifications</h4><input type="hidden" name="is_notifications" value="1" id="is_notifications"><div id="' . ($internal_only ? 'internal' : 'external') . '_notifications_list"><ul class="small-block-grid-1 medium-block-grid-4 section-body">';

        foreach ($unique_recipients as $user_id => $is_notify) {
            $user = \AuthService::getInstance($w)->getUser($user_id);
            if (!empty($user)) {
                if ($internal_only === true && $user->is_external == 0) {
                    $recipients_form_html .= '<li><label classs="small-12 columns">' . addcslashes($user->getFullName(), '\'') . ' <input type="checkbox" name="recipient_' . $user->id . '" value="1" ';
                    $recipients_form_html .= $user->id != \AuthService::getInstance($w)->loggedIn() && $is_notify == 1 ? 'checked="checked"' : '';
                    $recipients_form_html .= 'id="recipient_' . $user_id . '" class=""></label></li>';
                } else {
                    if ($internal_only === false) {
                        $recipients_form_html .= '<li><label class="small-12 columns">' . addcslashes($user->getFullName(), '\'') . ($user->is_external == 1 ? ' (external)' : '') . ' <input type="checkbox" name="recipient_' . $user->id . '" value="1" ';
                        $recipients_form_html .= $user->id != \AuthService::getInstance($w)->loggedIn() && $is_notify == 1 ? 'checked="checked"' : '';
                        $recipients_form_html .= 'id="recipient_' . $user_id . '" class=""></label></li>';
                    }
                }
            }
        }
        $recipients_form_html .= '</ul></div>';
    }
    $w->ctx('recipients_html', $recipients_form_html);
}
