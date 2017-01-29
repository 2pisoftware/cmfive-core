<?php

$p = $w->pathMatch("id");
$messageid = $p['id'];


if ($messageid){
	$message = $w->Inbox->getMessage($messageid);
	$parent_id = $message->parent_message_id;
	//print $parent_id;
	if ($parent_id){
		print "<div class='tab-body' style='float:right; width: 500px; margin-right:200px; margin-bottom: 20px; padding: 10px;'>";
		print "<b><u> Previous Messages </u></b><br/><hr/>";
		$counter = 1;
		print "<div style='padding:3px; background-color: ".$bgcolor."';'> Message sent by: <i>" . $w->Auth->getUser($message->sender_id)->getFullname() . "</i>  on: <i>" . $message->getDate("dt_created","d/m/Y H:i") . "</i><br/>";
		print $message->getMessage();
		print "</div>";
		while (!$parent_id == 0 || !$parent_id == null){
			if ($counter % 2 != 0){
				$bgcolor = "#ddd";
			} else {
				$bgcolor = "white";
			}
			$parent_message = $w->Inbox->getMessage($parent_id);
			print "<div style='padding:3px; background-color: ".$bgcolor."';'> Message sent by: <i>" . $w->Auth->getUser($parent_message->sender_id)->getFullname() . "</i>  on: <i>" . $parent_message->getDate("dt_created","d/m/Y H:i") . "</i><br/>";
			print $parent_message->getMessage();
			print "</div>";
			$parent_id = $parent_message->parent_message_id ? $parent_message->parent_message_id : null;
			$counter++;
		}
		print "</div>";
	}
	//	print_r($message);
	$lines =  array(
	array("","section"),
	array("To","autocomplete","receiver_id",$message->sender_id,$w->Auth->getUsers()),
	array("Subject","text","subject",'Re:'.$message->subject),
	array("","section"),
	array("","textarea","message",null,120,10),
	);
	print Html::form($lines,WEBROOT."/inbox/send/".$messageid,"POST","Send");

	if ($message_arr){
		foreach($message_arr as $mes){
			print_r($mes);
		}
	}
} else {
	$lines =  array(
	array("Send a Message","section"),
	array("To","autocomplete","receiver_id",null,$w->Auth->getUsers()),
	array("Subject","text","subject"),
	array("","textarea","message",null,120,10),
	);
	print Html::form($lines,WEBROOT."/inbox/send","POST","Send");
}

?>
<script type='text/javascript'>
    
    CKEDITOR.replace( 'message' ,
    {
        toolbar : 'Basic'
    });
    
    $('.savebutton').bind('click',function() {
		if ($.trim($('#acp_receiver_id').val()).length==0) {
			alert("You must enter a message recipient");
			return false;
		}
	});
</script>
