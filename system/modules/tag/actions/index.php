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
	// 			HtmlBootstrap5::b("/tag/edit/".$t['id'],"Edit",false).HtmlBootstrap5::b("/tag/delete/".$t['id'],"Delete",false)
	// 		);
	// 	}
	// }
	// $w->ctx("tags_table", HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header));
}