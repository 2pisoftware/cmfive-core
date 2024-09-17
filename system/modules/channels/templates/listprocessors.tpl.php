<?php
echo Html::box("/channels-processor/edit", "Add Processor", true);

if (!empty($processors)) {
    $table = array(array("ID", "Name", "Processor Class", "Processor Module", "Attached To", "Actions"));

    foreach ($processors as $p) {
        $channel = $p->getChannel();
        $line = [];
        $line[] = $p->id;
        $line[] = $p->name;
        $line[] = $p->class;
        $line[] = $p->module;
        $line[] = !empty($channel->name) ? $channel->name : "";
        $line[] = HtmlBootstrap5::buttonGroup(
            HtmlBootstrap5::box("/channels-processor/edit/{$p->id}", "Edit", true, false, null, null, "isbox", null, "btn-sm btn-secondary") .
                HtmlBootstrap5::dropdownButton(
                    "More",
                    [
                        HtmlBootstrap5::box("/channels-processor/editsettings/{$p->id}", "Settings", true, false, null, null, "isbox", null, "dropdown-item btn-sm text-start"),
                        '<hr class="dropdown-divider">',
                        HtmlBootstrap5::box("/channels-processor/delete/{$p->id}", "Delete", "Are you sure you want to delete " . (!empty($p->name) ? $p->name : "this processor") . "?", null, false, "dropdown-item btn-sm text-start text-danger")
                    ],
                    "btn-info btn btn-sm rounded-0 rounded-end-1"
                )
        );

        $table[] = $line;
    }

    echo HtmlBootstrap5::table($table, null, "tablesorter");
} else {
    echo "<p class='pt-3 text-center'>No Processors to list.</p>";
}