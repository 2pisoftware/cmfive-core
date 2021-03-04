<div class='row-fluid'>
    <div class='small-12 medium-3 columns'>
        <h4>Server OS: <?php echo $server; ?></h4>

        <?php if (!empty($load)) {
            echo '<p>Load: ' . implode(' ', $load) . '</p>';
        } ?>

        <?php echo Html::b('/admin/phpinfo', "phpinfo", null, null, true, 'secondary'); ?>
    </div>
    <div class='small-12 medium-9 columns'>
        <h4>Auditing</h4>
        <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
            <li>
                <div class='panel action_container'>
                    <a id='export_audit_table' style='margin-bottom: 0px;' class='button secondary small expand' href='/admin-maintenance/ajax_exportaudittable' target='_blank'>Export audit table to CSV</a>
                    <p class='text-center' style='margin-bottom: 0px;'><span id='export_audit_table_count'><?php echo $audit_row_count; ?></span> row<?php echo $audit_row_count == 1 ? '' : 's'; ?> in audit table</p>
                    <p class='text-center' style='line-height: 10px;'><small>Exported audit logs will be removed from the database</small></p>
                </div>
            </li>
        </ul>

        <h4>Cache and Index</h4>
        <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
            <li>
                <div class='panel action_container'>
                    <button type='button' id='clear_config' class='button info small expand'>Clear Config cache</button>
                    <p>&nbsp;</p>
                </div>
            </li>
            <?php if (Config::get('file.adapters.local.active') !== true) : ?>
                <li>
                    <div class='panel action_container'>
                        <button type='button' id='clear_config' class='button info small expand'>Clear cached images</button>
                        <p><?php echo !empty($cache_image_count) ? $cache_image_count : 0; ?> images cached</p>
                    </div>
                </li>
            <?php endif; ?>
            <li>
                <div class='panel action_container'>
                    <button type='button' id='reindex_objects' class='button info small expand'>Reindex searchable objects</button>
                    <p class='text-center'><span id='reindex_objects_count'><?php echo $count_indexed; ?></span> searchable objects indexed</p>
                </div>
            </li>
        </ul>
        <h4>Printers</h4>
        <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
            <li>
                <div class='panel action_container'>
                    <a id='backup_database' style='margin-bottom: 0px;' class='button info small expand' href='/admin/printers'>Manage printers</a>
                    <p class='text-center'><?php echo $number_of_printers; ?> printer<?php echo $number_of_printers == 1 ? '' : 's'; ?> found</p>
                </div>
            </li>
            <li>
                <div class='panel action_container'>
                    <a id='backup_database' style='margin-bottom: 0px;' class='button info small expand' href='/admin/printqueue'>View print queue</a>
                    <p class='text-center'>&nbsp;</p>
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    $("#clear_config").click(function() {
        var _this = $(this);
        var _old_text = _this.text();

        _this.addClass('disabled');
        _this.text('Clearing...');
        $.get('/admin/ajaxClearCache', function(response) {
            _this.removeClass('disabled');
            _this.text(_old_text);
        });
    });

    $("#reindex_objects").click(function() {
        var _this = $(this);
        var _old_text = _this.text();

        _this.addClass('disabled');
        _this.text('Reindexing...');
        $.get('/admin-maintenance/ajax_reindexobjects', function(response) {
            $("#reindex_objects_count").text(response);
            _this.removeClass('disabled');
            _this.text(_old_text);
        });
    });

    $('#export_audit_table').click(function() {
        $('#export_audit_table_count').text('0');
    });
</script>