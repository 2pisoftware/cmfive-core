<?php

function admin_ALL(Web $w)
{

    TagService::getInstance($w)->navigation($w, "Tag Admin");
    $tags = TagService::getInstance($w)->getTags();
    $table_header = ["Tag", "# Assigned", "Actions"];

    $table_data = [];
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            $table_data[] = [
                $tag->tag,
                $tag->countAssignedObjects(),
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/tag/edit/" . $tag->id, "Edit", false, null, false, "btn btn-sm btn-primary") .
                    HtmlBootstrap5::b("/tag/delete/" . $tag->id, "Delete", "Are you sure you want to delete the {$tag->tag} tag?", null, false, 'btn btn-sm btn-danger')
                )
            ];
        }
    }
    
    $w->ctx("tags_table", HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header));
}
