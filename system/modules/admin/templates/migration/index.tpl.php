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
            <div class="responsive-flex">
                <div style="width: 14rem; margin-right: 1rem">
                    <ul id="batch_list" class="list-group" role="tablist">
                        <?php
                        $active = true;
                        if (!empty($not_installed)) {
                            echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active" id="not-installed-tab" data-bs-toggle="list" href="#not-installed" role="tab" aria-controls="not-installed">Not Installed</li>';
                            $active = false;
                        }

                        if (!empty($batched)) {
                            krsort($batched);

                            foreach ($batched as $batch_no => $batched_migrations) {
                                $id = "batch" . $batch_no . "-tab";
                                $control = "batch" . $batch_no;
                                $target = "#batch" . $batch_no;

                                echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-start' . ($active ? ' active' : '') . '" id="' . $id . '" data-bs-toggle="list" href="' . $target . '" role="tab" aria-controls="' . $control . '">Batch ' . $batch_no . '</li>';
                                $active = false;
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content">
                        <?php
                        $active = true;
                        if (!empty($not_installed)) {
                            echo '<div class="tab-pane active" id="not-installed" role="tabpanel" aria-labelledby="not-installed-tab">';

                            echo HtmlBootstrap5::b("/admin-migration/run/all?ignoremessages=false&prevpage=batch", "Install migrations", "Are you sure you want to install migrations?", null, false, "btn btn-sm btn-primary");
                            echo HtmlBootstrap5::b("/admin-migration/rollbackbatch", "Rollback latest batch", "Are you sure you want to rollback migrations?", null, false, "btn btn-sm btn-danger");


                            $active = false;
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

                            echo HtmlBootstrap5::table($data, null, "table-striped", $header);
                            echo '</div>';
                        }

                        if (!empty($batched)) {
                            krsort($batched);

                            foreach ($batched as $batch_no => $batched_migrations) {
                                $id = "batch" . $batch_no . "-tab";
                                $control = "batch" . $batch_no;
                                $target = "#batch" . $batch_no;

                                echo '<div class="tab-pane' . ($active ? ' active' : '') . '" id="' . $control . '" role="tabpanel" aria-labelledby="' . $id . '">';
                                echo HtmlBootstrap5::b("/admin-migration/rollbackbatch", "Rollback latest batch", "Are you sure you want to rollback migrations?", null, false, "btn btn-sm btn-danger");

                                $active = false;
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
                                echo HtmlBootstrap5::table($data, null, "table-striped", $header);
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="individual">
            <?php if (!empty($available)) : ?>
                <div class="responsive-flex">
                    <div style="width: 14rem; margin-right: 1rem">
                        <ul id="migrations_list" class="list-group" role="tablist">
                            <?php
                            $active = true;
                            foreach ($available as $module => $available_in_module) {
                                $id = $module . "-tab";
                                $target = "#" . $module;

                                echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-start' . ($active ? ' active' : '') . '" id="' . $id . '" data-bs-toggle="list" href="' . $target . '" role="tab" aria-controls="' . $module . '">' . ucfirst($module);
                                $active = false;

                                // installed and non-installed migrations badges
                                $installed_count = is_array($installed[$module]) ? count($installed[$module]) : 0;
                                echo '<span class="right" role="status" aria-label="installation status">';
                                echo $installed_count > 0 ? '<span class="badge bg-success rounded-pill" aria-label="installed">' . $installed_count . '</span>' : '';
                                echo (count($available_in_module) - $installed_count) > 0 ? '<span class="badge bg-warning rounded-pill" style="margin-left: 5px" aria-label="not installed">' . (count($available_in_module) - $installed_count) . '</span>' : '';
                                echo '</span>';

                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-9">
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

                                    echo HtmlBootstrap5::table($data, null, "table-striped width-20", $header);
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
                <div class="responsive-flex">
                    <div style="width: 14rem; margin-right: 1rem">
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
                                echo '<span class="right" role="status" aria-label="installation status">';
                                echo $seed_status_counts[$module][0] > 0 ? '<span class="badge bg-success rounded-pill" aria-label="installed">' . $seed_status_counts[$module][0] . '</span>' : '';
                                echo $seed_status_counts[$module][1] > 0 ? '<span class="badge bg-warning rounded-pill" style="margin-left: 5px"  aria-label="not installed">' . $seed_status_counts[$module][1] . '</span>' : '';
                                echo '</span>';

                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-9">
                        <div class="tab-content">
                            <script>
                                // set default module selection for seed creation modal based on currently selected module in seeds_list
                                document.addEventListener('DOMContentLoaded', function() {
                                    var create_seed = document.getElementById('create-seed');
                                    var seeds_list = document.getElementById('seeds_list');

                                    function setDefaultSelectedModule() {
                                        var active = seeds_list.querySelector('.active');
                                        var module_name = active.innerHTML.split('<')[0].toLowerCase();

                                        var url = create_seed.dataset.modalTarget;
                                        var selection_value = /(?<=default-selection=)[^&]+/;

                                        create_seed.dataset.modalTarget = url.match(selection_value) ?
                                            url.replace(selection_value, module_name) :
                                            url + `${url.includes('?') ? '&' : '?'}default-selection=${module_name}`;
                                    }

                                    // set default selected module on page load
                                    setDefaultSelectedModule();

                                    // add setDefaultSelectedModule to seeds_list click event
                                    // when seeds_list module selection changes, update create_seed's default-selection request param
                                    seeds_list.addEventListener("click", setDefaultSelectedModule);
                                });
                            </script>
                            <?php
                            echo HtmlBootstrap5::box('/admin-migration/createseed', 'Create a seed', true, false, null, null, null, "create-seed", "btn btn-sm btn-primary");

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

                                echo HtmlBootstrap5::table($data, null, "table-striped center-3rd-column", $header);
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