<div class="row panel">
    <div class="col-12 col-sm-9">
        <div class="mt-2 text-break">Description: <?php echo StringSanitiser::sanitise($form->description); ?></div>
    </div>
    <div class="col-12 col-sm-3">
        <?php echo HtmlBootstrap5::b(href: "/form/export/" . $form->id, title: "Export", class: 'float-end btn-secondary'); ?>
    </div>
</div>

<div class="tabs mt-4">
    <div class="tab-head">
        <a href="#fields">Fields</a>
        <a href="#preview">Preview</a>
        <a href="#mapping">Mapping</a>
        <a href="#row_template">Row Templates</a>
        <a href="#summary_template">Summary Template</a>
        <a href="#events">Events</a>
    </div>
    <div class="tab-body">
        <div id="fields">
            <?php echo HtmlBootstrap5::box(href: "/form-field/edit/?form_id=" . $form->id, title: "Add a field", button: true, class: 'btn btn-primary mb-2'); ?>

            <?php if (!empty($fields)) : ?>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="3%"></th>
                            <th>Name</th>
                            <th>Technical Name</th>
                            <th>Type</th>
                            <th>Additional Details</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody data-sortable data-on-sort="handleDrop">
                        <?php foreach ($fields as $field) : ?>
                            <tr id="field_<?php echo $field->id; ?>" style="height: 40px;">
                                <td><i class="bi bi-grip-vertical"></i></td>
                                <td><?php echo StringSanitiser::sanitise($field->name); ?></td>
                                <td><?php echo StringSanitiser::sanitise($field->technical_name); ?></td>
                                <td><?php echo $field->getReadableType(); ?></td>
                                <td><?php echo $field->getAdditionalDetails(); ?></td>
                                <td>
                                    <?php
                                    echo HtmlBootstrap5::box(href: "/form-field/edit/" . $field->id . "?form_id=" . $form->id, title: "Edit", button: true, class: "btn btn-sm btn-secondary");
                                    echo HtmlBootstrap5::b(href: "/form-field/delete/" . $field->id, title: "Delete", class: "btn btn-sm btn-danger", confirm: "Are you sure you want to delete this form field? (WARNING: there may be existing data saved to this form field!)");
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <script>
                    var handleDrop = async function(e) {
                        // Get new ordering and update via ajax
                        var ordering = [];
                        const rows = document.querySelectorAll("#fields tbody tr");
                        for (let i = 0; i < rows.length; i++) {
                            var id_split = rows.item(i).id.split("_");
                            var id = id_split[1];

                            ordering.push(id);
                        }

                        await fetch("/form-field/move/<?php echo $form->id; ?>", {
                            method: "POST",
                            body: JSON.stringify({ ordering: ordering }),
                        });
                    };
                </script>
            <?php endif; ?>
        </div>
        <div id="preview">
            <div class="row">
                <?php echo HtmlBootstrap5::multiColForm(FormService::getInstance($w)->buildForm(new FormInstance($w), $form), "/form/show/" . $form->id . "?preview=1"); ?>
            </div>
        </div>
        <div id="mapping">
            <div class="row">
                <form action="/form-mapping/edit/?form_id=<?php echo $form->id; ?>" method="POST">
                    <div class="row">
                        <?php
                        $mapping_names = Config::get('form.mapping');
                        if (!empty($mapping_names)) {
                            foreach ($mapping_names as $mapping_name) {
                                $mapping = FormService::getInstance($w)->getFormMapping($form, $mapping_name);
                                $type = empty($mapping) ? "none" : $mapping->getMappingType();
                                ?>
                                <div class="col-sm-12 col-md-3">
                                    <div class="card m-2">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $mapping_name; ?></h5>
                                            <div class="form-check">
                                                <?php echo HtmlBootstrap5::radio(strtolower($mapping_name) . "_none", $mapping_name, $type, "none", "form-check-input"); ?>
                                                <label class="form-check-label" for="<?php echo strtolower($mapping_name); ?>_none">None</label>
                                            </div>
                                            <div class="form-check">
                                                <?php echo HtmlBootstrap5::radio(strtolower($mapping_name) . "_single", $mapping_name, $type, "single", "form-check-input"); ?>
                                                <label class="form-check-label" for="<?php echo strtolower($mapping_name); ?>_single">Single</label>
                                            </div>
                                            <div class="form-check">
                                                <?php echo HtmlBootstrap5::radio(strtolower($mapping_name) . "_multiple", $mapping_name, $type, "multiple", "form-check-input"); ?>
                                                <label class="form-check-label" for="<?php echo strtolower($mapping_name); ?>_multiple">Multiple</label>
                                            </div>
                                            <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-12 m-2">
                            <button id="form_mapping_save" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="row_template" class="clearfix">
            <?php
            echo HtmlBootstrap5::multiColForm([
                "Row templates" => [
                    [new \Html\Cmfive\CodeMirrorEditor([
                        "id|name" => "header_template",
                        "label" => "Header row template",
                        "value" => $form->header_template,
                    ])],
                    [new \Html\Cmfive\CodeMirrorEditor([
                        "id|name" => "row_template",
                        "label" => "Item row template",
                        "value" => $form->row_template,
                    ])]
                    // [["Header row template", "textarea", "header_template", $form->header_template, null, "4", "codemirror"]],
                    // [["Item row template", "textarea", "row_template", $form->row_template, null, "6", "codemirror"]]
                ]
            ], "/form/edit/" . $form->id . "?redirect_url=" . urlencode("/form/show/" . $form->id) . "#row_template", "POST");
            ?>
        </div>
        <div id="summary_template" class="clearfix">
            <?php
            echo HtmlBootstrap5::multiColForm([
                "Summary template" => [
                    [new \Html\Cmfive\CodeMirrorEditor([
                        "id|name" => "Summary_template",
                        "value" => $form->summary_template,
                    ])]
                    // [["", "textarea", "summary_template", $form->summary_template, null, "4", "codemirror"]],
                ]
            ], "/form/edit/" . $form->id . "?redirect_url=" . urlencode("/form/show/" . $form->id) . "#summary_template", "POST");
            ?>
        </div>
        <div id="events">
            <h3>Events</h3>
            <div class="row">
                <div class="col">
                    <?php echo HtmlBootstrap5::box(href: '/form-event/edit?form_id=' . $form->id, title: 'Add New Event', button: true, class: 'btn btn-primary'); ?>
                </div>
            </div>

            <?php if (!empty($event_table)) {
                echo $event_table;
            } ?>
        </div>
    </div>
</div>