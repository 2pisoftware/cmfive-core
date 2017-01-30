<?php echo Html::box("/report-connections/edit", __("Add a Connection"), true); ?>

<?php echo !empty($connections_table) ? $connections_table : "";
