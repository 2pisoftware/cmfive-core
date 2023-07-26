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
                    <div class="col-3">
                        <ul id="migrations_list" class="nav flex-column nav-pills" role="tablist">
                            <?php foreach ($available as $module => $available_in_module) :
                                $id = $module . "-tab";
                                $target = "#" . $module; ?>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id=<?php echo $id; ?> data-bs-toggle="tab" data-bs-target=<?php echo $target; ?> type="button" role="tab" aria-controls=<?php echo $module; ?> aria-selected="false">
                                        <div class="row">
                                            <div class="col">
                                                <?php echo ucfirst($module); ?>
                                            </div>
                                            <div class="col">
                                                <?php
                                                echo is_array($installed[$module]) && count($installed[$module]) > 0 ? "<span class='btn btn-primary btn-sm' style='font-size: 14pt;'>" . count($installed[$module]) . "</span>" : ""; ?>
                                            </div>
                                            <div class="col">
                                                <?php
                                                echo is_array($installed[$module]) && (count($available_in_module) - count($installed[$module])) > 0 ? "<span class='btn btn-warning btn-sm' style='font-size: 14pt;'>" . (count($available_in_module) - count($installed[$module])) . "</span>" : ""; ?>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-9">
                        <div class="tab-content">
                                <?php foreach ($available as $module => $available_in_module) :
                                    $labelledby = $module . "-tab"; ?>
                                    <div class="tab-pane" id=<?php echo $module; ?> role="tabpanel" aria-labelledby=<?php echo $id; ?>>
                                        <?php echo HtmlBootstrap5::box("/admin-migration/create/" . $module, "Create a" . (in_array($module[0], ['a', 'e', 'i', 'o', 'u']) ? 'n' : '') . ' ' . $module . " migration", true, false, null, null, null, null, "btn btn-sm btn-primary"); ?>
                                        <?php if (count($available[$module]) > 0) : ?>
                                            <?php echo HtmlBootstrap5::b("/admin-migration/run/" . $module . "?ignoremessages=false&prevage=individual", "Run all " . $module . " migrations", "Are you sure you want to run all outstanding migrations for this module?", null, false, "btn btn-sm btn-primary"); ?>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Path</th>
                                                        <th>Date run</th>
                                                        <th>Pre Text</th>
                                                        <th>Post Text</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($available_in_module as $a_migration_path => $migration_data) : ?>
                                                        <tr <?php echo (MigrationService::getInstance($w)->isInstalled($migration_data['class_name'])) ? 'style="background-color: #43CD80;"' : ''; ?>>
                                                            <td><?php echo $migration_data['class_name']; ?></td>
                                                            <td>
                                                                <?php
                                                                echo $migration_data['description'];
                                                                ?>
                                                            </td>
                                                            <td><?php echo $a_migration_path; ?></td>
                                                            <td>
                                                                <?php if (MigrationService::getInstance($w)->isInstalled($migration_data['class_name'])) :
                                                                    $installedMigration = MigrationService::getInstance($w)->getMigrationByClassname($migration_data['class_name']); ?>
                                                                    <span data-tooltip aria-haspopup="true" title="<?php echo @formatDate($installedMigration->dt_created, "d-M-Y \a\\t H:i"); ?>">
                                                                        Run <?php echo Carbon::createFromTimeStamp($installedMigration->dt_created)->diffForHumans(); ?> by <?php echo !empty($installedMigration->creator_id) && !empty(AuthService::getInstance($w)->getUser($installedMigration->creator_id)) ? AuthService::getInstance($w)->getUser($installedMigration->creator_id)->getContact()->getFullName() : "System"; ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php

                                                                echo $migration_data['pretext'];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $migration_data['posttext'];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $filename = basename($a_migration_path, ".php");
                                                                if (MigrationService::getInstance($w)->isInstalled($migration_data['class_name'])) {
                                                                    echo HtmlBootstrap5::b('/admin-migration/rollback/' . $module . '/' . $filename, "Rollback to here", "Are you 110% sure you want to rollback a migration? DATA COULD BE LOST PERMANENTLY!", null, false, "btn btn-sm btn-danger");
                                                                } else {
                                                                    echo HtmlBootstrap5::b('/admin-migration/run/' . $module . '/' . $filename . "?ignoremessages=false&prevpage=individual", "Migrate to here", "Are you sure you want to run a migration?", null, false, "btn btn-sm btn-primary");
                                                                }
                                                                ?>
                                                            </td>

                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                    </div>
                </div>
            <?php else : ?>
                <h4>There are no migrations on this project</h4>
            <?php endif; ?>
        </div>
        <div id='seed'>
            <?php echo HtmlBootstrap5::box('/admin-migration/createseed', 'Create a seed', true, false, null, null, null, null, "btn btn-sm btn-primary"); ?>
            <?php if (!empty($seeds)) : ?>
                <ul class="tabs" data-tab>
                    <?php foreach ($seeds as $module => $available_seeds) : ?>
                        <?php if (count($available_seeds) > 0) : ?>
                            <div class="tab-title <?php echo key($seeds) == $module ? 'active' : '' ?>">
                                <a href="#<?php echo $module; ?>"><?php echo ucfirst($module); ?></a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="tabs-content">
                    <?php foreach ($seeds as $module => $available_seeds) : ?>
                        <div class="content <?php echo key($seeds) == $module ? 'active' : '' ?>" id="<?php echo $module; ?>">
                            <table class='small-12 columns'>
                                <thead>
                                    <tr>
                                        <td>Name</td>
                                        <td>Description</td>
                                        <td>Status</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($available_seeds as $seed => $classname) : ?>
                                        <?php if (is_file($seed)) {
                                            require_once($seed);

                                            $seed_obj = null;
                                            if (class_exists($classname)) {
                                                $seed_obj = new $classname($w);
                                            }
                                        }
                                        if (!empty($seed_obj)) :
                                            $migration_exists = MigrationService::getInstance($w)->migrationSeedExists($classname); ?>
                                            <tr>
                                                <td><?php echo $seed_obj->name; ?></td>
                                                <td><?php echo $seed_obj->description; ?></td>
                                                <td>
                                                    <?php if ($migration_exists) : ?>
                                                        <span class='label success'>Installed</span>
                                                    <?php else : ?>
                                                        <span class='label secondary'>Not installed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo !$migration_exists ? HtmlBootstrap5::b('/admin-migration/installseed?url=' . urlencode($seed), "Install", null, null, false, "btn btn-sm btn-primary") : ''; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>