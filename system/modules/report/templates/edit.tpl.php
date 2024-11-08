<?php

use Html\Form\Html5Autocomplete; ?>

<div class="tabs">
    <div class="tab-head">
        <a href="#report"><?php echo !empty($report->id) ? "Edit" : "Create"; ?> Report</a>
        <?php if (!empty($report->id)) : ?>
            <a href="#code">SQL</a>
            <a href="#templates">Templates</a>
            <a href="#members">Members</a>
        <?php endif; ?>
        <a href="#database">View Database</a>
    </div>
    <div class="tab-body">
        <div id="report" class="clearfix">
            <?php echo ($btnrun ?? "") . ($duplicate_button ?? "") . $report_form; ?>
        </div>
        <?php if (!empty($report->id)) : ?>
            <div id="code" class="clearfix">
                <?php echo $btnrun . $sql_form; ?>
            </div>
            <div id="templates">
                <p>You can add special templates to render the data. Create a <a href="/admin-templates">System Template in Admin</a> and set the
                    module to <b>report</b>, it can then be selected here.</p>
                <p>The template processer uses the Twig language, you can find more information about this on
                    the <a href="">Twig Website</a>.</p>
                <p>A good first step when creating a new template, is to look at the data. You can use the following
                    twig statement in your template to do this:</p>
                <pre>{{dump(data)}}</pre>
                <p></p>
                <?php echo HtmlBootstrap5::box("/report-templates/edit/{$report->id}", "Add Template", true, false, null, null, "isbox", null, "btn btn-primary"); ?>
                <?php echo !empty($templates_table) ? $templates_table : ""; ?>
            </div>
            <div id="members" class="clearfix">
                <?php echo HtmlBootstrap5::box("/report/addmembers/" . $report->id, " Add New Members ", true, false, null, null, "isbox", null, "btn btn-primary") ?>
                <?php echo $viewmembers; ?>
            </div>
        <?php endif; ?>

        <div id="database" class="clearfix">
            <div>
                <h3>Special Parameters</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <tr>
                            <td>{{current_user_id}}</td>
                            <td>User</td>
                        </tr>
                        <tr>
                            <td>{{roles}}</td>
                            <td>Roles</td>
                        </tr>
                        <tr>
                            <td>{{webroot}}</td>
                            <td>Site URL</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <h3>View Database</h3>
                <label>Tables</label>
                <?php

                echo new Html5Autocomplete([
                    "id|name" => "dbtables",
                    "class" => "form-select",
                    "options" => ReportService::getInstance($w)->getAllDBTables(),
                    "maxItems" => 1,
                ]);
                ?>

                <div id="dbfields"></div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    var categories = <?php echo json_encode($category_config ?? []); ?>

    // $('#module').change(function() {
    //     var option_string = '<option value="">-- Select --</option>';

    //     if (categories.hasOwnProperty($(this).val().toLowerCase())) {
    //         var _categories = categories[$(this).val().toLowerCase()];

    //         Object.keys(_categories).forEach(function(key) {
    //             option_string += '<option value="' + key + '">' + _categories[key] + '</option>';
    //         });

    //         $("#category").removeAttr('disabled');
    //     } else {
    //         $("#category").attr('disabled', 'disabled');
    //     }

    //     $("#category").html(option_string);
    // });

    const report_url = "/report/taskAjaxSelectbyTable?id=";
    document.getElementById("dbtables").addEventListener("change", async (e) => {
        const value = e.target.value;
        const res = await fetch(`${report_url}${value}`);
        const body = await res.text();

        document.getElementById("dbfields").innerHTML = body.replaceAll("\"", "").replaceAll("\\", "");
    });
</script>