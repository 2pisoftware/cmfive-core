<?php
if (!empty($migration_filename) && !empty($migration_module) && !empty($migration_preText))
{
    
?>
    <center>
    <h1><strong><?php echo $migration_class; ?></strong></h1>
    <div style='word-wrap: break-word; background-color:#f08a24; color:white;border:5px solid black; border-radius:5px; padding-top:50px; padding-bottom:50px; padding-right:50px; padding-left:50px; width:100%; height:100%;'> <?php echo $migration_preText; ?> </div>
    <br><br>
    <?php
        echo HtmlBootstrap5::b("/admin-migration#" . $prevpage, "Cancel Migration", "Are you sure you would like to cancel?", null, null, "warning");
        if ($prevpage == "batch")
        {
            echo HtmlBootstrap5::b("/admin-migration/run/all?continuingrunall=true&prevpage=".$prevpage, "Continue Running All Migrations", "Are you sure you would like to continue?", null, null);
        } else if ($prevpage == "individual")
        {
            echo HtmlBootstrap5::b("/admin-migration/run/" . $_migration_module . "/" . $_migration_filename . "?continuingrunall=true&prevpage=".$prevpage, "Continue Migrations", "Are you sure you would like to continue?", null, null);
        }
     ?>
    </center>
    <?php } else {
    $w->error("Not all migration fields specified", "/admin-migration#batch");
} ?>