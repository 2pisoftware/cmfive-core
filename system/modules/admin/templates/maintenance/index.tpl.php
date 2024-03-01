<div class="container">
    <div class="row col card">
        <div class="card-body d-flex align-items-center">
            <p class="me-3 mb-0">Server OS: <?php echo $server; ?></p>

            <?php if (!empty($load)) {
                echo '<p  class="me-3 mb-0">Load: ' . implode(' ', $load) . '</p>';
            } ?>

            <?php echo HtmlBootstrap5::b('/admin/phpinfo', "phpinfo", null, null, true, 'btn-sm btn-primary align-self-center'); ?>
        </div>
    </div>
    <div class='row pt-3'>
        <div class="card col-12 col-md-6 col-lg-3 text-center">
            <div class="card-body d-inline-flex flex-column">
                <h4>Auditing</h4>
                <a id='export_audit_table' style='margin-bottom: 0px;' class='btn btn-primary btn-sm' href='/admin-maintenance/ajax_exportaudittable' target='_blank'>Export audit table to CSV</a>
                <p class='text-center' style='margin-bottom: 0px;'><span id='export_audit_table_count'><?php echo $audit_row_count; ?></span> row<?php echo $audit_row_count == 1 ? '' : 's'; ?> in audit table</p>
                <p class='text-center' style='line-height: 10px;'><small>Exported audit logs will be removed from the database</small></p>
            </div>
        </div>

        <div class="card col-12 col-md-6 col-lg-3 text-center">
            <div class="card-body d-inline-flex flex-column">
                <h4>Cache</h4>
                <button type="button" id='clear_config' class='btn btn-primary btn-sm' onclick="clearConfig()">Clear Config</button>
                <?php if (Config::get('file.adapters.local.active') !== true) : ?>
                    <div class='panel action_container'>
                        <button type="button" id='clear_config' class='btn btn-primary btn-sm' onclick="clearConfig()">Clear cached images</button>
                        <p><?php echo !empty($cache_image_count) ? $cache_image_count : 0; ?> images cached</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card col-12 col-md-6 col-lg-3 text-center">
            <div class="card-body d-inline-flex flex-column">
                <h4>Index</h4>
                <button type="button" id='reindex_objects' class='btn btn-primary btn-sm' onclick="reindexObjects()">Reindex searchable objects</button>
                <p class='text-center'><span id='reindex_objects_count'><?php echo $count_indexed; ?></span> searchable objects indexed</p>
            </div>
        </div>

        <div class="card col-12 col-md-6 col-lg-3 text-center">
            <div class="card-body d-inline-flex flex-column">
                <h4>Printers</h4>
                <a id='backup_database' style='margin-bottom: 0px;' class='btn btn-primary btn-sm' href='/admin/printers'>Manage printers</a>
                <p class='text-center'><?php echo $number_of_printers; ?> printer<?php echo $number_of_printers == 1 ? '' : 's'; ?> found</p>
                <a id='backup_database' style='margin-bottom: 0px;' class='btn btn-primary btn-sm' href='/admin/printqueue'>View print queue</a>
            </div>
        </div>
    </div>
    <div class="row pt-3">
        <div class="col-lg-6 col-12">
            <h3 class="mt-4 mb-0">Role Cleanup</h3>
            <p>Found <?php echo count($unused_roles); ?> unused role<?php echo count($unused_roles) == 1 ? '' : 's'; ?></p>
            <?php if ($unused_roles) : ?>
                <?php echo HtmlBootstrap5::b(
                    href: '/admin-maintenance/purgeunusedroles',
                    title: "Purge unused roles",
                    class: 'm-0 mb-3 btn btn-sm btn-primary'); ?>
                <table class="tablesorter">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unused_roles as $role) : ?>
                            <tr>
                                <td><?php echo $role['role']; ?></td>
                                <td><?php echo $role['user']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    function clearConfig() {
        var _this = document.getElementById("clear_config");
        var _old_text = _this.textContent;

        _this.disabled = true;
        _this.textContent = 'Clearing...';

        const xhttp = new XMLHttpRequest();
        xhttp.open("GET", "/admin/ajaxClearCache");
        xhttp.send();

        _this.disabled = false;
        _this.textContent = _old_text;
    }

    function reindexObjects() {
        var _this = document.getElementById("reindex_objects");
        var _old_text = _this.textContent;

        _this.disabled = true;
        _this.textContent = 'Reindexing...';

        const xhttp = new XMLHttpRequest();
        xhttp.open("GET", "/admin-maintenance/ajax_reindexobjects");
        xhttp.send();

        _this.disabled = false;
        _this.textContent = _old_text;
    }

</script>