<?php

function comment_GET(Web $w){
    $p = $w->pathMatch("comment_id", "tablename", "object_id");
    $is_internal_only = intval($w->request('internal_only', 0));
    // $redirect_url = $w->request('redirect_url', $w->localUrl($_SERVER["REQUEST_URI"]));

    $comment_id = intval($p["comment_id"]);
    $comment = $comment_id > 0 ? $w->Comment->getComment($comment_id) : new Comment($w);
    if ($comment === null){
        $comment = new Comment($w);
    }

//     $help =<<<EOF
// //italics//
// **bold**

// * bullet list
// * second item
// ** subitem

// # numbered list
// # second item
// ## sub item

// [[URL|linkname]]

// == Large Heading
// === Medium Heading
// ==== Small Heading

// Horizontal Line:
// ---
// EOF;

    // Setup for comment notifications.
    $top_object_table_name = $p['tablename'];
    $top_object_id = $p['object_id'];

    if ($top_object_table_name == 'comment') {
        $top_object = $w->Comment->getComment($p['object_id'])->getParentObject();
        $top_object_table_name = $top_object->getDbTableName();
        $top_object_id = $top_object->id;
    }

    // $form = [
    //     'Comment'=> [
    //         [
    //             array("", "textarea", "comment", $comment->comment, 100, 15, false)
    //         ]
    //     ],
    //     'Help'=> [
    //         [
    //             array("", "textarea", "-help",$help, 100, 5, false)
    //         ],
    //         [
    //             array("", "hidden", "redirect_url", $w->request("redirect_url"))
    //         ]
    //     ]
    // ];

    if (!$p["comment_id"]) {
        // Call hook for notification select.
        $get_recipients = $w->callHook('comment', 'get_notification_recipients_' . $top_object_table_name, ['object_id' => $top_object_id, 'internal_only' => $is_internal_only === 1 ? true : false]);

        // Add checkboxes to the form for each notification recipient.
        if (!empty($get_recipients)) {
            $unique_recipients = [];
            foreach($get_recipients as $recipients) {
                foreach ($recipients as $user_id => $is_notify) {
                    if ($user_id == $w->Auth->user()->id) {
                        continue;
                    }

                    if(!array_key_exists($user_id, $unique_recipients)) {
                        $recipient = $w->Auth->getUser($user_id);
                        $unique_recipients[$user_id] = ["is_notify" => $is_notify, "name" => empty($recipient) ? "" : $recipient->getFullName()];
                    } else {
                        if ($is_notify != $unique_recipients[$user_id]) {
                            $recipient = $w->Auth->getUser($user_id);
                            $unique_recipients[$user_id] = ["is_notify" => true, "name" => empty($recipient) ? "" : $recipient->getFullName()];
                        }
                    }
                }
            }

            $w->ctx("notify_recipients", json_encode($unique_recipients));

            // $form["Notifications"] = [
            //     [
            //         array("", "hidden", "is_notifications", 1)
            //     ]
            // ];
            // $parts = array_chunk($unique_recipients, 4, true);

            // foreach ($parts as $key => $row) {
            //     $form['Notifications'][$key + 1] = [];

            //     foreach ($row as $user_id => $is_notify) {
            //         $user = $w->Auth->getUser($user_id);
            //         if (!empty($user)) {
            //             $form['Notifications'][$key + 1][] = array($user->getFullName() . ($user->is_external == 1 ? ' (external)' : ''), 'checkbox', 'recipient_' . $user->id, $is_notify);
            //         }
            //     }
            // }
        }
    }

    $top_object = $w->Admin->getObject($top_object_table_name, $top_object_id);
    $users = $w->Auth->getUsers();
    $viewers = [];

    if (!empty($top_object)) {
        foreach (empty($users) ? [] : $users as $user) {
            if ($user->id === $w->Auth->user()->id) {
                continue;
            }

            $link = $w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $comment->id, "user_id" => $user->id, "type" => "viewer"]);

            if ($top_object->canView($user)) {
                $viewers[] = [
                    "id" => $user->id,
                    "name" => $user->getFullName(),
                    "can_view" => empty($link) ? false : true
                ];
            }
        }
    }

    //$form = Html::MultiColForm($form, $w->localUrl("/admin/comment/{$comment_id}/{$p["tablename"]}/{$p["object_id"]}?internal_only=" . $internal_only) . "&redirect_url=" . $redirect_url, "POST", "Save");
    //$w->ctx("form", $form);

    $w->ctx("comment", $comment->comment);
    $w->ctx("comment_id", $p["comment_id"]);
    $w->ctx("viewers", json_encode($viewers));
    $w->ctx("top_object_table_name", $top_object_table_name);
    $w->ctx("top_object_id", $top_object_id);
    $w->ctx("is_new_comment", empty($p["comment_id"]) || $p["comment_id"] == 0 ? "true" : "false");
    $w->ctx("is_internal_only", $is_internal_only);
    $w->ctx("is_restricted", !empty($comment->id) ? $comment->isRestricted() : false);
    $w->ctx("can_restrict", Comment::$_restrictable && $w->Auth->user()->hasRole("restrict") ? "true" : "false");
}

// function comment_POST(Web $w){
//     $p = $w->pathMatch("comment_id", "tablename","object_id");
//     $comment_id = intval($p["comment_id"]);
//     $internal_only = intval($w->request('internal_only', 0));

//     $comment = $w->Comment->getComment($comment_id);
//     $is_new = false;
//     if ($comment === null){
//         $comment = new Comment($w);
//         $is_new = true;
//     }

//     $comment->obj_table = $p["tablename"];
//     $comment->obj_id = $p["object_id"];
//     $comment->comment = strip_tags($w->request("comment"));

//     // Only set the internal flag on new comments
//     if ($is_new === true) {
//         $comment->is_internal = $internal_only;
//     }
//     $comment->insertOrUpdate();

//     //handle notifications
//     $top_table_name = $p['tablename'];
//     $top_id = $p['object_id'];
//     if ($top_table_name == 'comment') {
//         $topObject = $w->Comment->getComment($p['object_id'])->getParentObject();
//         $top_table_name = $topObject->getDbTableName();
//         $top_id = $topObject->id;
//     }
//     if($w->request("is_notifications")) {
//         $recipients = [];
//         foreach($_POST as $key=>$value) {
//             //keys of interest are formatted 'recipient_{user_id}'
//             $exp_key = explode('_',$key);
//             if ($exp_key[0] == 'recipient') {
//                 $recipients[] = $exp_key[1];
//             }
//         }
//         $results = $w->callHook('comment', 'send_notification_recipients_' . $top_table_name,['object_id'=>$top_id, 'recipients'=>$recipients, 'commentor_id'=>$w->auth->loggedIn(),'comment'=>$comment, 'is_new'=>$is_new]);


//     }

//     $redirectUrl = $w->request("redirect_url");

//     if (!empty($redirectUrl)){
//         $w->msg("Comment saved", urldecode($redirectUrl));
//     } else {
//         $w->msg("Comment saved", $w->localUrl($_SERVER["REQUEST_URI"]));
//     }
// }
