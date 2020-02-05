<?php

function admin_ALL(Web $w) {

	$w->Tag->navigation($w, "Tag Admin");
	$tags = $w->Tag->getTags();
	$table_header = ["Tag", "# Assigned", "Actions"];

	$table_data = [];
	if (!empty($tags)) {
		foreach ($tags as $tag) {
			$table_data[] = array(
				$tag->tag,
				$tag->countAssignedObjects(),
				Html::b("/tag/edit/" . $tag->id, "Edit", false) . Html::b("/tag/delete/" . $tag->id, "Delete", "Are you sure you want to delete the {$tag->tag} tag?", null, false, 'warning')
			);
		}
	}
	
	$w->ctx("tags_table", Html::table($table_data, null, "tablesorter", $table_header));
	
}
