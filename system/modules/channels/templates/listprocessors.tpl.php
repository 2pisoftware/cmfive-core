<div class="row">
    <div class="col">    
        <?php echo HtmlBootstrap5::box("/channels-processor/edit", "Add Processor", true); ?>
    </div>
</div>
<?php if (!empty($processors)) :
    $table = [["ID", "Name", "Processor Class", "Processor Module", "Attached To", "Actions"]];

    foreach ($processors as $p) {
        $channel = $p->getChannel();
        $line = [
            $p->id,
            StringSanitiser::sanitise($p->name),
            StringSanitiser::sanitise($p->class),
            StringSanitiser::sanitise($p->module),
            !empty($channel->name) ? StringSanitiser::sanitise($channel->name) : "",
        ];
        $line[] = HtmlBootstrap5::buttonGroup(
            HtmlBootstrap5::box("/channels-processor/edit/{$p->id}", "Edit", true, false, null, null, "isbox", null, "btn btn-sm btn-secondary") .
                HtmlBootstrap5::dropdownButton(
                    "More",
                    [
                        HtmlBootstrap5::box(href: "/channels-processor/editsettings/{$p->id}", title: "Settings", button: true, class: "dropdown-item btn btn-sm text-start"),
                        '<hr class="dropdown-divider">',
                        HtmlBootstrap5::box(href: "/channels-processor/delete/{$p->id}", title: "Delete", confirm: "Are you sure you want to delete " . (!empty($p->name) ? StringSanitiser::sanitise($p->name) : "this processor") . "?", class: "dropdown-item btn btn-sm text-start text-danger")
                    ],
                    "btn-info btn btn-sm rounded-0 rounded-end-1"
                )
        );

        $table[] = $line;
    }

    echo HtmlBootstrap5::table($table, null, "tablesorter");
else : ?>
    <p class='pt-3 text-center'>No Processors to list.</p>
<?php endif;