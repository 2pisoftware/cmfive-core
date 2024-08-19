<div class="row panel">
    <div class="col-12 col-sm-9">
        <div class="mt-2">Description: <?php echo $form->description; ?></div>
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
            <?php echo HtmlBootstrap5::box(href: "/form-field/edit/?form_id=" . $form->id, title: "Add a field", button: true, class: 'btn-primary'); ?>

            <?php if (!empty($fields)) : ?>
                <table class="tablesorter">
                    <thead>
                        <tr>
                            <th width="5%">Ordering</th>
                            <th>Name</th>
                            <th>Technical Name</th>
                            <th>Type</th>
                            <th>Additional Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody data-sortable data-on-sort="handleDrop">
                        <?php foreach ($fields as $field) : ?>
                            <tr id="field_<?php echo $field->id; ?>">
                                <td><i class="bi bi-grip-vertical"></i></td>
                                <td><?php echo $field->name; ?></td>
                                <td><?php echo $field->technical_name; ?></td>
                                <td><?php echo $field->getReadableType(); ?></td>
                                <td><?php echo $field->getAdditionalDetails(); ?></td>
                                <td>
                                    <?php
                                    echo HtmlBootstrap5::box(href: "/form-field/edit/" . $field->id . "?form_id=" . $form->id, title: "Edit", button: true);
                                    echo HtmlBootstrap5::b(href: "/form-field/delete/" . $field->id, title: "Delete", confirm: "Are you sure you want to delete this form field? (WARNING: there may be existing data saved to this form field!)", class: "btn-danger");
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

                        document.getElementsByClassName("#fields tbody tr").foreach(function(index, element) {
                            var id_split = this.attr("id").split("_");
                            var id = id_split[1];

                            ordering.push(id);
                        });

                        await fetch("/form-field/move/<?php echo $form->id; ?>", {
                            method: "POST",
                            data: JSON.stringify({ ordering }),
                        });
                    };
                </script>
            <?php endif; ?>
        </div>
        <div id="preview">
            <div class="row-fluid clearfix">
                <?php echo HtmlBootstrap5::multiColForm(FormService::getInstance($w)->buildForm(new FormInstance($w), $form), "/form/show/" . $form->id . "?preview=1"); ?>
            </div>
        </div>
        <div id="mapping">
            <div class="row-fluid clearfix">
                <form action="/form-mapping/edit/?form_id=<?php echo $form->id; ?>" method="POST">
                    <div class="row-fluid clearfix">
                        <div class="small-12 columns">
                            <?php
                            $mapping_names = Config::get('form.mapping');
                            if (!empty($mapping_names)) {
                                foreach ($mapping_names as $mapping_name) {
                                    $mapping = FormService::getInstance($w)->getFormMapping($form, $mapping_name);
                                    $type = empty($mapping) ? "none" : $mapping->getMappingType();

                                    echo "<h3>$mapping_name</h3>";
                                    echo "<label>" . HtmlBootstrap5::radio(strtolower($mapping_name) . "_none", $mapping_name, $type, "none") . " None</label>";
                                    echo "<label>" . HtmlBootstrap5::radio(strtolower($mapping_name) . "_single", $mapping_name, $type, "single") . " Single</label>";
                                    echo "<label>" . HtmlBootstrap5::radio(strtolower($mapping_name) . "_multiple", $mapping_name, $type, "multiple") . " Multiple</label>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row-fluid clearfix">
                        <div class="small-12 columns">
                            <button id="form_mapping_save" class="button">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="row_template" class="clearfix">
            <?php
            echo HtmlBootstrap5::multiColForm([
                "Row templates" => [
                    [["Header row template", "textarea", "header_template", $form->header_template, null, "4", "codemirror"]],
                    [["Item row template", "textarea", "row_template", $form->row_template, null, "6", "codemirror"]]
                ]
            ], "/form/edit/" . $form->id . "?redirect_url=" . urlencode("/form/show/" . $form->id) . "#row_template", "POST");
            ?>
        </div>
        <div id="summary_template" class="clearfix">
            <?php
            echo HtmlBootstrap5::multiColForm([
                "Summary template" => [
                    [["", "textarea", "summary_template", $form->summary_template, null, "4", "codemirror"]],
                ]
            ], "/form/edit/" . $form->id . "?redirect_url=" . urlencode("/form/show/" . $form->id) . "#summary_template", "POST");
            ?>
        </div>
        <div id="events">
            <?php echo HtmlBootstrap5::box('/form-event/edit?form_id=' . $form->id, 'Add New Event', true); ?>

            <?php if (isset($event_table)) : ?>
                <h4>Events</h4>
                <?php echo $event_table; ?>
            <?php endif; ?>
        </div>
    </div>
</div>