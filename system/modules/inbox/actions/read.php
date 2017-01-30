<?php
function read_GET(Web $w){
	$w->Inbox->navigation($w,__("Read Messages"));
	$p = $w->pathMatch('num');
	$num = $p['num'];
	$num ? $num : $num = 1;
	$read = $w->Inbox->getMessages($num-1,40,$w->Auth->user()->id,0);
	$read_total = $w->Inbox->getReadMessageCount($w->Auth->user()->id);
        
        $table_header = array("<input style='margin: 0px;' type='checkbox' id='allChk' onclick='selectAll()' />",__("Subject"),__("Date"),__("Sender"));
        $table_data = array();
	foreach ($read as $q => $in) {
            $table_data[] = array(
                "<input style='margin: 0px;' type='checkbox' id='".$in->id."' value='".$in->id."' class='classChk'/>",
                Html::a(WEBROOT."/inbox/view/read/".$in->id,$in->subject),
                $in->getDate("dt_created","d/m/Y H:i"),
                ($in->sender_id ? $in->getSender()->getFullName() : "")  
            );
	}
	$w->ctx("read_table", Html::table($table_data, null, "tablesorter", $table_header));
        
	$w->ctx('pgnum',$num);
	$w->ctx("readtotal",$read_total);
	$w->ctx("read",$read);
}
