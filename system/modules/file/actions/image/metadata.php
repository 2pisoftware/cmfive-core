<?php

/**
 * The image metadata GET action.
 *
 * @param Web $w
 * @return void
 */
function metadata_GET(Web $w)
{
    list($attachment_id) = $w->pathMatch();
    if (empty($attachment_id)) {
        $w->error("Failed to find attachment");
    }

    $attachment = FileService::getInstance($w)->getAttachment($attachment_id);
    if (empty($attachment)) {
        $w->error("Failed to find attachment");
    }

    $table_data = [];
    $exif_data = $attachment->getImageExifData();

    if (!empty($exif_data)) {
        foreach (json_decode($exif_data, true) as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $table_data[] = ["EXIF", "$key - $k", $v];
                }

                continue;
            }

            $table_data[] = ["EXIF", $key, $value];
        }
    }

    // If the table data is empty, add an empty array so the table header still draws.
    if (empty($table_data)) {
        $table_data[] = [];
    }

    $w->ctx("exif_table", Html::table($table_data, null, "tablesorter", ["Type", "Key", "Value"]));
    $w->ctx("attachment", $attachment);
}
