<div class='row-fluid'>
    <div class="card d-inline-block">
        <div class="card-body d-flex align-items-center responsive-flex">
            <p class="me-3 mb-0">Server OS: <?php echo $server; ?></p>

            <?php if (!empty($load)) {
                echo '<p class="me-3 mb-0">Load: ' . implode(' ', $load) . '</p>';
            } ?>

            <?php echo HtmlBootstrap5::b('/admin/phpinfo', "phpinfo", null, null, true, 'btn-sm btn-primary align-self-center'); ?>
        </div>
    </div>
    <div class='small-12 medium-9 responsive-flex pt-3'>
        <div class="card me-3 text-center" style="width: 18rem">
            <div class="card-body d-inline-flex flex-column">
                <h4>Auditing</h4>
                <a id='export_audit_table' style='margin-bottom: 0px;' class='button secondary small' href='/admin-maintenance/ajax_exportaudittable' target='_blank'>Export audit table to CSV</a>
                <p class='text-center' style='margin-bottom: 0px;'><span id='export_audit_table_count'><?php echo $audit_row_count; ?></span> row<?php echo $audit_row_count == 1 ? '' : 's'; ?> in audit table</p>
                <p class='text-center' style='line-height: 10px;'><small>Exported audit logs will be removed from the database</small></p>
            </div>
        </div>

        <div class="card me-3 text-center" style="width: 18rem">
            <div class="card-body d-inline-flex flex-column pt-3">
                <h4>Cache and Index</h4>
                <button type="button" id='clear_config' class='button info small' onclick="clearConfig()">Clear Config</button>
                <?php if (Config::get('file.adapters.local.active') !== true) : ?>
                    <div class='panel action_container'>
                        <button type="button" id='clear_config' class='button info small' onclick="clearConfig()">Clear cached images</button>
                        <p><?php echo !empty($cache_image_count) ? $cache_image_count : 0; ?> images cached</p>
                    </div>
                <?php endif; ?>
                <button type="button" id='reindex_objects' class='button info small' onclick="reindexObjects()">Reindex searchable objects</button>
                <p class='text-center'><span id='reindex_objects_count'><?php echo $count_indexed; ?></span> searchable objects indexed</p>
            </div>
        </div>

        <div class="card me-3 text-center" style="width: 18rem">
            <div class="card-body d-inline-flex flex-column">
                <h4>Printers</h4>
                <a id='backup_database' style='margin-bottom: 0px;' class='button info small' href='/admin/printers'>Manage printers</a>
                <p class='text-center'><?php echo $number_of_printers; ?> printer<?php echo $number_of_printers == 1 ? '' : 's'; ?> found</p>
                <a id='backup_database' style='margin-bottom: 0px;' class='button info small' href='/admin/printqueue'>View print queue</a>
            </div>
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