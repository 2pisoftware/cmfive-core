<?php

	$new = $w->Inbox->getMessages(0,5,$w->Auth->user()->id,1);
	
	if ($new) {
		$newqlines = array(array("Subject","Date","Sender"));
		$total_new_count = 0;
		foreach ($new as $q => $in) {
			$line = array();
			$line[]=Html::a(WEBROOT."/inbox/view/new/".$in->id,"<b>".$in->subject."</b>");;
			$line[]="<b>".$in->getDate("dt_created","d/m/Y H:i")."</b>";
			$line[]="<b>".($in->sender_id ? $in->getSender()->getFullName() : "")."</b>";
			$newqlines[]=$line;
		}
		$inbox = Html::table($newqlines,null,"tablesorter",false);
		}
	else {
		$url = $webroot."/inbox/read";
		$inbox = "<b>No new messages</b>";
	}
	
	return $inbox;
	
?>
