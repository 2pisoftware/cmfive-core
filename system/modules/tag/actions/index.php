<?php

function index_ALL(Web $w) {

	header('Location: /tag/admin');

 //    TagService::getInstance($w)->navigation($w, "Tag Admin");
	// $tags = TagService::getInstance($w)->getTags();
	// $table_header = array("Tag", "Actions");
	
	// $table_data = [];
	// if (!empty($tags)) {
	// 	foreach($tags as $t) {
	// 		$table_data[]  =array(
	// 			$t['tag'],
	// 			Html::b("/tag/edit/".$t['id'],"Edit",false).Html::b("/tag/delete/".$t['id'],"Delete",false)
	// 		);
	// 	}
	// }
	// $w->ctx("tags_table", Html::table($table_data, null, "tablesorter", $table_header));
}