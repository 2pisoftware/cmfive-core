<?php

use Carbon\Carbon; ?>

<?php if (!empty($status)) : ?>
    <h4><?php echo $status; ?></h4>
<?php endif; ?>

<div class="tabs">
    <div class="tab-head">
        <a href="#batch">Batch</a>
        <a href="#individual">Individual</a>
        <a href="#seed">Database Seeds</a>
    </div>
    <div class="tab-body clearfix">
        <div id="batch">
            <div class="row-fluid">
                <?php echo HtmlBootstrap5::b("/admin-migration/rollbackbatch", "Rollback latest batch", "Are you sure you want to rollback migrations?", null, false, "btn btn-sm btn-primary"); ?>
            </div>
            <?php if (!empty($not_installed)) : ?>
                <div class="accordion" id="accordion1">
                    <a href="#batch_available">Not Installed</a>
                    <div id="batch_available" class="content active">
                        <center>
                            <?php
                            echo HtmlBootstrap5::b("/admin-migration/run/all?ignoremessages=false&prevpage=batch", "Install migrations", "Are you sure you want to install migrations?", null, false, "right");
                            ?>
                        </center>
                        <?php
                        $header = ["Name", "Description", "Pre Text", "Post Text"];
                        $data = [];
                        foreach ($not_installed as $module => $_not_installed) {
                            foreach ($_not_installed as $_migration_class) {
                                $migration_path = $_migration_class['path'];
                                if (file_exists(ROOT_PATH . '/' . $migration_path)) {
                                    include_once ROOT_PATH . '/' . $migration_path;
                                    $classname = $_migration_class['class']['class_name'];
                                    if (class_exists($classname)) {
                                        $migration = (new $classname(1))->setWeb($w);
                                        $migration_description = $migration->description();
                                        $migration_preText = $migration->preText();
                                        $migration_postText = $migration->postText();
                                    }
                                    $row = [];
                                    $row[] = $module . ' - ' . $classname;
                                    $row[] = $migration_description;
                                    $row[] = $migration_preText;
                                    $row[] = $migration_postText;
                                    $data[] = $row;
                                }
                            }
                        }

                        $table =  HtmlBootstrap5::table($data, null, "tablesorter", $header);
                        echo $table;
                        ?>
                    </div>
                </div>

            <?php endif;
            if (!empty($batched)) :
                krsort($batched); ?>

                <div class="accordion" id="accordion2">

                    <?php foreach ($batched as $batch_no => $batched_migrations) :
                        $id = "heading" . $batch_no;
                        $control = "collapse" . $batch_no;
                        $target = "#collapse" . $batch_no; ?>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id=<?php echo $id; ?>>
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target=<?php echo $target; ?> aria-expanded="false" aria-controls=<?php echo $control; ?>>
                                    Batch <?php echo $batch_no; ?>
                                </button>
                            </h2>
                            <div id=<?php echo $control; ?> class="accordion-collapse collapse" data-bs-parent="#accordion2" aria-labelledby=<?php echo $id; ?>>
                                <div class="accordion-body">
                                    <?php
                                    $header = ["Name", "Description", "Pre Text", "Post Text"];
                                    $data = [];
                                    foreach ($batched_migrations as $batched_migration) {
                                        $row = [];
                                        $row[] = $batched_migration['module'] . ' - ' . $batched_migration['classname'];
                                        $row[] = $batched_migration['description'];
                                        $row[] = $batched_migration['pretext'];
                                        $row[] = $batched_migration['posttext'];
                                        $data[] = $row;
                                    }
                                    $table =  HtmlBootstrap5::table($data, null, "tablesorter", $header);
                                    echo $table; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>