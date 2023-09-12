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
                <?php echo HtmlBootstrap5::b("/admin-migration/rollbackbatch", "Rollback latest batch", "Are you sure you want to rollback migrations?", null, false, "btn btn-sm btn-danger"); ?>
            </div>
            <div class="accordion" id="accordion1">

                <?php if (!empty($not_installed)) :
                    $id = "heading" . $batch_no;
                    $control = "collapse" . $batch_no;
                    $target = "#collapse" . $batch_no; ?>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id=<?php echo $id; ?>>
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target=<?php echo $target; ?> aria-expanded="false" aria-controls=<?php echo $control; ?>>
                                Not Installed
                            </button>
                        </h2>
                        <div id=<?php echo $control; ?> class="accordion-collapse collapse" data-bs-parent="#accordion1" aria-labelledby=<?php echo $id; ?>>
                            <div class="accordion-body">

                                <?php
                                echo HtmlBootstrap5::b("/admin-migration/run/all?ignoremessages=false&prevpage=batch", "Install migrations", "Are you sure you want to install migrations?", null, false, "btn btn-sm btn-primary");
                                ?>
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
                    </div>
                <?php endif; ?>
                <?php if (!empty($batched)) :
                    krsort($batched); ?>

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
                            <div id=<?php echo $control; ?> class="accordion-collapse collapse" data-bs-parent="#accordion1" aria-labelledby=<?php echo $id; ?>>
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

        <div id="individual">
            <?php if (!empty($available)) : ?>
                <div class="row">
                    <div class="col-2">
                        <ul id="migrations_list" class="list-group" role="tablist">
                            <?php
                            $active = true;
                            foreach ($available as $module => $available_in_module) {
                                $id = $module . "-tab";
                                $target = "#" . $module;

                                echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-start' . ($active ? ' active' : '') . '" id="' . $id . '" data-bs-toggle="list" href="' . $target . '" role="tab" aria-controls="' . $module . '">' . ucfirst($module);
                                $active = false;

                                // installed and non-installed migrations badges
                                echo '<div class="right">';
                                echo is_array($installed[$module]) && count($installed[$module]) > 0 ? '<span class="badge bg-success rounded-pill">' . count($installed[$module]) . '</span>' : '';
                                echo is_array($installed[$module]) && (count($available_in_module) - count($installed[$module])) > 0 ? '<span class="badge bg-warning rounded-pill">' . (count($available_in_module) - count($installed[$module])) . '</span>' : '';
                                echo '</div>';

                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-10">
                        <div class="tab-content">
                            <?php
                            $active = true;
                            foreach ($available as $module => $available_in_module) {
                                $labelledby = $module . "-tab";
                                echo '<div class="tab-pane' . ($active ? ' active' : '') . '" id="' . $module . '" role="tabpanel" aria-labelledby="' . $labelledby . '">';
                                $active = false;

                                echo HtmlBootstrap5::box("/admin-migration/create/" . $module, "Create a" . (in_array($module[0], ['a', 'e', 'i', 'o', 'u']) ? 'n' : '') . ' ' . $module . " migration", true, false, null, null, null, null, "btn btn-sm btn-primary");

                                if (count($available[$module]) > 0) {
                                    echo HtmlBootstrap5::b("/admin-migration/run/" . $module . "?ignoremessages=false&prevpage=individual", "Run all " . $module . " migrations", "Are you sure you want to run all outstanding migrations for this module?", null, false, "btn btn-sm btn-primary");
                                    $header = ["Name", "Description", "Date run", "Pre Text", "Post Text", "Actions"];
                                    $data = [];
                                    foreach ($available_in_module as $a_migration_path => $migration_data) {
                                        $row = [];
                                        $row[] = $migration_data['class_name'];
                                        $row[] = $migration_data['description'];
                                        $row[] = MigrationService::getInstance($w)->isInstalled($migration_data['class_name']) ? "<span data-tooltip aria-haspopup='true' title='" . @formatDate(MigrationService::getInstance($w)->getMigrationByClassname($migration_data['class_name'])->dt_created, "d-M-Y \a\\t H:i") . "'>Run " . Carbon::createFromTimeStamp(MigrationService::getInstance($w)->getMigrationByClassname($migration_data['class_name'])->dt_created)->diffForHumans() . " by " . (!empty(MigrationService::getInstance($w)->getMigrationByClassname($migration_data['class_name'])->creator_id) && !empty(AuthService::getInstance($w)->getUser(MigrationService::getInstance($w)->getMigrationByClassname($migration_data['class_name'])->creator_id)) ? AuthService::getInstance($w)->getUser(MigrationService::getInstance($w)->getMigrationByClassname($migration_data['class_name'])->creator_id)->getContact()->getFullName() : "System") . "</span>" : "";
                                        $row[] = $migration_data['pretext'];
                                        $row[] = $migration_data['posttext'];

                                        if (MigrationService::getInstance($w)->isInstalled($migration_data['class_name'])) {
                                            $row[] = HtmlBootstrap5::b('/admin-migration/rollback/' . $module . '/' . basename($a_migration_path, ".php"), "Rollback to here", "Are you 110% sure you want to rollback a migration? DATA COULD BE LOST PERMANENTLY!", null, false, "btn btn-sm btn-danger");
                                        } else {
                                            $row[] = HtmlBootstrap5::b('/admin-migration/run/' . $module . '/' . basename($a_migration_path, ".php") . "?ignoremessages=false&prevpage=individual", "Migrate to here", "Are you sure you want to run a migration?", null, false, "btn btn-sm btn-primary");
                                        }

                                        $data[] = $row;
                                    }

                                    echo HtmlBootstrap5::table($data, null, "table width-20", $header);
                                }

                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <h4>There are no migrations on this project</h4>
            <?php endif; ?>
        </div>
        <div id='seed'>
            <?php if (!empty($seeds)) : ?>
                <div class="row">
                    <div class="col-2">
                        <ul id="seeds_list" class="list-group" role="tablist">
                            <?php
                            // installed and not-installed seed counts for each module to be displayed as badges
                            $seed_status_counts = [];
                            foreach ($seeds as $module => $available_seeds) {
                                $installed = 0;
                                $not_installed = 0;

                                foreach ($available_seeds as $seed => $classname) {
                                    if (is_file($seed)) {
                                        require_once($seed);

                                        if (class_exists($classname)) {
                                            $seed_obj = new $classname($w);

                                            if (!empty($seed_obj)) {
                                                $migration_exists = MigrationService::getInstance($w)->migrationSeedExists($classname);

                                                if ($migration_exists) {
                                                    $installed++;
                                                } else {
                                                    $not_installed++;
                                                }
                                            }
                                        }
                                    }
                                }

                                $seed_status_counts[$module] = [$installed, $not_installed];
                            }

                            $active = true;
                            foreach ($seeds as $module => $available_seeds) {
                                $id = $module . "-tab-seed";
                                $target = "#" . $module . "-seed";
                                $seedmodule = $module . "-seed";

                                echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-start' . ($active ? ' active' : '') . '" id="' . $id . '" data-bs-toggle="list" href="' . $target . '" role="tab" aria-controls="' . $seedmodule . '">' . ucfirst($module);
                                $active = false;

                                // installed and non-installed migrations badges
                                echo '<div class="right">';
                                echo $seed_status_counts[$module][0] > 0 ? '<span class="badge bg-success rounded-pill">' . $seed_status_counts[$module][0] . '</span>' : '';
                                echo $seed_status_counts[$module][1] > 0 ? '<span class="badge bg-warning rounded-pill">' . $seed_status_counts[$module][1] . '</span>' : '';
                                echo '</div>';

                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-10">
                        <div class="tab-content">
                            <?php echo HtmlBootstrap5::box('/admin-migration/createseed', 'Create a seed', true, false, null, null, null, null, "btn btn-sm btn-primary"); ?>
                            <?php
                            $active = true;
                            foreach ($seeds as $module => $available_seeds) {
                                $id = $module . "-tab-seed";
                                $seedmodule = $module . "-seed";

                                echo '<div class="tab-pane' . ($active ? ' active' : '') . '" id="' . $seedmodule . '" role="tabpanel" aria-labelledby="' . $id . '">';
                                $active = false;

                                $header = ["Name", "Description", "Status", "Action"];
                                $data = [];

                                foreach ($available_seeds as $seed => $classname) {
                                    if (is_file($seed)) {
                                        require_once($seed);

                                        $seed_obj = null;
                                        if (class_exists($classname)) {
                                            $seed_obj = new $classname($w);
                                        }
                                    }

                                    if (!empty($seed_obj)) {
                                        $migration_exists = MigrationService::getInstance($w)->migrationSeedExists($classname);
                                        $row = [];
                                        $row[] = $seed_obj->name;
                                        $row[] = $seed_obj->description;
                                        $row[] = $migration_exists ? "<span class='btn btn-success btn-sm'>Installed</span>" : "<span class='btn btn-warning btn-sm'>Not installed</span>";
                                        $row[] = !$migration_exists ? HtmlBootstrap5::b('/admin-migration/installseed?url=' . urlencode($seed), "Install", null, null, false, "btn btn-sm btn-primary") : '';
                                        $data[] = $row;
                                    }
                                }

                                echo HtmlBootstrap5::table($data, null, "table", $header);
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>