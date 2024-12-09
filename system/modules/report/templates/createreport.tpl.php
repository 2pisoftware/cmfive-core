<?php use Html\Form\Html5Autocomplete; ?>

<div class="tabs">
    <div class="tab-head">
        <a href="#tab-1">Create Report</a>
        <a href="#tab-2">View Database</a>
    </div>
    <div class="tab-body">
        <div id="tab-1">
            <p>Please review the <b>Help</b> file for full instructions on the special syntax used to create reports.</p>
            <div class="clearfix">
                <?php echo $createreport; ?>
            </div>
        </div>
        <div id="tab-2">
            <div class="clearfix">
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
                    <?php echo new Html5Autocomplete([
                        "id|name" => "dbtables",
                        "class" => "form-select",
                        "options" => ReportService::getInstance($w)->getAllDBTables(),
                        "maxItems" => 1,
                    ]); ?>

                    <div id="dbfields"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const report_url = "/report/taskAjaxSelectbyTable?id=";
    document.getElementById("dbtables").addEventListener("change", async (e) => {
        const value = e.target.value;
        const res = await fetch(`${report_url}${value}`);
        const body = await res.text();

        document.getElementById("dbfields").innerHTML = body.replaceAll("\"", "").replaceAll("\\", "");
    });
</script>