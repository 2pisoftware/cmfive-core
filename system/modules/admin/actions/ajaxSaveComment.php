<?php

function ajaxSaveComment_POST(Web $w)
{
    $p = $w->pathMatch('parent_id');
    $internal_only = intval(Request::int("internal_only", 0));

    $comment = new Comment($w);
    $comment->obj_table = "comment";
    $comment->obj_id = $p['parent_id'];
    $comment->comment = strip_tags(Request::string('comment'));
    $comment->is_internal = $internal_only;
    $comment->insert();

    //handle comment notifications
    $list = Request::mixed("notification_recipients");
    //var_dump(Request::mixed("notification_recipients"));
    if (!empty($list)) {
        $table = '';
        $obj_id = '';
        $recipients = [];
        foreach ($list as $key) {
            //keys of interest are either 'recipient_{user_id}' or 'parentObject_{classOrTableName}_{object_id}'
            $exp_key = explode('_', $key);
            if ($exp_key[0] == 'recipient') {
                $recipients[] = $exp_key[1];
            } elseif ($exp_key[0] == 'parentObject') {
                $table = $exp_key[1];
                $obj_id = $exp_key[2];
            }
        }
        $results = $w->callHook('comment', 'send_notification_recipients_' . $table, ['object_id' => $obj_id, 'recipients' => $recipients, 'commenter_id' => AuthService::getInstance($w)->loggedIn(), 'comment' => $comment, 'is_new' => true]);
    }


    $w->setLayout(null);

    echo $w->partial("displaycomment", ["object" => $comment, 'redirect' => Request::string('redirect')], "admin");
}
