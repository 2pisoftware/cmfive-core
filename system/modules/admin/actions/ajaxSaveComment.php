<?php

function ajaxSaveComment_POST(Web $w) {
    $p = $w->pathMatch('parent_id');
        
    $comment = new Comment($w);
    $comment->obj_table = "comment";
    $comment->obj_id = $p['parent_id'];
    $comment->comment = strip_tags($w->request('comment'));
    $comment->insert();
    
    //handle comment notifications
    $list = $w->request("notification_recipients");
    //var_dump($w->request("notification_recipients"));
    if(!empty($list)) {
        $table = '';
        $obj_id = '';
        $recipients = [];        
        foreach($list as $key) {
            //keys of interest are either 'recipient_{user_id}' or 'parentObject_{classOrTableName}_{object_id}' 
            $exp_key = explode('_',$key);
            if ($exp_key[0] == 'recipient') {
                $recipients[] = $exp_key[1];
            } else if ($exp_key[0] == 'parentObject') {
                $table = $exp_key[1];
                $obj_id = $exp_key[2];
            }         
        }        
        $results = $w->callHook('comment', 'send_notification_recipients_' . $table,['object_id'=>$obj_id, 'recipients'=>$recipients, 'commentor_id'=>$w->auth->loggedIn(),'comment'=>$comment, 'is_new'=>true]);
    
        
    }
    
    
    $w->setLayout(null);
   
    echo $w->partial("displaycomment", array("object" => $comment, 'redirect' => $w->request('redirect')), "admin");
}
