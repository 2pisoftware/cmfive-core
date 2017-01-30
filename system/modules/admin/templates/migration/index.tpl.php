<?php use Carbon\Carbon; ?>

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
				<?php echo Html::b("/admin-migration/rollbackbatch", __("Rollback latest batch"), __("Are you sure you want to rollback migrations?")); ?>
			</div>
			<ul class="accordion" data-accordion>
				<?php if (!empty($not_installed)) : ?>
					<li class="accordion-navigation">
						<a href="#batch_available">Not Installed</a>
						<div id="batch_available" class="content active">
							<?php echo Html::b("/admin-migration/run/all", __("Install migrations"), __("Are you sure you want to install migrations?"), null, false, "right");?>
							<ul>
								<?php foreach($not_installed as $module => $_not_installed) :
									foreach($_not_installed as $file => $classname) : ?>
										<li><?php echo $module . ' - ' . $classname; ?></li>
									<?php endforeach;
								endforeach; ?>
							</ul>
						</div>
					</li>
				<?php endif;
				if (!empty($batched)) : 
					krsort($batched);
					foreach($batched as $batch_no => $batched_migrations) : ?>
						<li class="accordion-navigation">
							<a href="#batch_<?php echo $batch_no; ?>"><?php _e('Batch'); ?> <?php echo $batch_no; ?></a>
							<div id="batch_<?php echo $batch_no; ?>" class="content">
								<ul>
									<?php foreach($batched_migrations as $batched_migration): ?>
										<li><?php echo $batched_migration['module'] . ' - ' . $batched_migration['classname']; ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</li>
					<?php endforeach;
				endif; ?>
			</ul>
		</div>	
		<div id="individual">
			<?php if (!empty($available)) : ?>
				<ul id="migrations_list" class="tabs vertical" style="border: 1px solid #444;" data-tab>
					<?php foreach($available as $module => $available_in_module): ?>
						<li class="tab-title">
							<a href="#<?php echo $module; ?>">
								<?php echo ucfirst($module); ?>
								<div class="right">
									<?php
										echo count(@$installed[$module]) > 0 ? "<span class='label round success' style='font-size: 14pt;'>" . count($installed[$module]) . "</span>" : "";

										echo (count($available_in_module) - count(@$installed[$module]) > 0 ? '<span class="label round warning" style="font-size: 14pt;">' . (count($available_in_module) - count(@$installed[$module])) . '</span>' : '');
									?>
								</div>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="tabs-content">
					<?php foreach($available as $module => $available_in_module) : ?>
						<div class="content" style="padding-top: 0px;" id="<?php echo $module; ?>">
							<?php echo Html::box("/admin-migration/create/" . $module, __("Create a") . (in_array($module{0}, ['a', 'e', 'i' ,'o', 'u']) ? 'n' : '') . ' ' . $module . __(" migration"), true); ?>
							<?php if (count($available[$module]) > 0) : ?>
								<?php echo Html::b("/admin-migration/run/" . $module, __("Run all ") . $module . __(" migrations"), __("Are you sure you want to run all outstanding migrations for this module?")); ?>
								<table>
									<thead>
										<tr><th><?php _e('Name'); ?></th><th><?php _e('Path'); ?></th><th><?php _e('Date run'); ?></th><th><?php _e('Actions'); ?></th></tr>
									</thead>
									<tbody>
										<?php foreach($available_in_module as $a_migration_path => $a_migration_class): ?>
										<tr <?php echo ($w->Migration->isInstalled($a_migration_class)) ? 'style="background-color: #43CD80;"' : ''; ?>>
											<td><?php echo $a_migration_class; ?></td>
											<td><?php echo $a_migration_path; ?></td>
											<td>
												<?php if ($w->Migration->isInstalled($a_migration_class)) :
													$installedMigration = $w->Migration->getMigrationByClassname($a_migration_class); ?>
													<span data-tooltip aria-haspopup="true" title="<?php echo @formatDate($installedMigration->dt_created, "d-M-Y \a\\t H:i"); ?>">
														Run <?php echo Carbon::createFromTimeStamp($installedMigration->dt_created)->diffForHumans(); ?> <?php _e('by'); ?> <?php echo !empty($installedMigration->creator_id) ? $w->Auth->getUser($installedMigration->creator_id)->getContact()->getFullName() : __("System"); ?>
													</span>
												<?php endif; ?> 
											</td>
											<td>
											<?php
												$filename = basename($a_migration_path, ".php");
												if ($w->Migration->isInstalled($a_migration_class)) {
													echo Html::b('/admin-migration/rollback/' . $module . '/' . $filename, __("Rollback to here"), __("Are you 110% sure you want to rollback a migration? DATA COULD BE LOST PERMANENTLY!"), null, false, "warning expand");
												} else {
													echo Html::b('/admin-migration/run/' . $module . '/' . $filename, __("Migrate to here"), __("Are you sure you want to run a migration?"), null, false, "info expand");
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
				<script>
					$(document).ready(function() {
						$("ul#migrations_list li:visible").click(function() {
							window.location.hash = $('a', $(this)).attr('href');
						});

						var tab_item;
						if (window.location.hash) {
							$("ul#migrations_list li:visible").each(function(index, element) {
								if ($('a', element).attr('href') == window.location.hash) {
									$(element).addClass('active');
									tab_item = $(element);
								}
							});
						} else {
							tab_item = $("ul#migrations_list li:visible").first(); //.addClass("active");
							tab_item.addClass("active");
						}

						if (tab_item) {
							var element_href = tab_item.find('a').attr('href');
							$(".tabs-content #" + element_href.substring(1, element_href.length).toLowerCase()).addClass("active");
						}
					});
				</script>
			<?php else: ?>
				<h4><?php _e('There are no migrations on this project'); ?></h4>
			<?php endif; ?>
		</div>
		<div id='seed'>
			<?php echo Html::box('/admin-migration/createseed', 'Create a seed', true); ?>
			<?php if (!empty($seeds)) : ?>
				<ul class="tabs" data-tab>
					<?php foreach($seeds as $module => $available_seeds) : ?>
						<?php if (count($available_seeds) > 0) : ?>
							<li class="tab-title <?php echo key($seeds) == $module ? 'active': '' ?>">
								<a href="#<?php echo $module; ?>"><?php echo ucfirst($module); ?></a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div class="tabs-content">
					<?php foreach($seeds as $module => $available_seeds) : ?>
						<div class="content <?php echo key($seeds) == $module ? 'active': '' ?>" id="<?php echo $module; ?>">
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
									<?php foreach($available_seeds as $seed => $classname) : ?>
										<?php if (is_file($seed)) {
											require_once($seed);

											$seed_obj = null;
											if (class_exists($classname)) {
												$seed_obj = new $classname($w);
											}
										}
										if (!empty($seed_obj)) : 
											$migration_exists = $w->Migration->migrationSeedExists($classname); ?>
											<tr>
												<td><?php echo $seed_obj->name; ?></td>
												<td><?php echo $seed_obj->description; ?></td>
												<td>
													<?php if ($migration_exists) : ?>
														<span class='label success'>Installed</span>
													<?php else: ?>
														<span class='label secondary'>Not installed</span>
													<?php endif; ?>
												</td>
												<td>
													<?php echo !$migration_exists ? Html::b('/admin-migration/installseed?url=' . urlencode($seed), 'Install') : ''; ?>
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
	
