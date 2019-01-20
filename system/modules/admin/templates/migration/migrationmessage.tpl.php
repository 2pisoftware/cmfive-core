<?php
if (!empty($migration_filename) && !empty($migration_module) && !empty($migration_preText))
{
    echo $migration_preText;
    echo Html::b("/admin-migration#batch", "Cancel Migration", "Are you sure you would like to cancel?", null, null);
    echo Html::b("/admin-migration/run/". $migration_module . "/" . $migration_filename . "?ignoremessages=" . "true", "Continue Migration", "Are you sure you would like to continue?", null, null);
} else {
    $w->error("Not all migration fields specified", "/admin-migration#batch");
} ?>