<?php

function trash_ALL(Web &$w) {
	InboxService::getInstance($w)->navigation($w, 'Bin');
	$p = $w->pathMatch('num');
	$num = $p['num'] ? $p['num'] : 1;

	$read_del = InboxService::getInstance($w)->getMessages($num - 1, 40, AuthService::getInstance($w)->user()->id, 0, 0, 1);
	//$new_del = InboxService::getInstance($w)->getMessages(0,100,AuthService::getInstance($w)->user()->id,1,0,1);
	$del_count = InboxService::getInstance($w)->getDelMessageCount();

	$table_header = array("<input style='margin: 0px;' type='checkbox' id='allChk' onclick='selectAll()' />", "Subject", "Date", "Sender");
	$table_data = array();
	if (!empty($read_del)) {
		foreach ($read_del as $q) {
			$table_data[] = array(
					"<input style='margin: 0px;' type='checkbox' id='" . $q->id . "' value='" . $q->id . "' class='classChk'/>",
					Html::a(WEBROOT . "/inbox/view/" . $q->id, $q->subject),
					$q->getDate("dt_created", "d/m/Y H:i"),
					($q->sender_id ? $q->getSender()->getFullName() : "")
			);
		}
	}
	$w->ctx("del_table", Html::table($table_data, null, "tablesorter", $table_header));

	$w->ctx('del_count', $del_count);
	$w->ctx('pgnum', $num);
	$w->ctx('readdel', $read_del);
	//$w->ctx('newdel',$new_del);
}
