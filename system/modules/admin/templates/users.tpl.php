<?php echo Html::box($webroot."/admin/useradd/box","Add New User",true); ?>
<br/><br/>
<div class='tabs'>
	<div class='tab-head'>
		<a class='active' href='#internal'>Internal</a>
		<a href='#external'>External</a>
	</div>
	<div class='tab-body'>
		<div id='internal'>
			<?php echo $internal_table; ?>
		</div>
		<div id='external'>
			<?php echo $external_table; ?>
		</div>
	</div>
</div>

echo Html::box($webroot . "/admin/useradd/box", "Add New User", true);
// echo Html::b("/admin-user/invalidate_all_passwords", "Invalidate All Passwords", "Are you sure you want to invalidate all passwords?", null, false, "warning");
echo $table;
