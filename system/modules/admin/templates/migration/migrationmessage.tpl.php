<?php
if (!empty($migration_filename) && !empty($migration_module) && !empty($migration_preText))
{
    echo "<center>";
    echo "<h1><strong>$migration_class</strong></h1>";
    echo $migration_preText;
    echo "<br><br>";
    echo Html::b("/admin-migration#batch", "Cancel Migration", "Are you sure you would like to cancel?", null, null);
    echo Html::b("/admin-migration/run/". $migration_module . "/" . $migration_filename . "?ignoremessages=" . "true", "Continue Migration", "Are you sure you would like to continue?", null, null);
    echo "</center>";
} else {
    $w->error("Not all migration fields specified", "/admin-migration#batch");
} ?>