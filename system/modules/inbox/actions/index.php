<?php
function index_GET(Web &$w) {
    $w->Inbox->navigation($w,"New Messages");

    // Get current page number
    $p = $w->pathMatch('num');
    $num = !empty($p['num']) ? $p['num'] : 1;

    // Get count of messages and the messages for current page
    $new_total = $w->Inbox->getNewMessageCount();
    $new = $w->Inbox->getMessages($num-1,40,$w->Auth->user()->id,1);

    // Make new message table
    $header = array("<input style='margin: 0px;' type='checkbox' id='allChk' onclick='selectAll()' />", "Subject", "Date", "Sender");
    $data = array();
    foreach ($new as $q => $in) {
        $data[] = array(
            "<input style='margin: 0px;' type='checkbox' id='".$in->id."' value='".$in->id."' class='classChk'/>",
            Html::a(WEBROOT."/inbox/view/new/".$in->id,"<b>".$in->subject."</b>"),
            $in->getDate("dt_created","d/m/Y H:i"),
            ($in->sender_id ? $in->getSender()->getFullName() : "")
        );
    }

    $w->ctx('new_table', Html::table($data,null,"tablesorter",$header));
    $w->ctx('pgnum',$num);
    $w->ctx("newtotal",$new_total);
    $w->ctx("new",$new);
}